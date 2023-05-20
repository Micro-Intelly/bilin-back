<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\History
 *
 * @method static \Database\Factories\HistoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|History newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|History newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|History query()
 * @mixin \Eloquent
 * @property string $id
 * @property string $history_able_type
 * @property string $history_able_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|History whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereHistoryAbleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereHistoryAbleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereUserId($value)
 * @property string|null $serie_id
 * @property-read \App\Models\User $author
 * @property-read Model|\Eloquent $history_able
 * @property-read \App\Models\Serie|null $serie
 * @method static \Illuminate\Database\Eloquent\Builder|History whereSerieId($value)
 */
class History extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'user_id',
        'serie_id',
        'history_able_type',
        'history_able_id'
    ];

    public function history_able(): MorphTo
    {
        return $this->morphTo();
    }
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class,'serie_id','id');
    }
}
