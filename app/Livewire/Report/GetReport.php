<?php

namespace App\Livewire\Report;

use App\Models\Cashier;
use Livewire\Component;
use Carbon\Carbon;

class GetReport extends Component
{
    public $startDate;
    public $endDate;
    public $reportType = 'daily'; // daily or monthly
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->selectedMonth = now()->format('Y-m');
    }

    public function getReport()
    {
        if ($this->reportType === 'daily') {
            return Cashier::whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
                ->with('buyer', 'transactions')
                ->get()
                ->map(function ($cashier) {
                    return [
                        'date' => $cashier->created_at->format('d M Y'),
                        'buyer' => $cashier->buyer->name,
                        'total_buy' => $cashier->total_buy,  // Changed from total_sales to total_buy
                        'capital' => $cashier->capital,
                        'profit' => $cashier->total_buy - $cashier->capital,
                        'quantity' => $cashier->quantity
                    ];
                });
        }

        // Monthly report
        return Cashier::whereYear('created_at', Carbon::parse($this->selectedMonth)->year)
            ->whereMonth('created_at', Carbon::parse($this->selectedMonth)->month)
            ->get()
            ->groupBy(function ($cashier) {
                return $cashier->created_at->format('d M Y');
            })
            ->map(function ($group) {
                return [
                    'total_sales' => $group->sum('total_buy'),
                    'total_capital' => $group->sum('capital'),
                    'total_profit' => $group->sum('total_buy') - $group->sum('capital'),
                    'total_transactions' => $group->count(),
                    'total_quantity' => $group->sum('quantity')
                ];
            });
    }

    public function render()
    {
        $report = $this->getReport();

        // Fix summary calculation for daily reports
        $summary = [
            'total_sales' => $this->reportType === 'daily'
                ? $report->sum('total_buy')
                : $report->sum('total_sales'),
            'total_capital' => $this->reportType === 'daily'
                ? $report->sum('capital')
                : $report->sum('total_capital'),
            'total_profit' => $this->reportType === 'daily'
                ? $report->sum('profit')
                : $report->sum('total_profit'),
            'total_transactions' => $report->count(),
            'total_quantity' => $this->reportType === 'daily'
                ? $report->sum('quantity')
                : $report->sum('total_quantity')
        ];

        return view('livewire.report.get-report', [
            'report' => $report,
            'summary' => $summary
        ]);
    }
}
