<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','original_name','stored_path','kind','for_month'
    ];

    protected $casts = [
        'for_month' => 'date',
    ];

    protected $appends = ['kind_label'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function tickets(): HasMany { return $this->hasMany(Ticket::class); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }

    public function getKindLabelAttribute(): string
    {
        return match ($this->kind) {
            'resolved' => 'TICKET',
            'actual_end' => 'TASK',
            default => strtoupper($this->kind ?? '-')
        };
    }
}
