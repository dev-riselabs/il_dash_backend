<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Speaker;
use App\Models\SpeakerEngagementScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpeakerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Speaker::query();

        if ($search = $request->string('search')->toString()) {
            $q->where(function ($w) use ($search) {
                $w->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderBy('last_name')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email'],
            'organization' => ['nullable', 'string', 'max:160'],
            'job_title' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:512'],
            'country' => ['nullable', 'string', 'max:80'],
            'session_id' => ['nullable', 'integer', 'exists:event_sessions,id'],
            'role' => ['nullable', 'in:keynote,panelist,moderator'],
        ]);

        $sessionId = $data['session_id'] ?? null;
        $role = $data['role'] ?? 'panelist';
        
        unset($data['session_id'], $data['role']);

        $speaker = Speaker::create($data);

        // Attach speaker to session if session_id provided
        if ($sessionId) {
            $speaker->sessions()->attach($sessionId, ['role' => $role]);
        }

        return response()->json($speaker->load('sessions:id,title,starts_at'), 201);
    }

    public function show(Speaker $speaker): JsonResponse
    {
        return response()->json($speaker->load('sessions:id,title,starts_at'));
    }

    public function update(Request $request, Speaker $speaker): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:120'],
            'last_name' => ['sometimes', 'string', 'max:120'],
            'email' => ['nullable', 'email'],
            'organization' => ['nullable', 'string', 'max:160'],
            'job_title' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:512'],
            'country' => ['nullable', 'string', 'max:80'],
        ]);

        $speaker->update($data);
        return response()->json($speaker);
    }

    public function destroy(Speaker $speaker): JsonResponse
    {
        $speaker->delete();
        return response()->json(null, 204);
    }

    public function topEngagement(Request $request): JsonResponse
    {
        $eventDayId = $request->integer('event_day_id');
        $q = SpeakerEngagementScore::with('speaker:id,first_name,last_name,organization,photo_url');
        if ($eventDayId) {
            $q->where('event_day_id', $eventDayId);
        }
        return response()->json($q->orderByDesc('score')->limit(10)->get());
    }
}
