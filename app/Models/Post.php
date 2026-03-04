<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'excerpt',
        'thumbnail',
        'blocks',
        'author_name',
        'author_bio',
        'author_avatar',
        'reading_time_minutes',
        'is_featured',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'blocks'               => 'array',
        'is_featured'          => 'boolean',
        'is_published'         => 'boolean',
        'published_at'         => 'datetime',
        'reading_time_minutes' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /** Human-readable reading time label */
    public function readingTimeLabel(): string
    {
        $min = $this->reading_time_minutes ?? 1;
        return $min . ' min read';
    }

    /*
    |--------------------------------------------------------------------------
    | Time-Travel Posting: Computed Status
    |--------------------------------------------------------------------------
    |
    | There are four states:
    |   draft      → is_published = false (or published_at blank)
    |   scheduled  → is_published = true AND published_at > now() (future)
    |   published  → is_published = true AND published_at <= now() (live / backdated)
    |
    */

    /**
     * Returns the current posting state: 'draft' | 'scheduled' | 'published'
     */
    public function computedStatus(): string
    {
        if (! $this->is_published) {
            return 'draft';
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return 'scheduled';
        }

        return 'published';
    }

    /**
     * Human-readable label for the status.
     */
    public function statusLabel(): string
    {
        return match ($this->computedStatus()) {
            'draft'     => 'Draft',
            'scheduled' => 'Scheduled',
            'published' => $this->published_at && $this->published_at->isPast() && $this->published_at->diffInHours(now()) > 48
                ? 'Published (Backdated)'
                : 'Published',
        };
    }

    /**
     * Filament badge colour token for the status.
     */
    public function statusColor(): string
    {
        return match ($this->computedStatus()) {
            'draft'     => 'gray',
            'scheduled' => 'warning',
            'published' => 'success',
        };
    }

    /** Returns a Tailwind colour token for the post type */
    public function typeColor(): string
    {
        return match ($this->type) {
            'news'    => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
            'blog'    => 'text-pink-400 bg-pink-400/10 border-pink-400/20',
            default   => 'text-sky-400 bg-sky-400/10 border-sky-400/20', // article
        };
    }

    /** Thumbnail URL or null */
    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    /** Author avatar URL or null */
    public function authorAvatarUrl(): ?string
    {
        return $this->author_avatar ? asset('storage/' . $this->author_avatar) : null;
    }

    /** Auto-generate excerpt from first text block if not set */
    public function resolvedExcerpt(int $chars = 160): string
    {
        if ($this->excerpt) {
            return Str::limit($this->excerpt, $chars);
        }

        foreach ($this->blocks ?? [] as $block) {
            if ($block['type'] === 'text') {
                $text = strip_tags($block['data']['paragraph'] ?? $block['data']['heading'] ?? '');
                if ($text) {
                    return Str::limit($text, $chars);
                }
            }
        }

        return '';
    }
}
