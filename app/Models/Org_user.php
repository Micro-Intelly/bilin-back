<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\Org_user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Org_user newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Org_user newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Org_user query()
 * @mixin \Eloquent
 * @property string $user_id
 * @property string $organization_id
 * @method static \Illuminate\Database\Eloquent\Builder|Org_user whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Org_user whereUserId($value)
 */
class Org_user extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'user_id',
        'organization_id'
    ];
}
