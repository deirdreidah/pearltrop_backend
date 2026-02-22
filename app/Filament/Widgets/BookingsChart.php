<?php

namespace App\Filament\Widgets;

use App\Models\CarBooking;
use Filament\Widgets\ChartWidget;

class BookingsChart extends ChartWidget
{
    protected ?string $heading = 'Bookings Trend';

    protected function getData(): array
    {
        $data = CarBooking::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill in missing months
        $months = [];
        $counts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[] = now()->subMonths($i)->format('M');
            $counts[] = $data[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $counts,
                    'fill' => 'start',
                    'borderColor' => '#10b981', // Emerald primary
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
