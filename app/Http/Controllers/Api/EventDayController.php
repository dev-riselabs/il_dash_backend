<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventDayController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = EventDay::query();

        if ($eventId = $request->integer('event_id')) {
            $q->where('event_id', $eventId);
        }

        if ($search = $request->string('search')->toString()) {
            $q->where(function ($w) use ($search) {
                $w->where('label', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderBy('date')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'day_no' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        $eventDay = EventDay::create($data);
        return response()->json($eventDay, 201);
    }

    public function show(EventDay $eventDay): JsonResponse
    {
        return response()->json($eventDay);
    }

    public function update(Request $request, EventDay $eventDay): JsonResponse
    {
        $data = $request->validate([
            'day_no' => ['sometimes', 'integer', 'min:1'],
            'date' => ['sometimes', 'date'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        $eventDay->update($data);
        return response()->json($eventDay);
    }

    public function destroy(EventDay $eventDay): JsonResponse
    {
        $eventDay->delete();
        return response()->json(null, 204);
    }
}
