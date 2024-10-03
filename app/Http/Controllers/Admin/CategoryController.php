<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword=$request->keyword;
        if ($keyword !== null) {
            $categories = Category::where('name','like',"%{$keyword}%")->paginate(15);
        } else {
            $categories = Category::paginate(15);
        }
        $total = $categories->total();
        return view('admin.categories.index', compact('categories', 'keyword', 'total'));
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        $category = new Category();

        $category->name = $request->input('name');

        $category->save();
        
        return redirect()->route('admin.categories.index')
                            ->with('flash_message', 'カテゴリを登録しました。');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
        ]);
        
        $category->name = $request->input('name');
        $category->update();

        return redirect()->route('admin.categories.index')
                            ->with('flash_message', 'カテゴリを編集しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
                            ->with('flash_message', 'カテゴリを削除しました。');
    }
}
