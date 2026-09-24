<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
    ];

    /**
     * Relationship: The task this comment belongs to.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Relationship: The author of this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
