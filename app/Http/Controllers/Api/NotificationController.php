<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class NotificationController extends BaseApiController
{
    /**
     * Get all notifications (paginated)
     * GET /api/notifications?page=1&per_page=20
     */
    public function index(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $perPage = $this->getPerPage($request);

            $notifications = Notification::where('karyawan_id', $karyawanId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->paginatedResponse($notifications, 'Data notifikasi berhasil diambil');

        } catch (\Exception $e) {
            Log::error('Get notifications failed', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengambil data notifikasi');
        }
    }

    /**
     * Get unread notifications only
     * GET /api/notifications/unread
     */
    public function unread(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $notifications = Notification::where('karyawan_id', $karyawanId)
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->responseWithMeta(
                $notifications,
                ['count' => $notifications->count()],
                'Data notifikasi belum dibaca berhasil diambil'
            );

        } catch (\Exception $e) {
            Log::error('Get unread notifications failed', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengambil notifikasi belum dibaca');
        }
    }

    /**
     * Get unread count (untuk badge)
     * GET /api/notifications/unread-count
     */
    public function unreadCount(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $count = Notification::where('karyawan_id', $karyawanId)
                ->where('is_read', false)
                ->count();

            return $this->successResponse(['count' => $count], 'Jumlah notifikasi belum dibaca');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghitung notifikasi belum dibaca');
        }
    }

    /**
     * Get single notification detail
     * GET /api/notifications/{id}
     */
    public function show($id)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $notification = Notification::where('notification_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->first();

            if (!$notification) {
                return $this->notFoundResponse('Notifikasi tidak ditemukan');
            }

            return $this->successResponse($notification, 'Detail notifikasi berhasil diambil');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil detail notifikasi');
        }
    }

    /**
     * Mark notification as read
     * POST /api/notifications/{id}/read
     */
    public function markAsRead($id)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $notification = Notification::where('notification_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->first();

            if (!$notification) {
                return $this->notFoundResponse('Notifikasi tidak ditemukan');
            }

            $notification->markAsRead();

            Log::info('Notification marked as read', [
                'notification_id' => $id,
                'karyawan_id' => $karyawanId
            ]);

            return $this->successResponse($notification, 'Notifikasi berhasil ditandai sudah dibaca');

        } catch (\Exception $e) {
            Log::error('Mark as read failed', [
                'notification_id' => $id,
                'error' => $e->getMessage()
            ]);

            return $this->serverErrorResponse('Gagal menandai notifikasi sebagai sudah dibaca');
        }
    }

    /**
     * Mark all notifications as read
     * POST /api/notifications/read-all
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $updated = Notification::where('karyawan_id', $karyawanId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            Log::info('All notifications marked as read', [
                'karyawan_id' => $karyawanId,
                'count' => $updated
            ]);

            return $this->successResponse(
                ['count' => $updated],
                'Semua notifikasi berhasil ditandai sudah dibaca'
            );

        } catch (\Exception $e) {
            Log::error('Mark all as read failed', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal menandai semua notifikasi sebagai sudah dibaca');
        }
    }

    /**
     * Delete notification
     * DELETE /api/notifications/{id}
     */
    public function destroy($id)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $notification = Notification::where('notification_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->first();

            if (!$notification) {
                return $this->notFoundResponse('Notifikasi tidak ditemukan');
            }

            $notification->delete();

            Log::info('Notification deleted', [
                'notification_id' => $id,
                'karyawan_id' => $karyawanId
            ]);

            return $this->noContentResponse('Notifikasi berhasil dihapus');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus notifikasi');
        }
    }

    /**
     * Delete all read notifications
     * DELETE /api/notifications/clear-read
     */
    public function clearRead(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $deleted = Notification::where('karyawan_id', $karyawanId)
                ->where('is_read', true)
                ->delete();

            Log::info('Read notifications cleared', [
                'karyawan_id' => $karyawanId,
                'count' => $deleted
            ]);

            return $this->successResponse(
                ['count' => $deleted],
                'Notifikasi yang sudah dibaca berhasil dihapus'
            );

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus notifikasi yang sudah dibaca');
        }
    }
}{
    /**
     * Get all notifications untuk user yang login
     *
     * GET /api/notifications?page=1&per_page=20
     */
    public function index(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            if (!$karyawanId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki data karyawan'
                ], 400);
            }

            $perPage = $request->get('per_page', 20);

            $notifications = Notification::where('karyawan_id', $karyawanId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications->items(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get notifications failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread notifications only
     *
     * GET /api/notifications/unread
     */
    public function unread(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            if (!$karyawanId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki data karyawan'
                ], 400);
            }

            $notifications = Notification::where('karyawan_id', $karyawanId)
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'count' => $notifications->count(),
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            Log::error('Get unread notifications failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch unread notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread count only (untuk badge counter)
     *
     * GET /api/notifications/unread-count
     */
    public function unreadCount(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            if (!$karyawanId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki data karyawan'
                ], 400);
            }

            $count = Notification::where('karyawan_id', $karyawanId)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to count unread notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single notification detail
     *
     * GET /api/notifications/{id}
     */
    public function show($id)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $notification = Notification::where('notification_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $notification
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark single notification as read
     *
     * POST /api/notifications/{id}/read
     */
    public function markAsRead($id)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $notification = Notification::where('notification_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            Log::info('Notification marked as read', [
                'notification_id' => $id,
                'karyawan_id' => $karyawanId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => $notification
            ]);

        } catch (\Exception $e) {
            Log::error('Mark as read failed', [
                'notification_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     *
     * POST /api/notifications/read-all
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $updated = Notification::where('karyawan_id', $karyawanId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            Log::info('All notifications marked as read', [
                'karyawan_id' => $karyawanId,
                'count' => $updated
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'count' => $updated
            ]);

        } catch (\Exception $e) {
            Log::error('Mark all as read failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete notification
     *
     * DELETE /api/notifications/{id}
     */
    public function destroy($id)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $notification = Notification::where('notification_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            Log::info('Notification deleted', [
                'notification_id' => $id,
                'karyawan_id' => $karyawanId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all read notifications
     *
     * DELETE /api/notifications/clear-read
     */
    public function clearRead(Request $request)
    {
        try {
            $karyawanId = auth()->user()->karyawan_id;

            $deleted = Notification::where('karyawan_id', $karyawanId)
                ->where('is_read', true)
                ->delete();

            Log::info('Read notifications cleared', [
                'karyawan_id' => $karyawanId,
                'count' => $deleted
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Read notifications cleared',
                'count' => $deleted
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear read notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
