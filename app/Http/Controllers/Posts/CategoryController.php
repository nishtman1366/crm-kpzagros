<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Posts\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('posts')->orderBy('id', 'ASC')->get();

        return Inertia::render('Dashboard/Posts/ListCategories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('categoryForm', [
            'name' => 'required'
        ]);

        Category::create($request->all());

        return redirect()->route('dashboard.posts.categories.list');
    }

    public function view(Request $request)
    {

    }

    function update(Request $request)
    {
        $id = $request->route('categoryId');
        $category = Category::find($id);
        $request->validateWithBag('categoryForm', [
            'name' => 'required'
        ]);

        $category->name = $request->get('name');
        $category->save();

        return redirect()->route('dashboard.posts.categories.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('categoryId');
        $category = Category::find($id);
        $category->delete();
        return redirect()->route('dashboard.posts.categories.list');
    }
}
