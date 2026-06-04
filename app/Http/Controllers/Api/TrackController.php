<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Track::query();

        if ($eventId = $request->integer('event_id')) {
            $q->where('event_id', $eventId);
        }

        if ($search = $request->string('search')->toString()) {
            $q->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->integer('per_page', 50);
        return response()->json($q->orderBy('name')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'name' => ['required', 'string', 'max:160', 'unique:tracks,name,NULL,id,event_id,' . $request->integer('event_id')],
            'slug' => ['nullable', 'string', 'max:160'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $track = Track::create($data);
        return response()->json($track, 201);
    }

    public function show(Track $track): JsonResponse
    {
        return response()->json($track);
    }

    public function update(Request $request, Track $track): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:160', 'unique:tracks,name,' . $track->id . ',id,event_id,' . $track->event_id],
            'slug' => ['nullable', 'string', 'max:160'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $track->update($data);
        return response()->json($track);
    }

    public function destroy(Track $track): JsonResponse
    {
        $track->delete();
        return response()->json(null, 204);
    }
}
