<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    public function index()
    {
        $drugs = Drug::orderBy('name')->paginate(30);
        return view('admin.drugs.index', compact('drugs'));
    }

    public function create()
    {
        return view('admin.drugs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        Drug::create($request->only('name', 'unit', 'description'));

        return redirect()->route('admin.drugs.index')->with('success', 'Drug created successfully.');
    }

    public function edit(Drug $drug)
    {
        return view('admin.drugs.edit', compact('drug'));
    }

    public function update(Request $request, Drug $drug)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $drug->update($request->only('name', 'unit', 'description'));

        return redirect()->route('admin.drugs.index')->with('success', 'Drug updated successfully.');
    }

    public function destroy(Drug $drug)
    {
        $drug->delete();
        return redirect()->route('admin.drugs.index')->with('success', 'Drug deleted.');
    }
}
