<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends BaseApiController
{
    public function show(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan->load('department');

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        return $this->successResponse([
            'user' => $user,
            'karyawan' => $karyawan
        ], 'Profile berhasil diambil');
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Update user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update karyawan
            $karyawan->update([
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return $this->successResponse([
                'user' => $user->fresh(),
                'karyawan' => $karyawan->fresh()->load('department')
            ], 'Profile berhasil diupdate');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal update profile');
        }
    }

    public function uploadPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        if (!$request->hasFile('photo')) {
            return $this->errorResponse('File foto tidak ditemukan', 400);
        }

        try {
            // Delete old photo if exists
            if ($karyawan->photo) {
                Storage::disk('s3')->delete($karyawan->photo);
            }

            // Upload new photo
            $photo = $request->file('photo');
            $filename = 'karyawan_' . $karyawan->karyawan_id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('karyawan_photos', $filename, 'public');

            // Update karyawan
            $karyawan->update(['photo' => $photoPath]);

            return $this->successResponse([
                'photo_url' => Storage::url($photoPath),
                'photo_path' => $photoPath
            ], 'Foto profile berhasil diupload');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal upload foto');
        }
    }

    public function stats(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        // Stats bulan ini
        $thisMonth = now()->startOfMonth();
        $today = now();

        $stats = [
            'total_jadwal_bulan_ini' => $karyawan->jadwals()
                ->whereBetween('date', [$thisMonth, $today])
                ->count(),
            'total_hadir_bulan_ini' => $karyawan->absens()
                ->whereBetween('date', [$thisMonth, $today])
                ->whereIn('status', ['present', 'late'])
                ->count(),
            'total_terlambat_bulan_ini' => $karyawan->absens()
                ->whereBetween('date', [$thisMonth, $today])
                ->where('status', 'late')
                ->count(),
            'total_jam_kerja_bulan_ini' => round($karyawan->absens()
                ->whereBetween('date', [$thisMonth, $today])
                ->sum('work_hours'), 2),
        ];

        // Persentase kehadiran
        $stats['persentase_kehadiran'] = $stats['total_jadwal_bulan_ini'] > 0
            ? round(($stats['total_hadir_bulan_ini'] / $stats['total_jadwal_bulan_ini']) * 100, 1)
            : 0;

        return $this->successResponse([
            'stats' => $stats,
            'karyawan_info' => [
                'nama' => $karyawan->full_name,
                'nip' => $karyawan->nip,
                'position' => $karyawan->position,
                'department' => $karyawan->department->name ?? 'No Department',
                'hire_date' => $karyawan->hire_date ? $karyawan->hire_date->format('d M Y') : null,
                'status' => $karyawan->employment_status
            ]
        ], 'Statistics profile berhasil diambil');
    }
}
