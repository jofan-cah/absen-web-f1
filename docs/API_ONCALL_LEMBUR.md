# API OnCall Lembur

## Overview

API khusus untuk fitur OnCall Lembur dengan screen terpisah di Mobile.

**Base URL:** `/api/oncall`

**Authentication:** Bearer Token (Sanctum)

---

## Flow OnCall

```
┌─────────────────────────────────────────────────────────────────┐
│                     FLOW ONCALL LEMBUR                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Admin buat Jadwal OnCall                                    │
│           ↓                                                     │
│  2. GET /api/oncall/today                                       │
│     → Cek apakah ada jadwal oncall hari ini                     │
│           ↓                                                     │
│  3. POST /api/oncall/clock-in                                   │
│     → Clock in dengan foto + lokasi                             │
│     → Data masuk ke: Absen (clock_in) + Lembur (started_at)     │
│           ↓                                                     │
│  4. PUT /api/oncall/{id}/report (OPTIONAL)                      │
│     → Isi laporan pekerjaan + bukti foto                        │
│     → Bisa diisi sebelum clock out                              │
│           ↓                                                     │
│  5. POST /api/oncall/clock-out                                  │
│     → Clock out dengan foto + lokasi + laporan                  │
│     → Data masuk ke: Absen (clock_out) + Lembur (jam_selesai)   │
│     → Status langsung: SUBMITTED                                │
│           ↓                                                     │
│  6. Admin Approve/Reject                                        │
│     → Generate Tunjangan jika approved                          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Endpoints

### 1. Cek Jadwal OnCall Hari Ini

**Endpoint:** `GET /api/oncall/today`

**Description:** Mengecek apakah karyawan memiliki jadwal oncall hari ini dan status-nya.

**Response Success (200):**
```json
{
  "success": true,
  "message": "Status OnCall hari ini",
  "data": {
    "has_oncall": true,
    "jadwal": {
      "jadwal_id": "JDW123ABC",
      "karyawan_id": "KAR001",
      "shift_id": "SHIFT-ONCALL",
      "date": "2026-01-24",
      "type": "oncall",
      "status": "normal",
      "shift": {
        "shift_id": "SHIFT-ONCALL",
        "name": "OnCall",
        "start_time": "22:00:00",
        "end_time": "06:00:00"
      }
    },
    "absen": {
      "absen_id": "ABS123ABC",
      "clock_in": null,
      "clock_out": null,
      "status": "scheduled"
    },
    "lembur": null,
    "status": "waiting_clock_in",
    "actions": {
      "can_clock_in": true,
      "can_clock_out": false,
      "can_fill_report": false
    },
    "shift_info": {
      "name": "OnCall",
      "start_time": "22:00",
      "end_time": "06:00"
    }
  }
}
```

**Status Values:**
- `waiting_clock_in` - Belum clock in
- `in_progress` - Sudah clock in, belum clock out
- `submitted` - Sudah disubmit, menunggu approval
- `approved` - Sudah diapprove
- `rejected` - Ditolak

**Response No OnCall (200):**
```json
{
  "success": true,
  "message": "Status OnCall hari ini",
  "data": {
    "has_oncall": false,
    "jadwal": null,
    "absen": null,
    "lembur": null,
    "message": "Tidak ada jadwal OnCall untuk hari ini"
  }
}
```

---

### 2. Clock In OnCall

**Endpoint:** `POST /api/oncall/clock-in`

**Description:** Melakukan clock in oncall dengan foto dan lokasi.

**Request Body (multipart/form-data):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| photo | File | Yes | Foto selfie clock in (jpeg, png, jpg, max 5MB) |
| latitude | Double | Yes | Latitude lokasi |
| longitude | Double | Yes | Longitude lokasi |
| address | String | Yes | Alamat lengkap |

**Response Success (200):**
```json
{
  "success": true,
  "message": "Clock in OnCall berhasil",
  "data": {
    "absen": {
      "absen_id": "ABS123ABC",
      "clock_in": "22:15:00",
      "clock_in_photo": "absen_photos/oncall_clock_in_KAR001_2026-01-24_xxx.jpg",
      "clock_in_latitude": -6.12345678,
      "clock_in_longitude": 106.12345678,
      "clock_in_address": "Jl. Sudirman No. 1, Jakarta",
      "status": "present"
    },
    "lembur": {
      "lembur_id": "LMB20260124XXXX",
      "jenis_lembur": "oncall",
      "jam_mulai": "22:15:00",
      "status": "draft",
      "started_at": "2026-01-24T22:15:00.000000Z"
    },
    "clock_in_time": "22:15:00"
  }
}
```

**Response Error (400):**
```json
{
  "success": false,
  "message": "Sudah melakukan clock in OnCall hari ini"
}
```

---

### 3. Update Laporan (Optional)

**Endpoint:** `PUT /api/oncall/{lembur_id}/report`

**Description:** Mengisi atau mengupdate laporan pekerjaan oncall. Bisa dilakukan sebelum clock out.

**Request Body (multipart/form-data):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| deskripsi_pekerjaan | String | Yes | Deskripsi pekerjaan yang dilakukan (max 1000 char) |
| bukti_foto | File | No | Foto bukti pekerjaan (jpeg, png, jpg, max 5MB) |

**Response Success (200):**
```json
{
  "success": true,
  "message": "Laporan OnCall berhasil disimpan",
  "data": {
    "lembur": {
      "lembur_id": "LMB20260124XXXX",
      "deskripsi_pekerjaan": "Monitor server, restart service apache",
      "bukti_foto": "lembur/KAR001/oncall_xxx.jpg"
    }
  }
}
```

---

### 4. Clock Out OnCall

**Endpoint:** `POST /api/oncall/clock-out`

**Description:** Melakukan clock out oncall. **Status langsung menjadi SUBMITTED.**

**Request Body (multipart/form-data):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| photo | File | Yes | Foto selfie clock out (jpeg, png, jpg, max 5MB) |
| latitude | Double | Yes | Latitude lokasi |
| longitude | Double | Yes | Longitude lokasi |
| address | String | Yes | Alamat lengkap |
| deskripsi_pekerjaan | String | Yes | Deskripsi pekerjaan yang dilakukan |
| bukti_foto | File | No | Foto bukti pekerjaan lembur |

**Response Success (200):**
```json
{
  "success": true,
  "message": "Clock out OnCall berhasil. Lembur sudah disubmit untuk approval.",
  "data": {
    "absen": {
      "absen_id": "ABS123ABC",
      "clock_in": "22:15:00",
      "clock_out": "06:00:00",
      "clock_out_photo": "absen_photos/oncall_clock_out_KAR001_2026-01-24_xxx.jpg",
      "clock_out_latitude": -6.12345678,
      "clock_out_longitude": 106.12345678,
      "clock_out_address": "Jl. Sudirman No. 1, Jakarta",
      "work_hours": 7.75,
      "status": "present"
    },
    "lembur": {
      "lembur_id": "LMB20260124XXXX",
      "jenis_lembur": "oncall",
      "jam_mulai": "22:15:00",
      "jam_selesai": "06:00:00",
      "total_jam": 7.75,
      "deskripsi_pekerjaan": "Monitor server, restart service apache",
      "status": "submitted",
      "submitted_at": "2026-01-25T06:00:00.000000Z"
    },
    "clock_out_time": "06:00:00",
    "work_hours": 7.75
  }
}
```

---

### 5. Detail OnCall

**Endpoint:** `GET /api/oncall/{lembur_id}`

**Description:** Melihat detail lengkap oncall lembur.

**Response Success (200):**
```json
{
  "success": true,
  "message": "Detail OnCall berhasil diambil",
  "data": {
    "lembur": {
      "lembur_id": "LMB20260124XXXX",
      "karyawan_id": "KAR001",
      "tanggal_lembur": "2026-01-24",
      "jenis_lembur": "oncall",
      "jam_mulai": "22:15:00",
      "jam_selesai": "06:00:00",
      "total_jam": 7.75,
      "deskripsi_pekerjaan": "Monitor server, restart service apache",
      "bukti_foto": "lembur/KAR001/oncall_xxx.jpg",
      "bukti_foto_url": "https://s3.../lembur/KAR001/oncall_xxx.jpg?...",
      "status": "submitted",
      "absen": {
        "absen_id": "ABS123ABC",
        "clock_in": "22:15:00",
        "clock_out": "06:00:00",
        "clock_in_photo_url": "https://s3.../absen_photos/oncall_clock_in_xxx.jpg?...",
        "clock_out_photo_url": "https://s3.../absen_photos/oncall_clock_out_xxx.jpg?..."
      }
    },
    "tracking": {
      "clock_in": "22:15:00",
      "clock_out": "06:00:00",
      "started_at": "2026-01-24 22:15:00",
      "completed_at": "2026-01-25 06:00:00",
      "submitted_at": "2026-01-25 06:00:00",
      "approved_at": null
    },
    "tunjangan_info": null
  }
}
```

---

### 6. List OnCall

**Endpoint:** `GET /api/oncall/my-list`

**Description:** Melihat daftar oncall lembur karyawan.

**Query Parameters:**
| Param | Type | Required | Description |
|-------|------|----------|-------------|
| month | Int | No | Bulan (default: bulan ini) |
| year | Int | No | Tahun (default: tahun ini) |
| status | String | No | Filter status: draft, submitted, approved, rejected |
| per_page | Int | No | Jumlah per halaman (default: 15) |

**Response Success (200):**
```json
{
  "success": true,
  "message": "Data OnCall berhasil diambil",
  "data": [
    {
      "lembur_id": "LMB20260124XXXX",
      "tanggal_lembur": "2026-01-24",
      "jam_mulai": "22:15:00",
      "jam_selesai": "06:00:00",
      "total_jam": 7.75,
      "status": "submitted"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 5
  },
  "summary": {
    "total": 5,
    "draft": 0,
    "submitted": 2,
    "approved": 3,
    "rejected": 0,
    "total_jam": 23.25
  },
  "period": {
    "month": 1,
    "year": 2026,
    "month_name": "January 2026"
  }
}
```

---

## Flutter Screen Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    FLUTTER SCREEN FLOW                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────┐                                            │
│  │   HOME SCREEN   │                                            │
│  │                 │                                            │
│  │  [OnCall Card]  │  ← Tampilkan jika has_oncall = true        │
│  │  Status: ...    │                                            │
│  └────────┬────────┘                                            │
│           │                                                     │
│           ▼                                                     │
│  ┌─────────────────┐                                            │
│  │  ONCALL SCREEN  │  ← Screen terpisah dari Absen biasa        │
│  │                 │                                            │
│  │  Status:        │                                            │
│  │  - waiting      │  → Show [Clock In] button                  │
│  │  - in_progress  │  → Show [Clock Out] button + Report form   │
│  │  - submitted    │  → Show status "Menunggu Approval"         │
│  │  - approved     │  → Show status "Disetujui"                 │
│  │  - rejected     │  → Show status "Ditolak"                   │
│  │                 │                                            │
│  └────────┬────────┘                                            │
│           │                                                     │
│           ▼                                                     │
│  ┌─────────────────┐                                            │
│  │ CLOCK IN FLOW   │                                            │
│  │                 │                                            │
│  │  1. Take Photo  │                                            │
│  │  2. Get Location│                                            │
│  │  3. POST /clock-in                                           │
│  │                 │                                            │
│  └────────┬────────┘                                            │
│           │                                                     │
│           ▼                                                     │
│  ┌─────────────────┐                                            │
│  │ IN PROGRESS     │                                            │
│  │                 │                                            │
│  │  Timer: 02:30:15│  ← Hitung dari clock_in                    │
│  │                 │                                            │
│  │  [Isi Laporan]  │  ← Optional, bisa isi dulu                 │
│  │  [Clock Out]    │                                            │
│  │                 │                                            │
│  └────────┬────────┘                                            │
│           │                                                     │
│           ▼                                                     │
│  ┌─────────────────┐                                            │
│  │ CLOCK OUT FLOW  │                                            │
│  │                 │                                            │
│  │  1. Take Photo  │                                            │
│  │  2. Get Location│                                            │
│  │  3. Fill Report │  ← deskripsi_pekerjaan (required)          │
│  │  4. Bukti Foto  │  ← optional                                │
│  │  5. POST /clock-out                                          │
│  │                 │                                            │
│  │  → Auto Submit! │                                            │
│  │                 │                                            │
│  └─────────────────┘                                            │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Data Model Relationship

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│   JADWAL    │       │    ABSEN    │       │   LEMBUR    │
│ type=oncall │───────│ type=oncall │───────│jenis=oncall │
├─────────────┤  1:1  ├─────────────┤  1:1  ├─────────────┤
│ jadwal_id   │       │ absen_id    │       │ lembur_id   │
│ karyawan_id │       │ jadwal_id   │◄──────│ absen_id    │
│ shift_id    │       │ clock_in    │       │ oncall_     │
│ date        │       │ clock_out   │       │  jadwal_id  │◄──┐
│ type        │       │ *_photo     │       │ jam_mulai   │   │
│ status      │       │ *_latitude  │       │ jam_selesai │   │
└──────┬──────┘       │ *_longitude │       │ total_jam   │   │
       │              │ *_address   │       │ deskripsi   │   │
       │              │ work_hours  │       │ bukti_foto  │   │
       │              └─────────────┘       │ status      │   │
       │                                    └─────────────┘   │
       └─────────────────────────────────────────────────────┘
```

---

## Status Flow

```
DRAFT → SUBMITTED → APPROVED → (Generate Tunjangan)
                  ↘ REJECTED
```

**Note untuk OnCall:**
- Clock Out langsung set status = `submitted`
- Tidak perlu tombol Submit terpisah
- Admin approve langsung (bypass koordinator)

---

## Error Codes

| Code | Message |
|------|---------|
| 400 | Sudah melakukan clock in/out OnCall hari ini |
| 400 | Belum melakukan clock in OnCall |
| 404 | Tidak ada jadwal OnCall untuk hari ini |
| 404 | Data absen/lembur OnCall tidak ditemukan |
| 422 | Validation error (lihat errors object) |
