<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductivitySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_description',
        'duration',
        'completed_at',
    ];

    protected $casts = [
        'duration' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
