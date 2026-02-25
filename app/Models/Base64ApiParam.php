<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Base64ApiParam extends Model
{
    protected $table = 'base64_api_params';

    protected $fillable = [
        'endpoint_id',
        'direction',
        'field_key',
        'field_label',
        'field_type',
        'placeholder',
        'helper_text',
        'is_required',
        'default_value',
        'options',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Base64ApiEndpoint::class, 'endpoint_id');
    }

    public function scopeRequest(Builder $query): Builder
    {
        return $query->where('direction', 'request');
    }

    public function scopeResponse(Builder $query): Builder
    {
        return $query->where('direction', 'response');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('field_key');
    }
}
