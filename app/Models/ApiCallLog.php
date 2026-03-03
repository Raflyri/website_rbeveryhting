<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ApiCallLog extends Model
{
    protected $fillable = [
        'ip',
        'country',
        'country_code',
        'url',
        'api_endpoint',
        'method',
        'http_status',
        'duration_ms',
        'request_snippet',
        'response_snippet',
        'level',
        'message',
    ];

    protected $casts = [
        'http_status' => 'integer',
        'duration_ms' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeByIp(Builder $query, string $ip): Builder
    {
        return $query->where('ip', 'like', "%{$ip}%");
    }

    public function scopeByCountry(Builder $query, string $country): Builder
    {
        return $query->where('country', 'like', "%{$country}%");
    }

    public function scopeByEndpoint(Builder $query, string $endpoint): Builder
    {
        return $query->where('api_endpoint', 'like', "%{$endpoint}%");
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /** Returns a Tailwind-compatible colour token for the log level */
    public function levelColor(): string
    {
        return match ($this->level) {
            'error'   => 'danger',
            'warning' => 'warning',
            default   => 'success',
        };
    }

    /** Returns a coloured badge for HTTP status */
    public function statusColor(): string
    {
        $code = (int) $this->http_status;
        if ($code >= 500) return 'danger';
        if ($code >= 400) return 'warning';
        if ($code >= 300) return 'info';
        return 'success';
    }
}
