<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Service;
use App\Models\LandingSetting;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Services', Service::count())
                ->description('Active services listed')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),

            Stat::make('Site Status', LandingSetting::value('is_maintenance_mode') ? 'Maintenance' : 'Live')
                ->description('Current visibility')
                ->descriptionIcon(LandingSetting::value('is_maintenance_mode') ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color(LandingSetting::value('is_maintenance_mode') ? 'danger' : 'success'),

            Stat::make('System', 'Healthy')
                ->description('All systems operational')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('success'),

            Stat::make('Base64 Endpoints', \App\Models\Base64ApiEndpoint::count())
                ->description(\App\Models\Base64ApiEndpoint::active()->count() . ' active tools')
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('info'),
        ];
    }
}
