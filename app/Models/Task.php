<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_id', 'task_id', 'request_id', 'problem_id', 'change_id', 'title', 'scheduled_start_at_src', 'actual_end_at_src'
    ];

    protected $casts = [
        'scheduled_start_at_src' => 'datetime',
        'actual_end_at_src' => 'datetime',
    ];

    public function upload(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }
}
