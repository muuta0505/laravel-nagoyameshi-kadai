<?php

namespace App\Http\Controllers\Admin;

use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Company::orderBy('created_at')->first();
        return view('admin.company.index', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $company = Company::all();
        return view('admin.company.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        $request->validate([
            'name' => 'required',
            'postal_code' => 'required|numeric|max:7',
            'address' => 'required',
            'address' => 'required',
            'establishment_date' => 'required',
            'capital' => 'required',
            'business' => 'required',
            'number_of_employees' => 'required',
        ]);
        $company = $request->input('name');
        $company = $request->input('postal_code');
        $company = $request->input('address');
        $company = $request->input('representative');
        $company = $request->input('establishment_date');
        $company = $request->input('capital');
        $company = $request->input('business');
        $company = $request->input('number_of_employees');
        $company->update();

        return redirect()->route('admin.companies.edit')
                            ->with('flash_message', '会社概要を編集しました。');
    }
}
