<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Venue::query();

        if ($search = $request->string('search')->toString()) {
            $q->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->integer('per_page', 50);
        return response()->json($q->orderBy('name')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160', 'unique:venues,name'],
            'slug' => ['nullable', 'string', 'max:160'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:available,unavailable'],
        ]);

        $venue = Venue::create($data);
        return response()->json($venue, 201);
    }

    public function show(Venue $venue): JsonResponse
    {
        return response()->json($venue);
    }

    public function update(Request $request, Venue $venue): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:160', 'unique:venues,name,' . $venue->id],
            'slug' => ['nullable', 'string', 'max:160'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:available,unavailable'],
        ]);

        $venue->update($data);
        return response()->json($venue);
    }

    public function destroy(Venue $venue): JsonResponse
    {
        $venue->delete();
        return response()->json(null, 204);
    }
}
