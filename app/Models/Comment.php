<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Comment
 *
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $commentable
 * @method static \Database\Factories\CommentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $commentable_type
 * @property string $commentable_id
 * @property string $body
 * @property string $author_id
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereType($value)
 */
class Comment extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'body',
        'title',
        'description',
        'author_id',
        'in_reply_to_id',
        'root_comm_id',
        'type',
        'serie_id',
        'commentable_id',
        'commentable_type',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
    public function comments(): HasMany
    {
        return $this
            ->hasMany(Comment::class, 'root_comm_id')
            ->orderBy('created_at','desc');
    }
    public function root_comm(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'root_comm_id');
    }
    public function in_reply_to(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'in_reply_to_id');
    }
}
