<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Investor::query();
        foreach (['region', 'country', 'type'] as $f) {
            if ($v = $request->input($f)) {
                $q->where($f, $v);
            }
        }
        if ($search = $request->string('search')->toString()) {
            $q->where('name', 'like', "%{$search}%");
        }
        $perPage = (int) $request->integer('per_page', 25);
        return response()->json($q->orderBy('name')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'type' => ['nullable', 'in:institutional,VC,bank,sovereign,corporate'],
            'country' => ['nullable', 'string', 'max:80'],
            'region' => ['nullable', 'string', 'max:32'],
            'logo_url' => ['nullable', 'string', 'max:512'],
            'sectors_of_interest' => ['nullable', 'array'],
        ]);
        return response()->json(Investor::create($data), 201);
    }

    public function show(Investor $investor): JsonResponse
    {
        return response()->json($investor->load(['deals', 'signals']));
    }

    public function update(Request $request, Investor $investor): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:200'],
            'type' => ['nullable', 'in:institutional,VC,bank,sovereign,corporate'],
            'country' => ['nullable', 'string', 'max:80'],
            'region' => ['nullable', 'string', 'max:32'],
            'logo_url' => ['nullable', 'string', 'max:512'],
            'sectors_of_interest' => ['nullable', 'array'],
        ]);
        $investor->update($data);
        return response()->json($investor);
    }

    public function recent(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);
        return response()->json(Investor::orderByDesc('created_at')->limit($limit)->get());
    }

    public function byRegion(): JsonResponse
    {
        return response()->json(
            Investor::selectRaw('region, count(*) as count')->groupBy('region')->get()
        );
    }

    public function byCountry(): JsonResponse
    {
        return response()->json(
            Investor::selectRaw('country, count(*) as count')->groupBy('country')->get()
        );
    }
}
