<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Language
 *
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $language_able
 * @method static \Database\Factories\LanguageFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $language
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereLanguage($value)
 * @property string $home-img-mini
 * @property string $home-img
 * @property string $home-phrase
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereHomeImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereHomeImgMini($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereHomePhrase($value)
 */
class Language extends Model
{
    use HasFactory, UuidTrait;
}
