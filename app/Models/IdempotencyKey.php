<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;

class IdempotencyKey extends Model
{
    use Prunable;

    protected $fillable = [
        'key',
        'response',
        'status',
    ];
    public function prunable(): Builder
    {
        return static::query()->where(
            'created_at',
            '<=',
            now()->subHours(24)
        );
    }
}