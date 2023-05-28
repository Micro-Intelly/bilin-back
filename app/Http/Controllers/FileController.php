<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\File;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Models\Serie;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreFileRequest $request
     * @param Serie $serie
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreFileRequest $request, Serie $serie): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'description' => 'max:500|nullable',
            'path' => 'required'
        ]);
        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id))
        {
            $data = [
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'series_id' => $serie->id,
                'path' => $request->get('path')
            ];
            File::create($data);
            return response()->json(['status' => 200, 'message' => 'Created']);
        } else {
            abort(401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File $file
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(File $file): JsonResponse
    {
        if(! Storage::disk('do-spaces')->exists($file->path)) {
            abort(404);
        }
//        return response()->file(Storage::disk('do-spaces')->get($file->path));
        return response()->json(['status'=>200, 'message'=>Storage::disk('do-spaces')->temporaryUrl($file->path, now()->addMinutes(10))]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Serie $serie
     * @param \App\Models\File $file
     * @return JsonResponse
     */
    public function destroy(Request $request, Serie $serie, File $file): JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id) &&
            $file->series_id === $serie->id)
        {
            try {
                $file->delete();
                return response()->json(['status' => 200, 'message' => 'Deleted']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }


    /**
     * Handles the file upload
     *
     * @param FileReceiver $receiver
     * @return \Illuminate\Http\JsonResponse
     * @throws UploadMissingFileException
     */
    public function uploadFile(FileReceiver $receiver): JsonResponse
    {
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need
            return $this->saveFile($save->getFile());
        }

        // we are in chunk mode, lets send the current progress
        $handler = $save->handler();
        return response()->json([
            "done" => $handler->getPercentageDone()
        ]);
    }

    /**
     * Saves the file
     *
     * @param UploadedFile $file
     * @return \Illuminate\Http\JsonResponse
     */
    protected function saveFile(UploadedFile $file): JsonResponse
    {
        $fileName = $this->createFilename($file);
        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());
        // Group files by the date (week
        $dateFolder = date("Y-m-W");

        // Build the file path
        $path = 'media';
        if(str_starts_with($mime,'audio')){
            $path = 'podcasts';
        } else if(str_starts_with($mime,'video')){
            $path = 'videos';
        } else if(str_starts_with($mime,'application')){
            $path = 'files';
        }
//        $filePath = "upload/{$mime}/{$dateFolder}/";
        $filePath = "app/".$path;
        $finalPath = storage_path($filePath);

        // move the file name
        $remotePath = Storage::disk('do-spaces')->put($filePath, $file,'private');
        $file->move($finalPath, $fileName);
        Storage::disk('local')->delete('files/'.$fileName);

        return response()->json([
            'path' => $remotePath,
            'name' => $fileName,
            'mime_type' => $mime
        ]);
    }

    /**
     * Delete chunks when cancel upload
     *
     * @param Request $request
     * @param string $uniqueId
     * @return \Illuminate\Http\JsonResponse
     */
    protected function cancel_file(Request $request, string $uniqueId): JsonResponse
    {
        try{
            \Illuminate\Support\Facades\File::delete(\Illuminate\Support\Facades\File::glob(storage_path('app/chunks/*-'.$uniqueId.'.*.part')));
            return response()->json(['status' => 200, 'message' => 'Success']);
        } catch (Exception $exception) {
            return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
        }

    }

    /**
     * Delete file when delete uploaded file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function delete_file(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
        ]);
        try{
            $type = $request->get('type');
            $path = 'app/'.$type.'/'.$request->get('name');
            if(Storage::disk('do-spaces')->exists($path)){
                if($type == 'podcasts' || $type == 'videos'){
                    $count = Episode::where('path', $path)->count();
                    if($count > 0){
                        return response()->json(['status' => 400, 'message' => 'There are some video or podcast related to this file, you can not delete id without deleting the episode']);
                    }
                } else if($type == 'files') {
                    $count = File::where('path', $path)->count();
                    if($count > 0){
                        return response()->json(['status' => 400, 'message' => 'There are some file record related to this file, you can not delete id without deleting the episode']);
                    }
                }
                Storage::disk('do-spaces')->delete($path);
                return response()->json(['status' => 200, 'message' => 'Success']);
            } else {
                return response()->json(['status' => 400, 'message' => 'File not exists']);
            }
        } catch (Exception $exception) {
            return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }
}
