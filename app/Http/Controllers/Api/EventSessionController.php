<?php

namespace App\Http\Controllers\Api;

use App\Events\SessionStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\EventSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventSessionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = EventSession::query()->with(['track:id,name', 'venue:id,name', 'sector:id,name', 'speakers:id,first_name,last_name']);

        foreach (['status', 'event_day_id', 'track_id', 'venue_id', 'sector_id'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        if ($search = $request->string('search')->toString()) {
            $q->where('title', 'like', "%{$search}%");
        }

        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderBy('starts_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'event_day_id' => ['nullable', 'exists:event_days,id'],
            'track_id' => ['nullable', 'exists:tracks,id'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'in:plenary,panel,keynote,roundtable,showcase'],
            'status' => ['nullable', 'in:upcoming,next,live,completed,cancelled,delayed'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'speaker_ids' => ['nullable', 'array'],
            'speaker_ids.*' => ['integer', 'exists:speakers,id'],
        ]);

        $speakerIds = $data['speaker_ids'] ?? [];
        unset($data['speaker_ids']);

        $session = EventSession::create($data);
        if ($speakerIds) {
            $session->speakers()->sync($speakerIds);
        }
        return response()->json($session->load('speakers'), 201);
    }

    public function show(EventSession $session): JsonResponse
    {
        return response()->json($session->load([
            'track', 'venue', 'sector', 'day', 'speakers',
            'insights', 'resources', 'timelineEvents', 'quotes.speaker', 'resolutions',
        ]));
    }

    public function update(Request $request, EventSession $session): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_day_id' => ['nullable', 'exists:event_days,id'],
            'track_id' => ['nullable', 'exists:tracks,id'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'type' => ['sometimes', 'in:plenary,panel,keynote,roundtable,showcase'],
            'starts_at' => ['sometimes', 'date'],
            'ends_at' => ['sometimes', 'date'],
            'ai_summary' => ['nullable', 'string'],
            'speaker_ids' => ['nullable', 'array'],
            'speaker_ids.*' => ['integer', 'exists:speakers,id'],
        ]);

        if (array_key_exists('speaker_ids', $data)) {
            $session->speakers()->sync($data['speaker_ids']);
            unset($data['speaker_ids']);
        }
        $session->update($data);
        return response()->json($session->load('speakers'));
    }

    public function updateStatus(Request $request, EventSession $session): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:upcoming,next,live,completed,cancelled,delayed'],
        ]);
        $changed = $data['status'] !== $session->status;
        $session->update($data);
        if ($changed) {
            SessionStatusChanged::dispatch($session);
        }
        return response()->json($session);
    }

    public function destroy(EventSession $session): JsonResponse
    {
        $session->delete();
        return response()->json(null, 204);
    }
}
