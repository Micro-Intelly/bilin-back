<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\BelongsToRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Storage;

/**
 * App\Models\Episode
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\EpisodeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Episode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Episode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Episode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Episode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Episode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Episode whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $name
 * @property string|null $description
 * @property string $path
 * @property string $section_id
 * @method static \Illuminate\Database\Eloquent\Builder|Episode whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Episode whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Episode wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Episode whereSectionId($value)
 */
class Episode extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'title',
        'description',
        'serie_id',
        'path',
        'type',
        'section_id',
        'user_id'
    ];

    public static function boot() {
        parent::boot();

        self::deleting(function($episode) {
            $episodeCount = Episode::where('path','=', $episode->path)->count();
            $episodePath = substr($episode->path, 5);
            if(Storage::disk('local')->exists($episodePath) && $episodeCount < 2) {
                Storage::disk('local')->delete($episodePath);
            }
            $episode->histories()->delete();
            $episode->comments()->delete();
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class, 'serie_id');
    }
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function histories(): MorphMany
    {
        return $this->morphMany(History::class, 'history_able');
    }

    public static function check_limits(Request $request): bool
    {
        $userOrg = (bool)$request->user()->organization_id;
        if(!$userOrg){
            $userOrg = Org_user::where('user_id', '=', $request->user()->id)->count() > 0;
        }
        $constantKey = $userOrg ? 'constants.limits.episode_limit_org' : 'constants.limits.episode_limit';
        return Episode::where('user_id', '=', $request->user()->id)->count() < config($constantKey);
    }
}
