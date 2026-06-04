<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Sector::query();

        if ($search = $request->string('search')->toString()) {
            $q->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->integer('per_page', 50);
        return response()->json($q->orderBy('name')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160', 'unique:sectors,name'],
            'slug' => ['nullable', 'string', 'max:160'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $sector = Sector::create($data);
        return response()->json($sector, 201);
    }

    public function show(Sector $sector): JsonResponse
    {
        return response()->json($sector);
    }

    public function update(Request $request, Sector $sector): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:160', 'unique:sectors,name,' . $sector->id],
            'slug' => ['nullable', 'string', 'max:160'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $sector->update($data);
        return response()->json($sector);
    }

    public function destroy(Sector $sector): JsonResponse
    {
        $sector->delete();
        return response()->json(null, 204);
    }
}
