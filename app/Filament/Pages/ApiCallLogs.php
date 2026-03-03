<?php

namespace App\Filament\Pages;

use App\Models\ApiCallLog;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class ApiCallLogs extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'API Call Logs';
    protected static ?string $navigationGroup = 'Tools & Utilities';
    protected static ?int    $navigationSort  = 99;
    protected static ?string $title           = 'API Call Logs';
    protected static string  $view            = 'filament.pages.api-call-logs';

    /*
    |--------------------------------------------------------------------------
    | Filter Properties (Livewire)
    |--------------------------------------------------------------------------
    */

    public string $level      = 'all';
    public string $search     = '';
    public string $ipFilter   = '';
    public string $country    = '';
    public string $endpoint   = '';
    public string $dateFrom   = '';
    public string $dateTo     = '';
    public int    $perPage    = 50;

    /*
    |--------------------------------------------------------------------------
    | Lifecycle hooks — re-query whenever a filter changes
    |--------------------------------------------------------------------------
    */

    public function updatedLevel(): void
    { /* triggers Livewire re-render */
    }
    public function updatedSearch(): void
    { /* triggers Livewire re-render */
    }
    public function updatedIpFilter(): void
    { /* triggers Livewire re-render */
    }
    public function updatedCountry(): void
    { /* triggers Livewire re-render */
    }
    public function updatedEndpoint(): void
    { /* triggers Livewire re-render */
    }
    public function updatedDateFrom(): void
    { /* triggers Livewire re-render */
    }
    public function updatedDateTo(): void
    { /* triggers Livewire re-render */
    }

    /*
    |--------------------------------------------------------------------------
    | Data provided to the Blade view
    |--------------------------------------------------------------------------
    */

    protected function getViewData(): array
    {
        return [
            'logs'     => $this->queryLogs(),
            'stats'    => $this->stats(),
            'endpoints' => ApiCallLog::select('api_endpoint')
                ->whereNotNull('api_endpoint')
                ->distinct()
                ->orderBy('api_endpoint')
                ->pluck('api_endpoint')
                ->all(),
            'countries' => ApiCallLog::select('country')
                ->whereNotNull('country')
                ->distinct()
                ->orderBy('country')
                ->pluck('country')
                ->all(),
        ];
    }

    private function queryLogs(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $q = ApiCallLog::query()->latest();

        if ($this->level !== 'all') {
            $q->where('level', $this->level);
        }

        if ($this->ipFilter !== '') {
            $q->where('ip', 'like', "%{$this->ipFilter}%");
        }

        if ($this->country !== '') {
            $q->where('country', $this->country);
        }

        if ($this->endpoint !== '') {
            $q->where('api_endpoint', $this->endpoint);
        }

        if ($this->search !== '') {
            $q->where(function ($sub) {
                $term = "%{$this->search}%";
                $sub->where('message', 'like', $term)
                    ->orWhere('url', 'like', $term)
                    ->orWhere('ip', 'like', $term)
                    ->orWhere('request_snippet', 'like', $term)
                    ->orWhere('response_snippet', 'like', $term);
            });
        }

        if ($this->dateFrom !== '') {
            $q->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo !== '') {
            $q->whereDate('created_at', '<=', $this->dateTo);
        }

        return $q->paginate($this->perPage);
    }

    private function stats(): array
    {
        return [
            'total'   => ApiCallLog::count(),
            'success' => ApiCallLog::where('level', 'info')->count(),
            'warning' => ApiCallLog::where('level', 'warning')->count(),
            'error'   => ApiCallLog::where('level', 'error')->count(),
        ];
    }

    public function resetFilters(): void
    {
        $this->level    = 'all';
        $this->search   = '';
        $this->ipFilter = '';
        $this->country  = '';
        $this->endpoint = '';
        $this->dateFrom = '';
        $this->dateTo   = '';
    }
}
