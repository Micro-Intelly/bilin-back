<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Request;
use Storage;

/**
 * App\Models\Serie
 *
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property string $image
 * @property string $type
 * @property string $access
 * @property string $author_id
 * @property string|null $organization_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Language[] $language
 * @property-read int|null $language_count
 * @property-read \App\Models\User|null $organization
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\SerieFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Serie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Serie query()
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $level
 * @property string $language_id
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Serie whereLevel($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Episode[] $episodes
 * @property-read int|null $episodes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section[] $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $episode_comments
 * @property-read int|null $episode_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\History[] $histories
 * @property-read int|null $histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Test[] $tests
 * @property-read int|null $tests_count
 */
class Serie extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'title',
        'description',
        'access',
        'level',
        'type',
        'organization_id',
        'author_id',
        'language_id',
        'image'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
    public function files(): HasMany
    {
        return $this->hasMany(File::class,'series_id');
    }
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function notes(): HasMany
    {
        return $this->hasMany(Comment::class, 'serie_id');
    }
    public function episode_comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'serie_id');
    }
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
    public function tests(): HasMany
    {
        return $this->hasMany(Test::class, 'series_id');
    }
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class,'series_id')->orderBy('created_at');
    }
    public function episodes(): HasManyThrough
    {
        return $this->hasManyThrough(Episode::class, Section::class,'series_id');
    }
    public function histories(): HasMany
    {
        return $this->hasMany(History::class, 'serie_id');
    }

    public static function validate_permission(Request $request, Model $serie): bool
    {
        $validate = false;
        if($serie->access == 'public' ||
            ($serie->access == 'registered' && $request->user() != null) ||
            ($request->user() != null && $request->user()->can('manage-series')) ||
            ($request->user() != null && $request->user()->id == $serie->author_id)
        )
        {
            $validate = true;
        }
        else if($serie->access == 'org' && $request->user() != null)
        {
            $userOrgs = User::organization_ids($request->user()->id);
            if($userOrgs->contains($serie->organization_id)){
                $validate = true;
            }
        }
        return $validate;
    }

    protected static function boot () {
        parent::boot();

        self::deleting(function($serie) {
            Taggable::where('taggable_id', $serie->id)->delete();
            $serie->sections->each->delete();
            $serie->tests->each->delete();
            $serie->comments->each->delete();
            $serie->notes()->delete();
            $serie->histories()->delete();
            $serie->files->each->delete();
            if($serie->image != 'public/image/application/defaultImage.png'){
                $imageCount = Serie::where('image','=', $serie->image)->count();
                $imagePath = substr($serie->image, 8);
                $imagePath = 'public/'.$imagePath;
                if(Storage::disk('do-spaces')->exists($imagePath) && $imageCount < 2) {
                    Storage::disk('do-spaces')->delete($imagePath);
                }
            }
        });
    }
}
