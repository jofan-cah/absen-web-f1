# API Response Format - Flutter Integration Guide

## Overview

Dokumentasi format response API untuk sinkronisasi dengan Flutter app.

**Base URL:** `https://your-domain.com/api`

**Authentication:** Bearer Token (Laravel Sanctum)

---

## Response Format Standard

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Success Response dengan Pagination

```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15,
    "has_more_pages": true,
    "next_page_url": "https://api.com/endpoint?page=2",
    "prev_page_url": null
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message here"
}
```

### Validation Error Response (422)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": [
      "The field_name is required.",
      "The field_name must be at least 3 characters."
    ],
    "another_field": [
      "The another_field format is invalid."
    ]
  }
}
```

---

## HTTP Status Codes

| Code | Name | Description | Response Type |
|------|------|-------------|---------------|
| 200 | OK | Request berhasil | `successResponse()` |
| 201 | Created | Resource berhasil dibuat | `createdResponse()` |
| 400 | Bad Request | Request tidak valid | `errorResponse()` |
| 401 | Unauthorized | Token tidak valid/expired | `unauthorizedResponse()` |
| 403 | Forbidden | Tidak punya akses | `forbiddenResponse()` |
| 404 | Not Found | Resource tidak ditemukan | `notFoundResponse()` |
| 422 | Unprocessable Entity | Validation error | `validationErrorResponse()` |
| 500 | Internal Server Error | Server error | `serverErrorResponse()` |

---

## Flutter Model Parsing

### Base API Response Model

```dart
class ApiResponse<T> {
  final bool success;
  final String message;
  final T? data;
  final Map<String, List<String>>? errors;
  final PaginationMeta? meta;

  ApiResponse({
    required this.success,
    required this.message,
    this.data,
    this.errors,
    this.meta,
  });

  factory ApiResponse.fromJson(
    Map<String, dynamic> json,
    T Function(dynamic)? fromJsonT,
  ) {
    return ApiResponse<T>(
      success: json['success'] ?? false,
      message: json['message'] ?? '',
      data: json['data'] != null && fromJsonT != null
          ? fromJsonT(json['data'])
          : json['data'],
      errors: json['errors'] != null
          ? Map<String, List<String>>.from(
              json['errors'].map((key, value) => MapEntry(
                key,
                List<String>.from(value),
              )),
            )
          : null,
      meta: json['meta'] != null
          ? PaginationMeta.fromJson(json['meta'])
          : null,
    );
  }

  bool get isSuccess => success;
  bool get isError => !success;
  bool get hasValidationErrors => errors != null && errors!.isNotEmpty;

  String? getFirstError(String field) {
    return errors?[field]?.first;
  }

  List<String> getAllErrors() {
    if (errors == null) return [];
    return errors!.values.expand((e) => e).toList();
  }
}
```

### Pagination Meta Model

```dart
class PaginationMeta {
  final int currentPage;
  final int perPage;
  final int total;
  final int lastPage;
  final int? from;
  final int? to;
  final bool hasMorePages;
  final String? nextPageUrl;
  final String? prevPageUrl;

  PaginationMeta({
    required this.currentPage,
    required this.perPage,
    required this.total,
    required this.lastPage,
    this.from,
    this.to,
    required this.hasMorePages,
    this.nextPageUrl,
    this.prevPageUrl,
  });

