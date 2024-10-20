<?php

namespace App\Http\Controllers\Admin;

use App\Models\Term;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $term = Term::first();
        return view('admin.terms.index', compact('term'));
    }
    public function edit(Request $request, Term $term)
    {
        $terms = Term::first();
        return view('admin.terms.edit', compact('term'));
    }
    public function update(Request $request, Term $term)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $term->content = $request->input('content');
        $term->update();

        return redirect()->route('admin.terms.index')
                            ->with('flash_message', '利用規約を編集しました。');
    }
}
