<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

/**
 * App\Models\File
 *
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\FileFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $name
 * @property string|null $description
 * @property string $path
 * @property string $series_id
 * @method static \Illuminate\Database\Eloquent\Builder|File whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereSeriesId($value)
 */
class File extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'name',
        'description',
        'series_id',
        'path'
    ];

    public static function boot() {
        parent::boot();

        self::deleting(function($file) {
            if($file->path != 'app/files/dummy.pdf'){
                $fileCount = File::where('path','=', $file->path)->count();
    //            $filePath = substr($file->path, 5);
                if(Storage::disk('do-spaces')->exists($file->path) && $fileCount < 2) {
                    Storage::disk('do-spaces')->delete($file->path);
                }
            }
        });
    }
}
