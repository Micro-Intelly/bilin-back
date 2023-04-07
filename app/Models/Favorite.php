<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Favorite
 *
 * @property string $uuid
 * @property string $favorite_able_type
 * @property string $favorite_able_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\FavoriteFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereFavoriteAbleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereFavoriteAbleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUuid($value)
 * @mixin \Eloquent
 * @property string $id
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereId($value)
 */
class Favorite extends Model
{
    use HasFactory, UuidTrait;
}
