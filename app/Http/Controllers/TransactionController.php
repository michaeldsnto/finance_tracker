<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(
                    fn ($query) => $query->where('user_id', auth()->id())
                ),
            ],
        ]);

        $transactions = $user->transactions()
            ->with('category')
            ->when($filters['date_from'] ?? null, fn ($query, $dateFrom) => $query->whereDate('date', '>=', $dateFrom))
            ->when($filters['date_to'] ?? null, fn ($query, $dateTo) => $query->whereDate('date', '<=', $dateTo))
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->orderBy('date', 'desc')
            ->get();

        $categories = $this->userCategories();

        return view('transactions.index', compact('transactions', 'categories', 'filters'));
    }
    public function create()
    {
        $categories = $this->userCategories();

        return view('transactions.create', compact('categories'));
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $validatedData = $this->validateTransactionData($request);

        $user->transactions()->create($validatedData);

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }
    public function edit($id)
    {
        $transaction = $this->findUserTransactionOrFail($id);
        $categories = $this->userCategories();

        return view('transactions.edit', compact('transaction', 'categories'));
    }
    public function update(Request $request, $id)
    {
        $transaction = $this->findUserTransactionOrFail($id);
        $validatedData = $this->validateTransactionData($request);

        $transaction->update($validatedData);

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }
    public function destroy($id)
    {
        $transaction = $this->findUserTransactionOrFail($id);

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
    private function validateTransactionData(Request $request): array
    {
        $validatedData = $request->validate($this->transactionValidationRules());

        if (!$validatedData['category_id']) {
            return $validatedData;
        }

        $category = $this->findUserCategoryOrFail($validatedData['category_id']);

        if ($category->type !== $validatedData['type']) {
            throw ValidationException::withMessages([
                'category_id' => 'The selected category type must match the transaction type.',
            ]);
        }

        return $validatedData;
    }

    private function transactionValidationRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'in:income,expense'],
            'date' => ['required', 'date'],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(
                    fn($query) => $query->where('user_id', auth()->id())
                ),
            ],
        ];
    }

    private function userCategories()
    {
        return auth()->user()->categories()->orderBy('name')->get();
    }

    private function findUserTransactionOrFail(int|string $id): Transaction
    {
        return auth()->user()->transactions()->findOrFail($id);
    }

    private function findUserCategoryOrFail(int|string $id): Category
    {
        return auth()->user()->categories()->findOrFail($id);
    }
}
