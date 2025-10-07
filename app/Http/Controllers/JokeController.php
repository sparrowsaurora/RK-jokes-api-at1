<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreJokeRequest;
use App\Http\Requests\UpdateJokeRequest;
use App\Models\Joke;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class JokeController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $categoryId = $request->query('category');

        $query = Joke::with('categories');

        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        $categoriesTotal = Category::count();
        $jokes = $query->paginate(10);
        $categories = Category::all();

        return view('jokes.index', compact('jokes', 'categories', 'categoryId', 'categoriesTotal'));
    }

    public function show(Joke $joke)
    {
        return view('jokes.show', compact('joke'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('jokes.create', compact('categories'));
    }

    public function store(StoreJokeRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] =  Auth::id();

        $joke = Joke::create($data);

        $categoryIds = $request->input('categories', []);

        if ($request->filled('new_categories')) {
            $newCategoryNames = array_map('trim', explode(',', $request->new_categories));
            foreach ($newCategoryNames as $name) {
                if ($name === '') continue;
                $category = Category::firstOrCreate(['name' => $name]);
                $categoryIds[] = $category->id;
            }
        }

        $joke->categories()->sync($categoryIds);

        return redirect()->route('jokes.index')->with('success', 'Joke created successfully!');
    }

    public function edit(Joke $joke)
    {
        $this->authorize('update', $joke);

        $categories = Category::all();
        return view('jokes.edit', compact('joke', 'categories'));
    }


    public function update(Request $request, Joke $joke)
    {
        $this->authorize('update', $joke);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $joke->update($validated);
        $joke->categories()->sync($request->input('categories', []));

        return redirect()->route('jokes.index')->with('success', 'Joke updated successfully.');
    }

    public function destroy(Joke $joke)
    {
        if (auth()->user()->hasRole('Client') && $joke->user_id !== auth()->id()) {
            abort(403);
        }

        $joke->delete();
        return redirect()->route('jokes.index')->with('success', 'Joke deleted (soft) successfully!');
    }

    // Trash methods for managing soft deleted jokes:

    public function trash()
    {
        $jokes = Joke::onlyTrashed()->paginate(10);
        return view('jokes.trash', compact('jokes'));
    }

    public function restore($id)
    {
        $joke = Joke::onlyTrashed()->findOrFail($id);
        $joke->restore();

        return redirect()->route('jokes.trash')->with('success', 'Joke restored successfully!');
    }

    public function forceDelete($id)
    {
        $joke = Joke::onlyTrashed()->findOrFail($id);
        $joke->forceDelete();

        return redirect()->route('jokes.trash')->with('success', 'Joke permanently deleted!');
    }

    public function restoreAll()
    {
        if (!auth()->user()->hasAnyRole(['Administrator', 'Staff'])) {
            abort(403);
        }

        Joke::onlyTrashed()->restore();

        return redirect()->route('jokes.trash')->with('success', 'All jokes restored!');
    }

    public function forceDeleteAll()
    {
        if (!auth()->user()->hasAnyRole(['Administrator', 'Staff'])) {
            abort(403);
        }

        Joke::onlyTrashed()->forceDelete();

        return redirect()->route('jokes.trash')->with('success', 'All jokes permanently deleted!');
    }


    public function byCategory(Category $category)
    {
        $jokes = $category->jokes()->paginate(10);
        return view('jokes.index', compact('jokes'));
    }
}
