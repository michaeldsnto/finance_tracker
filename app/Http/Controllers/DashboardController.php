<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $totalIncome = $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        $recentTransactions = $user->transactions()->with('category')->orderBy('date', 'desc')->take(5)->get();
        $expenseChartData = $this->expenseChartData();

        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'balance',
            'recentTransactions',
            'expenseChartData'
        ));
    }

    private function expenseChartData(): array
    {
        $startMonth = now()->startOfMonth()->subMonths(5);
        $months = collect(range(0, 5))->map(
            fn (int $offset) => $startMonth->copy()->addMonths($offset)
        );

        $expensesByMonth = auth()->user()
            ->transactions()
            ->where('type', 'expense')
            ->whereDate('date', '>=', $startMonth->toDateString())
            ->get()
            ->groupBy(fn ($transaction) => Carbon::parse($transaction->date)->format('Y-m'));

        return [
            'labels' => $months->map(fn (Carbon $month) => $month->format('M Y'))->values()->all(),
            'values' => $months->map(
                fn (Carbon $month) => (float) $expensesByMonth->get($month->format('Y-m'), collect())->sum('amount')
            )->values()->all(),
        ];
    }
}
