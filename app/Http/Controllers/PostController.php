<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function testGet($id){
        return ;
    }
    public function testShowV(Request $request)
    {

        $stream = new VideoStreamController(storage_path() . '/app/videos/file_example_MP4_1920_18MG.mp4');
        $stream->start();exit;
/*
        $path = storage_path() . '/app/public/file_example_MP4_1920_18MG.mp4';

//        if(!File::exists($path)) {
//            return response()->json(['message'=>'File not found'], Response::HTTP_NOT_FOUND);
//        }

        $headers = [
            'Content-Type'        => 'video/mp4',
            'Content-Length'      => File::size($path),
            'Content-Disposition' => 'attachment; filename="file_example_MP4_1920_18MG.mp4"'
        ];

        return Response::stream(function() use ($path) {
            $stream = fopen($path, 'r');
            fpassthru($stream);
        }, 200, $headers);
*/
    }
    public function testShowP(Request $request)
    {
        $stream = new VideoStreamController(storage_path() . '/app/podcasts/Free_Test_Data_10MB_MP3.mp3');
        $stream->start();exit;
    }

}
