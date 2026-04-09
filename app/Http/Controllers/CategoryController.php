<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $categories = $user->categories()->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validatedData = $request->validate($this->categoryValidationRules());

        $user->categories()->create($validatedData);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = $this->findUserCategoryOrFail($id);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = $this->findUserCategoryOrFail($id);
        $validatedData = $request->validate($this->categoryValidationRules());

        $category->update($validatedData);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = $this->findUserCategoryOrFail($id);

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

    private function categoryValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ];
    }

    private function findUserCategoryOrFail(int|string $id): Category
    {
        return auth()->user()->categories()->findOrFail($id);
    }
}
