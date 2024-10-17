<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $term = Term::first();
        return view('admin.terms.index', compact('terms'));
    }
    public function edit(Request $request, Term $term)
    {
        $terms = Term::get();
        return view('admin.terms.edit', compact('terms'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);
        $term->update();

        return redirect()->route('admin.terms.update')
                            ->with('flash_message', '利用規約を編集しました。');
    }
}