  factory PaginationMeta.fromJson(Map<String, dynamic> json) {
    return PaginationMeta(
      currentPage: json['current_page'] ?? 1,
      perPage: json['per_page'] ?? 15,
      total: json['total'] ?? 0,
      lastPage: json['last_page'] ?? 1,
      from: json['from'],
      to: json['to'],
      hasMorePages: json['has_more_pages'] ?? false,
      nextPageUrl: json['next_page_url'],
      prevPageUrl: json['prev_page_url'],
    );
  }
}
```

---

## API Service Helper

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  static const String baseUrl = 'https://your-domain.com/api';
  String? _token;

  void setToken(String token) {
    _token = token;
  }

  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (_token != null) 'Authorization': 'Bearer $_token',
  };

  Future<ApiResponse<T>> get<T>(
    String endpoint, {
    Map<String, dynamic>? queryParams,
    T Function(dynamic)? fromJsonT,
  }) async {
    try {
      final uri = Uri.parse('$baseUrl$endpoint').replace(
        queryParameters: queryParams?.map((k, v) => MapEntry(k, v.toString())),
      );

      final response = await http.get(uri, headers: _headers);
      return _handleResponse<T>(response, fromJsonT);
    } catch (e) {
      return ApiResponse<T>(
        success: false,
        message: 'Network error: ${e.toString()}',
      );
    }
  }

  Future<ApiResponse<T>> post<T>(
    String endpoint, {
    Map<String, dynamic>? body,
    T Function(dynamic)? fromJsonT,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl$endpoint'),
        headers: _headers,
        body: body != null ? jsonEncode(body) : null,
      );
      return _handleResponse<T>(response, fromJsonT);
    } catch (e) {
      return ApiResponse<T>(
        success: false,
        message: 'Network error: ${e.toString()}',
      );
    }
  }

  Future<ApiResponse<T>> postMultipart<T>(
    String endpoint, {
    Map<String, String>? fields,
    Map<String, String>? files, // key: field name, value: file path
    T Function(dynamic)? fromJsonT,
  }) async {
    try {
      final request = http.MultipartRequest('POST', Uri.parse('$baseUrl$endpoint'));

      request.headers.addAll({
        'Accept': 'application/json',
        if (_token != null) 'Authorization': 'Bearer $_token',
      });

      if (fields != null) {
        request.fields.addAll(fields);
      }

      if (files != null) {
        for (var entry in files.entries) {
          request.files.add(await http.MultipartFile.fromPath(
            entry.key,
            entry.value,
          ));
        }
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);
      return _handleResponse<T>(response, fromJsonT);
    } catch (e) {
      return ApiResponse<T>(
        success: false,
        message: 'Network error: ${e.toString()}',
      );
    }
  }

  ApiResponse<T> _handleResponse<T>(
    http.Response response,
    T Function(dynamic)? fromJsonT,
  ) {
    final json = jsonDecode(response.body);

    // Handle specific status codes
    switch (response.statusCode) {
      case 200:
      case 201:
        return ApiResponse<T>.fromJson(json, fromJsonT);
      case 401:
        // Token expired - trigger logout
        return ApiResponse<T>(
          success: false,
          message: json['message'] ?? 'Session expired. Please login again.',
        );
      case 403:
        return ApiResponse<T>(
          success: false,
          message: json['message'] ?? 'Access forbidden',
        );
      case 404:
        return ApiResponse<T>(
          success: false,
          message: json['message'] ?? 'Resource not found',
        );
      case 422:
        return ApiResponse<T>.fromJson(json, fromJsonT);
      case 500:
        return ApiResponse<T>(
          success: false,
          message: json['message'] ?? 'Server error. Please try again later.',
        );
      default:
        return ApiResponse<T>.fromJson(json, fromJsonT);
    }
  }
}
```

---

## Contoh Response Per Endpoint

### 1. Login

**Request:**
```http
POST /api/login
Content-Type: application/json

{
  "nip": "0125010006",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "user_id": "USR006",
      "nip": "0125010006",
      "name": "Sonya Mahardika Andriano Saputra",
      "email": "sonya.mahardika.andriano.saputra@company.com",
      "role": "admin"
    },
    "karyawan": {
      "karyawan_id": "KAR006",
      "full_name": "Sonya Mahardika Andriano Saputra",
      "position": "Vice General Manager",
      "department": {
        "department_id": "DEPT033",
        "name": "Management"
      }
    },
    "token": "1|abcdef123456..."
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "NIP atau password salah"
}
```

---

### 2. Get Absen Today

**Request:**
```http
GET /api/absen/today
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Status absen hari ini",
  "data": {
    "has_jadwal": true,
    "jadwal": {
      "jadwal_id": "JDWXXX",
      "date": "2026-01-24",
      "type": "normal",
      "shift": {
        "shift_id": "SHF001",
        "name": "Shift Pagi",
        "start_time": "08:00:00",
        "end_time": "17:00:00"
      }
    },
    "absen": {
      "absen_id": "ABSXXX",
      "clock_in": "08:05:00",
      "clock_out": null,
      "status": "present",
      "late_minutes": 5
    },
    "can_clock_in": false,
    "can_clock_out": true,
    "has_multiple_jadwal": false,
    "regular": { ... },
    "oncall": null
  }
}
```

**No Jadwal Response (404):**
```json
{
  "success": false,
  "message": "Tidak ada jadwal untuk hari ini"
}
```

---

### 3. Clock In

**Request:**
```http
POST /api/absen/clock-in
Authorization: Bearer {token}
Content-Type: multipart/form-data

photo: [FILE]
latitude: -6.12345678
longitude: 106.12345678
address: "Jl. Sudirman No. 1, Jakarta"
type: "normal" (optional)
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Clock in berhasil",
  "data": {
    "absen": {
      "absen_id": "ABSXXX",
      "clock_in": "08:05:00",
      "clock_in_photo": "absen_photos/clock_in_xxx.jpg",
      "clock_in_latitude": -6.12345678,
      "clock_in_longitude": 106.12345678,
      "clock_in_address": "Jl. Sudirman No. 1, Jakarta",
      "status": "present",
      "late_minutes": 5
    },
    "jadwal_type": "normal",
    "status": "present",
    "late_minutes": 5,
    "clock_in_time": "08:05:00"
  }
}
```

