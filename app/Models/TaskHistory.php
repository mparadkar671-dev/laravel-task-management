<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskHistory extends Model
{
    // Since we don't need 'updated_at' for history logs, we can disable it
    public $timestamps = false; 

    protected $fillable = [
        'task_id',
        'changed_by',
        'old_status',
        'new_status',
        'changed_at',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}