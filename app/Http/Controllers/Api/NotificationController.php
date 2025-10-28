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
            $user_id = auth()->user()->user_id;

            if (!$user_id) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $perPage = $this->getPerPage($request);

            $notifications = Notification::where('user_id', $user_id)
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
            $user_id = auth()->user()->user_id;

            if (!$user_id) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $notifications = Notification::where('user_id', $user_id)
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
            $user_id = auth()->user()->user_id;

            if (!$user_id) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $count = Notification::where('user_id', $user_id)
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
            $user_id = auth()->user()->user_id;

            $notification = Notification::where('notification_id', $id)
                ->where('user_id', $user_id)
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
            $user_id = auth()->user()->user_id;

            $notification = Notification::where('notification_id', $id)
                ->where('user_id', $user_id)
                ->first();

            if (!$notification) {
                return $this->notFoundResponse('Notifikasi tidak ditemukan');
            }

            $notification->markAsRead();

            Log::info('Notification marked as read', [
                'notification_id' => $id,
                'user_id' => $user_id
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
            $user_id = auth()->user()->user_id;

            $updated = Notification::where('user_id', $user_id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            Log::info('All notifications marked as read', [
                'user_id' => $user_id,
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
            $user_id = auth()->user()->user_id;

            $notification = Notification::where('notification_id', $id)
                ->where('user_id', $user_id)
                ->first();

            if (!$notification) {
                return $this->notFoundResponse('Notifikasi tidak ditemukan');
            }

            $notification->delete();

            Log::info('Notification deleted', [
                'notification_id' => $id,
                'user_id' => $user_id
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
            $user_id = auth()->user()->user_id;

            $deleted = Notification::where('user_id', $user_id)
                ->where('is_read', true)
                ->delete();

            Log::info('Read notifications cleared', [
                'user_id' => $user_id,
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
}

