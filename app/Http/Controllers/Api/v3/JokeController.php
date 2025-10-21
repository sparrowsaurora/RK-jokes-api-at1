<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Joke;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JokeController extends Controller
{

    /**
     * Display a listing of the Categories.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Joke::with(['user', 'categories'])->get();
        return ApiResponse::success($categories, "Jokes retrieved");
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming data
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:4'],
            'content' => ['required', 'string', 'min:6'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        // Automatically get the user from the Bearer token
        $user = $request->user();

        // Create the joke
        $joke = Joke::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'published_at' => $validated['published_at'] ?? null,
            'user_id' => $user->id,
        ]);

        // Attach to the selected category
        $joke->categories()->attach($validated['category_id']);

        return ApiResponse::success($joke->load('categories'), 'Joke created successfully', 201);
    }


    /**
     * Display the specified Category.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $joke = Joke::with(['user', 'categories'])->find($id);

        if (!$joke) {
            return ApiResponse::error([], "Joke not found", 404);
        }

        return ApiResponse::success($joke, "Joke retrieved");
    }

    //    /**
    //     * Update the specified Category in storage.
    //     *
    //     * @param Request $request
    //     * @param string $id
    //     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'min:4'],
            'content' => ['sometimes', 'string', 'min:6'],
            'published_at' => ['nullable', 'date'],
        ]);

        $joke = Joke::find($id);

        if (!$joke) {
            return ApiResponse::error([], "Joke not found", 404);
        }

        // check user owns joke
        $user = $request->user();
        if ($joke->user_id !== $user->id) {
            return ApiResponse::error([], "You does not own the joke", 404);
        }


        $joke->update($validated);

        return ApiResponse::success($joke, "Joke updated");
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        // check user owns joke or is staff/admin

        $joke = Joke::find($id);

        if (!$joke) {
            return ApiResponse::error([], "Joke not found", 404);
        }

        // check user owns joke
        $user = $request->user();
//        if ($joke->user_id !== $user->id || !$user->hasAnyRole(['super-user', 'admin'])) {
        if ($joke->user_id !== $user->id || !$user->hasAnyRole(['admin', 'staff', 'super-user'])) {
            return ApiResponse::error([], "You do not have permission to delete this joke", 403);
        }

        $joke->delete();

        return ApiResponse::success([], "Joke <$id> moved to trash");
    }

    /**
     * Show all soft deleted Categories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function trash(Request $request): JsonResponse
    {
        $trashed = Joke::onlyTrashed()->get();

        return ApiResponse::success($trashed, "Trashed Jokes retrieved");
    }

    /**
     * Recover all soft deleted categories from trash
     *
     * @return JsonResponse
     */
    public function recoverAll(): JsonResponse
    {
        Joke::onlyTrashed()->restore();

        return ApiResponse::success([], "all Jokes restored successfully");
    }

    /**
     * Remove all soft deleted categories from trash
     *
     * @return JsonResponse
     */
    public function removeAll(): JsonResponse
    {
        Joke::onlyTrashed()->forceDelete();

        return ApiResponse::success([], "all Jokes permanently deleted");
    }

    /**
     * Recover specified soft deleted category from trash
     *
     * @param string $id
     * @return JsonResponse
     */
    public function recoverOne(string $id): JsonResponse
    {
        $joke = Joke::onlyTrashed()->find($id);

        if (!$joke) {
            return ApiResponse::error([], "joke not found in trash");
        }

        $joke->restore();

        return ApiResponse::success([], "Joke restored successfully");
    }

    /**
     * Remove specified soft deleted category from trash
     *
     * @param string $id
     * @return JsonResponse
     */
    public function removeOne(string $id): JsonResponse
    {
        $joke = Joke::onlyTrashed()->find($id);

        if (!$joke) {
            return ApiResponse::error([], "joke not found in trash");
        }

        $joke->forceDelete();

        return ApiResponse::success([], "Joke permanently deleted");
    }

    public function random(Request $request)
    {
        $joke = Joke::inRandomOrder()->first();

        $tokens = $request->user();
        return ApiResponse::success([$joke, 'tokens' => $tokens], "Random joke retrieved");
    }

    // Display all jokes in a category
    public function jokesByCategory(string $categoryId): JsonResponse
    {
        $category = Category::find($categoryId);
        if (!$category) {
            return ApiResponse::error([], "Category not found", 404);
        }
        $jokes = $category->jokesIncategory()->get();
        return ApiResponse::success($jokes, "Jokes retrieved");
    }


}
