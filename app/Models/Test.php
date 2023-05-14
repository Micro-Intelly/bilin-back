<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Http\Request;

/**
 * App\Models\Test
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Language[] $language
 * @property-read int|null $language_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\TestFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Test newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Test newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Test query()
 * @mixin \Eloquent
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $series_id
 * @property string $language_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereUpdatedAt($value)
 * @property string $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereUserId($value)
 */
class Test extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'title',
        'description',
        'series_id',
        'access',
        'level',
        'organization_id',
        'user_id',
        'language_id',
    ];

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class, 'series_id');
    }
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
    public function questions(): HasMany
    {
        return $this->HasMany(Question::class);
    }
    public function results(): HasMany
    {
        return $this->HasMany(Result::class)->orderBy('n_try');
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class)->orderBy('name');
    }
    public function histories(): MorphMany
    {
        return $this->morphMany(History::class, 'history_able');
    }

    public static function validate_permission(Request $request, Test $test): bool
    {
        $validate = false;
        if($test->access == 'public' ||
            ($test->access == 'registered' && $request->user() != null) ||
            ($request->user() != null && $request->user()->can('manage-test')) ||
            ($request->user() != null && $request->user()->id == $test->user_id)
        )
        {
            $validate = true;
        }
        else if($test->access == 'org' && $request->user() != null)
        {
            $userOrgs = User::organization_ids($request->user()->id);
            if($userOrgs->contains($test->organization_id)){
                $validate = true;
            }
        }
        return $validate;
    }

    public static function check_limits(Request $request): bool
    {
        $userOrg = (bool)$request->user()->organization_id;
        if(!$userOrg){
            $userOrg = Org_user::where('user_id', '=', $request->user()->id)->count() > 0;
        }
        $constantKey = $userOrg ? 'constants.limits.test_limit_org' : 'constants.limits.test_limit';
        return Test::where('user_id', '=', $request->user()->id)->count() < config($constantKey);
    }

    protected static function boot () {
        parent::boot();

        self::deleting(function($test) {
            Taggable::where('taggable_id', $test->id)->delete();
            $test->results()->delete();
            $test->comments()->delete();
            $test->histories()->delete();
            $test->questions()->delete();
        });
    }
}
