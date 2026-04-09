<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transactions') }}
            </h2>

            <a
                href="{{ route('transactions.create') }}"
                class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800"
            >
                Add Transaction
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <form action="{{ route('transactions.index') }}" method="GET" class="space-y-4 p-6">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <x-input-label for="date_from" :value="__('Date From')" />
                            <x-text-input
                                id="date_from"
                                name="date_from"
                                type="date"
                                class="mt-1 block w-full"
                                :value="$filters['date_from'] ?? ''"
                            />
                            <x-input-error :messages="$errors->get('date_from')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="date_to" :value="__('Date To')" />
                            <x-text-input
                                id="date_to"
                                name="date_to"
                                type="date"
                                class="mt-1 block w-full"
                                :value="$filters['date_to'] ?? ''"
                            />
                            <x-input-error :messages="$errors->get('date_to')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select
                                id="category_id"
                                name="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? null) == $category->id)>
                                        {{ $category->name }} ({{ ucfirst($category->type) }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('transactions.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            Reset
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800"
                        >
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                @if ($transactions->isEmpty())
                    <div class="p-6 text-sm text-gray-600">
                        No transactions found for the current filters.
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
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($transactions as $transaction)
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
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                            <div class="flex items-center justify-end gap-3">
                                                <a
                                                    href="{{ route('transactions.edit', $transaction->id) }}"
                                                    class="font-medium text-indigo-600 hover:text-indigo-800"
                                                >
                                                    Edit
                                                </a>

                                                <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="font-medium text-red-600 hover:text-red-800"
                                                        onclick="return confirm('Delete this transaction?')"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
