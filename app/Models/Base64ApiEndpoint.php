<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Base64ApiEndpoint extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'base64_api_endpoints';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'api_url',
        'http_method',
        'icon',
        'category',
        'is_active',
        'sort_order',
    ];

    /**
     * All parameter definitions for this endpoint.
     */
    public function params(): HasMany
    {
        return $this->hasMany(Base64ApiParam::class, 'endpoint_id');
    }

    /**
     * Request parameters, ordered by sort_order.
     */
    public function requestParams(): HasMany
    {
        return $this->params()->request()->ordered();
    }

    /**
     * Response field definitions, ordered by sort_order.
     */
    public function responseParams(): HasMany
    {
        return $this->params()->response()->ordered();
    }

    /**
     * Scope a query to only include active endpoints.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order endpoints by sort_order ASC, then name.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
