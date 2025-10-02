<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_id','created_at_src','resolved_at_src','request_type','request_id','subject'
    ];

    protected $casts = [
        'created_at_src' => 'datetime',
        'resolved_at_src' => 'datetime',
    ];

    public function upload(): BelongsTo { return $this->belongsTo(Upload::class); }
}

