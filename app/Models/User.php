<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Storage;

/**
 * App\Models\User
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $organization_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organization[] $organizations
 * @property-read int|null $organizations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $posts
 * @property-read int|null $posts_count
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOrganizationId($value)
 * @property string $thumbnail
 * @method static \Illuminate\Database\Eloquent\Builder|User whereThumbnail($value)
 * @property-read \App\Models\Organization|null $organization
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Result[] $results
 * @property-read int|null $results_count
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, UuidTrait;

    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'thumbnail'
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function boot () {
        parent::boot();

        self::deleting(function($user) {
            Org_user::where('user_id',$user->id)->delete();
            $user->results()->delete();
            $user->histories()->delete();
            $user->comments->each->delete();
            $user->series->each->delete();
        });

        self::deleted(function($user) {
            if($user->organization_id != null){
                $orgCount = User::where('organization_id','=', $user->organization_id)->count();
                if($orgCount < 1) {
                    Org_user::where('organization_id',$user->organization_id)->delete();
                    Serie::where('organization_id',$user->organization_id)->update([
                        'access'=>'registered', 'organization_id'=>null
                    ]);
                    Test::where('organization_id',$user->organization_id)->update([
                        'access'=>'registered', 'organization_id'=>null
                    ]);
                    $user->organization()->delete();
                }
            }
            if( $user->thumbnail != 'public/image/user/account-thumbnail.png'){
                $imageCount = User::where('thumbnail','=', $user->thumbnail)->count();
    //            $imagePath = substr($user->thumbnail, 8);
    //            $imagePath = 'public/'.$imagePath;
                if(Storage::disk('do-spaces')->exists($user->thumbnail) && $imageCount < 2) {
                    Storage::disk('do-spaces')->delete($user->thumbnail);
                }
            }
        });
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'org_users');
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }
    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }
    public function series(): HasMany
    {
        return $this->hasMany(Serie::class, 'author_id');
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }
    public function series_comments(): HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, Serie::class,'author_id','serie_id');
    }
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public static function organization_ids(string $id): \Illuminate\Support\Collection
    {
        $user = User::where('id',$id)
            ->with('organizations:id')
            ->first();
        $userOrgIds = $user->organizations->pluck('id');
        if($user->organization_id != null){
            $userOrgIds[] = $user->organization_id;
        }
        return $userOrgIds;
    }
}
