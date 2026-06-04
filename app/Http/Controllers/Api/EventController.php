<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Event::query();

        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }
        if ($search = $request->string('search')->toString()) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderByDesc('starts_at')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:events,slug'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['nullable', 'in:upcoming,live,completed,cancelled'],
        ]);

        $data['slug'] = $this->resolveUniqueSlug($data['slug'] ?? null, $data['name']);
        $data['status'] = $data['status'] ?? 'upcoming';

        $event = Event::create($data);
        return response()->json($event, 201);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json($event->load([
            'days',
            'tracks',
            'sessions' => function ($q) {
                $q->with(['speakers', 'track', 'venue', 'sector', 'day'])->orderBy('starts_at');
            },
        ]));
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('events', 'slug')->ignore($event->id)],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['sometimes', 'date'],
            'ends_at' => ['sometimes', 'date', 'after:starts_at'],
            'status' => ['sometimes', 'in:upcoming,live,completed,cancelled'],
        ]);

        if (array_key_exists('slug', $data) && $data['slug'] !== $event->slug) {
            $data['slug'] = $this->resolveUniqueSlug($data['slug'], $data['name'] ?? $event->name, $event->id);
        }

        $event->update($data);
        return response()->json($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();
        return response()->json(null, 204);
    }

    private function resolveUniqueSlug(?string $slug, string $fallbackName, ?int $ignoreId = null): string
    {
        $base = $slug ?: Str::slug($fallbackName);
        $base = $base !== '' ? $base : 'event';
        $candidate = $base;
        $suffix = 2;

        $query = fn (string $value) => Event::where('slug', $value)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId));

        while ($query($candidate)->exists()) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
