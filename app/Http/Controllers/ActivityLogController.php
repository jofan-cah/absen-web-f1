<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs (web admin)
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'karyawan'])->recent();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by platform
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('module_id', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage);

        // Get filter options
        $filterOptions = [
            'actions' => [
                'login', 'logout', 'login_failed',
                'create', 'update', 'delete',
                'approve', 'reject', 'submit',
                'export', 'import', 'error', 'other'
            ],
            'modules' => ActivityLog::distinct()->whereNotNull('module')->pluck('module'),
            'platforms' => ['web', 'mobile', 'api', 'system'],
        ];

        // Summary stats
        $today = Carbon::today();
        $stats = [
            'total_today' => ActivityLog::whereDate('created_at', $today)->count(),
            'logins_today' => ActivityLog::whereDate('created_at', $today)->where('action', 'login')->count(),
            'errors_today' => ActivityLog::whereDate('created_at', $today)->where('action', 'error')->count(),
            'changes_today' => ActivityLog::whereDate('created_at', $today)->whereIn('action', ['create', 'update', 'delete'])->count(),
        ];

        return view('activity-logs.index', compact('logs', 'filterOptions', 'stats'));
    }

    /**
     * Display activity log detail
     */
    public function show($id)
    {
        $log = ActivityLog::with(['user', 'karyawan'])->findOrFail($id);
        return view('activity-logs.show', compact('log'));
    }

    /**
     * API: Get activity logs (JSON)
     */
    public function apiIndex(Request $request)
    {
        $query = ActivityLog::with(['user', 'karyawan'])->recent();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]
        ]);
    }

    /**
     * API: Get login history
     */
    public function apiLoginHistory(Request $request)
    {
        $query = ActivityLog::logins()->recent();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]
        ]);
    }

    /**
     * API: Get error logs
     */
    public function apiErrors(Request $request)
    {
        $query = ActivityLog::errors()->recent();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ]
        ]);
    }

    /**
     * API: Get activity summary/stats
     */
    public function apiStats(Request $request)
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'today' => [
                'total' => ActivityLog::whereDate('created_at', $today)->count(),
                'logins' => ActivityLog::whereDate('created_at', $today)->where('action', 'login')->count(),
                'login_failed' => ActivityLog::whereDate('created_at', $today)->where('action', 'login_failed')->count(),
                'creates' => ActivityLog::whereDate('created_at', $today)->where('action', 'create')->count(),
                'updates' => ActivityLog::whereDate('created_at', $today)->where('action', 'update')->count(),
                'deletes' => ActivityLog::whereDate('created_at', $today)->where('action', 'delete')->count(),
                'errors' => ActivityLog::whereDate('created_at', $today)->where('action', 'error')->count(),
            ],
            'this_week' => [
                'total' => ActivityLog::where('created_at', '>=', $thisWeek)->count(),
                'logins' => ActivityLog::where('created_at', '>=', $thisWeek)->where('action', 'login')->count(),
                'errors' => ActivityLog::where('created_at', '>=', $thisWeek)->where('action', 'error')->count(),
            ],
            'this_month' => [
                'total' => ActivityLog::where('created_at', '>=', $thisMonth)->count(),
                'by_action' => ActivityLog::where('created_at', '>=', $thisMonth)
                    ->selectRaw('action, count(*) as count')
                    ->groupBy('action')
                    ->pluck('count', 'action'),
                'by_module' => ActivityLog::where('created_at', '>=', $thisMonth)
                    ->whereNotNull('module')
                    ->selectRaw('module, count(*) as count')
                    ->groupBy('module')
                    ->pluck('count', 'module'),
            ],
            'recent_errors' => ActivityLog::errors()->recent()->limit(5)->get(),
            'recent_logins' => ActivityLog::where('action', 'login')->recent()->limit(5)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Clear old logs (admin only)
     */
    public function clearOldLogs(Request $request)
    {
        $daysToKeep = $request->get('days', 90);
        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        $deleted = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        // Log this action
        ActivityLog::log('delete', "Menghapus {$deleted} activity logs lama (>{$daysToKeep} hari)", [
            'module' => 'ActivityLog',
            'new_data' => [
                'deleted_count' => $deleted,
                'days_kept' => $daysToKeep,
                'cutoff_date' => $cutoffDate->format('Y-m-d'),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil menghapus {$deleted} log lama",
            'data' => [
                'deleted_count' => $deleted,
            ]
        ]);
    }
}
