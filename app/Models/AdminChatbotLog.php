<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminChatbotLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'question',
        'normalized_question',
        'intent',
        'parameters',
        'success',
        'response_summary',
        'response_meta',
        'context_snapshot',
        'latency_ms',
        'feedback',
        'feedback_at',
    ];

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'success' => 'boolean',
            'response_meta' => 'array',
            'context_snapshot' => 'array',
            'feedback_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
