<?php

namespace App\Http\Controllers;

use App\Models\LogbookEntry;
use App\Services\LogbookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupervisorLogbookController extends Controller
{
    protected LogbookService $service;

    public function __construct(LogbookService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $query = LogbookEntry::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->orderByDesc('entry_date')->paginate(15));
    }

    public function approve(Request $request, LogbookEntry $logbook): JsonResponse
    {
        $entry = $this->service->approve($logbook, $request->user());

        return response()->json(['data' => $entry]);
    }

    public function return(Request $request, LogbookEntry $logbook): JsonResponse
    {
        $data = $request->validate([
            'comment' => ['required', 'string'],
        ]);

        $entry = $this->service->return($logbook, $request->user(), $data['comment']);

        return response()->json(['data' => $entry]);
    }
}