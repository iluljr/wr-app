<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WRTemplate extends Model
{
    use HasFactory;

    protected $table = 'wr_templates';

    protected $fillable = ['name','settings'];

    protected $casts = [
        'settings' => 'array',
    ];
}
