<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefCity;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => RefCity::orderBy('province_name')->orderBy('city_name')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'province_name' => 'required|string|max:100',
            'city_name'     => 'required|string|max:100',
        ]);
        $city = RefCity::create($validated);
        return response()->json(['success' => true, 'data' => $city], 201);
    }

    public function show($id)
    {
        return response()->json(['success' => true, 'data' => RefCity::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $city = RefCity::findOrFail($id);
        $validated = $request->validate([
            'province_name' => 'required|string|max:100',
            'city_name'     => 'required|string|max:100',
        ]);
        $city->update($validated);
        return response()->json(['success' => true, 'data' => $city]);
    }

    public function destroy($id)
    {
        RefCity::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Kota dihapus.']);
    }
}