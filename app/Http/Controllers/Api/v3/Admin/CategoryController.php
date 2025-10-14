<?php

namespace App\Http\Controllers\Api\v3\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Psy\Util\Json;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Validation
        $validated = $request->validate([
            'perPage' => ['integer', 'nullable'],
            'page' => ['integer', 'nullable'],
            'search' => ['nullable', 'string', 'max:32'],
        ]);

        $perPage = $validated['perPage'] ?? 12;
        $page = $validated['page'] ?? 1;
        $search = $validated['search'] ?? '';

        // Get the categories
        $categories = Category::where('title', 'like', '%' . $search . '%')
            ->orderBy('title')
            ->withCount('jokes')
            ->paginate($perPage, ['*'], 'page', $page);

        $trashCount = Category::onlyTrashed()->count();

        // return view
        return ApiResponse::success(
            ['categories' => $categories, 'trashCount' => $trashCount, 'searchResult' => $search],
            "resources gathered successfully"
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // TODO: something here
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'min:4', 'max:32',
                'unique:categories,title',
            ],
            'description' => [
                'nullable',
                'string',
                'min:16', 'max:255',
            ],
        ]);

        $category = Category::create($validated);

        return ApiResponse::success([], "Category {$category->title} added successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResponse
    {
        $categoryId = $category->id;

        $category = Category::where('id', $categoryId)
            ->withCount('jokes')
            ->first();

        $jokes = $category->jokesByDateAddedDesc()->limit(5)->get();

        return ApiResponse::success(
            [$jokes, $category],
            "resources gathered successfully"
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit')
            ->with('category', $category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'min:4', 'max:32',
                Rule::unique('categories')->ignore($category->id)
            ],
            'description' => [
                'nullable',
                'string',
                'min:16', 'max:255',
            ],
        ]);

        $category->update($validated);

        return ApiResponse::success([], "Category {$category->title} updated successfully");
    }

    /**
     * Verify the removal of the specified category from storage.
     */
    public function delete(Category $category): View
    {
        return view('admin.categories.delete')
            ->with('category', $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return ApiResponse::success([], "Category {$category->title} destroyed successfully");
    }



    public function trash(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'perPage' => ['integer', 'nullable'],
            'page' => ['integer', 'nullable'],
            'search' => ['nullable', 'string', 'max:32',],
        ]);

        $perPage = $validated['perPage'] ?? 12;
        $page = $validated['page'] ?? 1;
        $search = $validated['search'] ?? '';

        $categories = Category::onlyTrashed()
            ->where('title', 'like', '%' . $search . '%')
            ->orderBy('title')
            ->withCount('jokes')
            ->paginate($perPage, ['*'], 'page', $page);

        return ApiResponse::success(
            ['categories' => $categories, 'search' => $search],
            "retrieved categories from trash"
        );
    }


    public function recoverAll(): RedirectResponse
    {
        $categories = Category::onlyTrashed()->get();

        foreach ($categories as $category) {
            $category->restore();
        }

        return redirect()->route('admin.categories.index')
            ->with('success', __('Categories') . ' ' . __('successfully recovered from trash'));
    }


    public function removeAll(): RedirectResponse
    {
        $categories = Category::onlyTrashed()->get();

        foreach ($categories as $category) {
            $category->forceDelete();
        }

        return redirect()
            ->route('admin.categories.trash')
            ->with('success', __('Categories') . ' ' . __('successfully removed from trash'));
    }

    public function recoverOne(string $id): RedirectResponse
    {

        $category = Category::onlyTrashed()->whereId($id)->first();

        if ($category) {
            $category->restore();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', __('Category') . ' ' . __('successfully restored'));
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('Error', __('Category') . ' ' . __('not found'));

    }


    public function removeOne(string $id): RedirectResponse
    {
        $category = Category::onlyTrashed()->whereId($id)->first();

        if ($category) {
            $category->forceDelete();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', __('Category') . ' ' . __('successfully restored'));
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('Error', __('Category') . ' ' . __('not found'));

    }
}
