<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500">Total Balance</p>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">
                            {{ number_format($balance, 2) }}
                        </p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500">Total Income</p>
                        <p class="mt-3 text-3xl font-semibold text-green-700">
                            {{ number_format($totalIncome, 2) }}
                        </p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500">Total Expense</p>
                        <p class="mt-3 text-3xl font-semibold text-red-700">
                            {{ number_format($totalExpense, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Monthly Expense Overview</h3>
                    <p class="mt-1 text-sm text-gray-500">Expense trend for the last 6 months.</p>
                </div>
                <div class="p-6">
                    <div class="h-80">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                </div>

                @if ($recentTransactions->isEmpty())
                    <div class="p-6 text-sm text-gray-600">
                        No transactions yet. Add your first transaction to see activity here.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Title
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Category
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Amount
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($recentTransactions as $transaction)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $transaction->title }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            {{ $transaction->category?->name ?? 'Uncategorized' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-700' : 'text-red-700' }}">
                                            {{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            {{ \Illuminate\Support\Carbon::parse($transaction->date)->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="flex justify-end">
                <a
                    href="{{ route('transactions.index') }}"
                    class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800"
                >
                    View All Transactions
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartElement = document.getElementById('expenseChart');

            if (!chartElement) {
                return;
            }

            new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: @json($expenseChartData['labels']),
                    datasets: [{
                        label: 'Expenses',
                        data: @json($expenseChartData['values']),
                        backgroundColor: 'rgba(239, 68, 68, 0.18)',
                        borderColor: 'rgba(220, 38, 38, 1)',
                        borderWidth: 1.5,
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
