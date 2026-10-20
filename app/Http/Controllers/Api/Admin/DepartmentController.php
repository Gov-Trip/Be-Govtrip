<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => Department::all()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:departments,code|max:50',
            'name' => 'required|string|max:150',
        ]);

        $department = Department::create($validated);

        return response()->json(['success' => true, 'message' => 'Departemen berhasil dibuat.', 'data' => $department], 201);
    }

    public function show($id)
    {
        return response()->json(['success' => true, 'data' => Department::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:departments,code,'.$department->id,
            'name' => 'required|string|max:150',
        ]);

        $department->update($validated);

        return response()->json(['success' => true, 'message' => 'Departemen diperbarui.', 'data' => $department]);
    }

    public function destroy($id)
    {
        Department::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Departemen dihapus.']);
    }
}