**Already Clocked In (400):**
```json
{
  "success": false,
  "message": "Sudah melakukan clock in hari ini"
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "photo": ["The photo field is required."],
    "latitude": ["The latitude field is required."],
    "longitude": ["The longitude field is required."],
    "address": ["The address field is required."]
  }
}
```

---

### 4. OnCall Today

**Request:**
```http
GET /api/oncall/today
Authorization: Bearer {token}
```

**Has OnCall Response (200):**
```json
{
  "success": true,
  "message": "Status OnCall hari ini",
  "data": {
    "has_oncall": true,
    "jadwal": {
      "jadwal_id": "JDWXXX",
      "date": "2026-01-24",
      "type": "oncall",
      "shift": {
        "shift_id": "SHIFT-ONCALL",
        "name": "OnCall",
        "start_time": "00:00:00",
        "end_time": "23:59:59"
      }
    },
    "absen": {
      "absen_id": "ABSXXX",
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
      "start_time": "00:00",
      "end_time": "23:59"
    }
  }
}
```

**No OnCall Response (200):**
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

### 5. OnCall Clock Out

**Request:**
```http
POST /api/oncall/clock-out
Authorization: Bearer {token}
Content-Type: multipart/form-data

photo: [FILE] (selfie clock out)
latitude: -6.12345678
longitude: 106.12345678
address: "Jl. Sudirman No. 1, Jakarta"
deskripsi_pekerjaan: "Monitor server, restart service apache" (required)
bukti_foto: [FILE] (optional - bukti pekerjaan)
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Clock out OnCall berhasil. Lembur sudah disubmit untuk approval.",
  "data": {
    "absen": {
      "absen_id": "ABSXXX",
      "clock_in": "22:15:00",
      "clock_out": "06:00:00",
      "work_hours": 7.75
    },
    "lembur": {
      "lembur_id": "LMBXXX",
      "jam_mulai": "22:15:00",
      "jam_selesai": "06:00:00",
      "total_jam": 7.75,
      "deskripsi_pekerjaan": "Monitor server, restart service apache",
      "status": "submitted"
    },
    "clock_out_time": "06:00:00",
    "work_hours": 7.75
  }
}
```

---

## Status Values Reference

### Absen Status
| Value | Description |
|-------|-------------|
| `scheduled` | Belum absen |
| `present` | Hadir tepat waktu |
| `late` | Hadir terlambat |
| `early_checkout` | Pulang lebih awal |
| `absent` | Tidak hadir |
| `sick` | Sakit (ijin approved) |
| `annual` | Cuti tahunan |
| `personal` | Ijin pribadi |
| `shift_swap` | Tukar shift |
| `compensation_leave` | Cuti pengganti |

### Lembur Status
| Value | Description |
|-------|-------------|
| `draft` | Draft (belum submit) |
| `submitted` | Menunggu approval |
| `approved` | Disetujui |
| `rejected` | Ditolak |
| `processed` | Sudah diproses (tunjangan dibuat) |

### OnCall Flow Status
| Value | Description |
|-------|-------------|
| `waiting_clock_in` | Belum clock in |
| `in_progress` | Sedang berjalan |
| `submitted` | Sudah disubmit |
| `approved` | Disetujui admin |
| `rejected` | Ditolak |

### Ijin Status
| Value | Description |
|-------|-------------|
| `pending` | Menunggu approval |
| `approved` | Disetujui |
| `rejected` | Ditolak |
| `cancelled` | Dibatalkan |

### Tunjangan Status
| Value | Description |
|-------|-------------|
| `pending` | Menunggu approval |
| `approved` | Disetujui |
| `requested` | Diminta pencairan |
| `received` | Sudah diterima |
| `rejected` | Ditolak |

---

## Error Handling di Flutter

```dart
Future<void> handleApiCall() async {
  final response = await apiService.post<UserData>(
    '/login',
    body: {'nip': nip, 'password': password},
    fromJsonT: (json) => UserData.fromJson(json),
  );

  if (response.isSuccess) {
    // Handle success
    final userData = response.data;
    // Navigate to home, save token, etc.
  } else if (response.hasValidationErrors) {
    // Handle validation errors
    final nipError = response.getFirstError('nip');
    final passwordError = response.getFirstError('password');

    if (nipError != null) {
      showError(nipError);
    }
    if (passwordError != null) {
      showError(passwordError);
    }
  } else {
    // Handle general error
    showError(response.message);
  }
}
```

---

## Query Parameters Standard

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `page` | int | Nomor halaman | 1 |
| `per_page` | int | Jumlah item per halaman | 15 (max 100) |
| `month` | int | Filter bulan (1-12) | Current month |
| `year` | int | Filter tahun | Current year |
| `status` | string | Filter by status | - |
| `search` | string | Search keyword | - |
| `sort_by` | string | Sort field | - |
| `sort_order` | string | asc / desc | desc |
