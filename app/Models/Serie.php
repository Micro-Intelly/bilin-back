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
 */
class Serie extends Model
{
    use HasFactory, UuidTrait;

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
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class,'series_id');
    }
    public function episodes(): HasManyThrough
    {
        return $this->hasManyThrough(Episode::class, Section::class,'series_id');
    }
}