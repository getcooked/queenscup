<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * The activity log screen.
 *
 * Read only by design: nothing in the panel edits or deletes a line, because
 * a record staff can tidy up is not much of a record.
 */
class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'action' => ['nullable', 'string', 'max:60'],
            'user' => ['nullable', 'integer'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $logs = ActivityLog::with('user')
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['user'] ?? null, fn ($query, $id) => $query->where('user_id', $id))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(
                fn ($sub) => $sub->where('description', 'like', "%{$term}%")
                    ->orWhere('actor_name', 'like', "%{$term}%")
                    ->orWhere('subject_id', 'like', "%{$term}%")
            ))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('activity', [
            'logs' => $logs,
            'filters' => $filters,
            'actions' => ActivityLog::ACTION_LABELS,
            // Only people who have actually done something, so the picker does
            // not list every account ever created.
            'actors' => User::whereIn('id', ActivityLog::query()->distinct()->pluck('user_id')->filter())
                ->orderBy('name')
                ->get(['id', 'name']),
            'todayCount' => ActivityLog::whereDate('created_at', now()->toDateString())->count(),
            'totalCount' => ActivityLog::count(),
        ]);
    }
}
