<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Joke;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use function PHPUnit\Framework\isEmpty;

class CategoryController extends Controller
{
    /**
     * Display a listing of the Categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::all();
        return ApiResponse::success($categories, "Categories retrieved");
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:32'],
        ]);
        $search = $validated['search'];

        if (empty($search) || $search == '') {
            return ApiResponse::error([], "search parameter is empty");
        };

        $categories = Category::where(function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        })
            ->orderBy('title')
            ->withCount('jokes')
            ->get();

        if ($categories->isEmpty()) {
            return ApiResponse::error([], "No results found");
        }

        return ApiResponse::success(['Categories' => $categories, 'resultsCount' => $categories->count()], "Categories retrieved");
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
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
                'min:6', 'max:255',
            ],
        ]);

        $category = Category::create($validated);

        return ApiResponse::success($category, 'Category created', 201);
    }

    /**
     * Display the specified Category.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $category = Category::whereId($id)->withCount('jokes')->get();
//        $category = Category::whereId($id)->get();
        $jokes = $category->jokesByDateAddedDesc()->get();
        if (count($category) === 0) {
            return ApiResponse::error([], "Category not found", 404);
        }
        return ApiResponse::success(
            [
                'category' => $category,
                'jokesInCategory' => $jokes
            ], "Category retrieved"
        );
    }

    /**
     * Update the specified Category in storage.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'title' => [
                    'nullable', 'sometimes',
                    'string', 'min:4', 'max:32',
                    'unique:categories,title',
                    // ^ Rule::unique('categories')->ignore($category->id) -?
                ],
                'description' => [
                    'nullable', 'sometimes',
                    'string', 'min:6', 'max:255',
                ],
            ]);

            $category = Category::find($id);

            if (!$category) {
                return ApiResponse::error([], "Category not found", 404);
            }

            $category->update($validated);

            return ApiResponse::success($category, 'Category updated');
        } catch (Throwable $e) {
            return ApiResponse::error(
                ['exception' => $e->getMessage()],
                'An unexpected error occurred',
            );
        }
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error([], "Category not found", 404);
        }
        $category->delete();
        return ApiResponse::success($category, "Category <$id> moved to trash");
    }

    /**
     * Show all soft deleted Categories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function trash(Request $request)
    {
        $trashed = Category::onlyTrashed()->get();

        return ApiResponse::success($trashed, "Trashed category retrieved");
    }

    /**
     * Recover all soft deleted categories from trash
     *
     * @return JsonResponse
     */
    public function recoverAll()
    {
        Category::onlyTrashed()->restore();

        return ApiResponse::success([], "all categories restored successfully");
    }

    /**
     * Remove all soft deleted categories from trash
     *
     * @return JsonResponse
     */
    public function removeAll()
    {
        Category::onlyTrashed()->forceDelete();

        return ApiResponse::success([], "all categories permanently deleted");
    }

    /**
     * Recover specified soft deleted category from trash
     *
     * @param string $id
     * @return JsonResponse
     */
    public function recoverOne(string $id)
    {
        $category = Category::onlyTrashed()->find($id);

        if (!$category) {
            return ApiResponse::error([], "Category not found in trash");
        }

        $category->restore();

        return ApiResponse::success([], "Category restored successfully");
    }

    /**
     * Remove specified soft deleted category from trash
     *
     * @param string $id
     * @return JsonResponse
     */
    public function removeOne(string $id)
    {
        $category = Category::onlyTrashed()->find($id);

        if (!$category) {
            return ApiResponse::error([], "Category not found in trash");
        }

        $category->forceDelete();

        return ApiResponse::success([], "Category permanently deleted");
    }
}
