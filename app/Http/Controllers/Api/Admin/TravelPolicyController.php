<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelPolicy;
use Illuminate\Http\Request;

class TravelPolicyController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => TravelPolicy::with('destinationCity')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_city_id' => 'required|exists:ref_cities,id',
            'grade_group' => 'required|string|max:50',
            'daily_allowance' => 'required|numeric|min:0',
            'max_hotel_rate' => 'required|numeric|min:0',
            'max_transport_rate' => 'required|numeric|min:0',
            'effective_year' => 'required|integer|min:2020',
        ]);

        $exists = TravelPolicy::where('destination_city_id', $request->destination_city_id)
            ->where('grade_group', $request->grade_group)
            ->where('effective_year', $request->effective_year)->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'SBM sudah ada.'], 400);
        }

        $policy = TravelPolicy::create($validated);

        return response()->json(['success' => true, 'data' => $policy], 201);
    }

    public function show($id)
    {
        return response()->json(['success' => true, 'data' => TravelPolicy::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $policy = TravelPolicy::findOrFail($id);
        $validated = $request->validate([
            'destination_city_id' => 'required|exists:ref_cities,id',
            'grade_group' => 'required|string|max:50',
            'daily_allowance' => 'required|numeric|min:0',
            'max_hotel_rate' => 'required|numeric|min:0',
            'max_transport_rate' => 'required|numeric|min:0',
            'effective_year' => 'required|integer|min:2020',
        ]);

        $policy->update($validated);

        return response()->json(['success' => true, 'data' => $policy]);
    }

    public function destroy($id)
    {
        TravelPolicy::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'SBM dihapus.']);
    }
}
