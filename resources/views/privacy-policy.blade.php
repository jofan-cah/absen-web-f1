<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - Hadirku</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
        }

        h1 {
            color: #1a73e8;
            font-size: 24px;
            margin-bottom: 10px;
            text-align: center;
        }

        h2 {
            color: #1a73e8;
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 2px solid #e8f0fe;
            padding-bottom: 5px;
        }

        h3 {
            font-size: 15px;
            margin-top: 15px;
            margin-bottom: 8px;
            color: #444;
        }

        p {
            margin-bottom: 12px;
            text-align: justify;
        }

        ul {
            margin-left: 20px;
            margin-bottom: 12px;
        }

        li {
            margin-bottom: 6px;
        }

        .update-date {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .section {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #1a73e8;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .contact-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            h1 {
                font-size: 20px;
            }

            h2 {
                font-size: 16px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <h1>KEBIJAKAN PRIVASI APLIKASI HADIRKU</h1>
    <p class="update-date">Terakhir diperbarui: 29 Januari 2026</p>

    <div class="section">
        <h2>1. PENDAHULUAN</h2>
        <p>Aplikasi Hadirku ("Aplikasi") dikembangkan oleh PT. Rasi Bintang Perkasa ("Kami") untuk keperluan sistem absensi karyawan. Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.</p>
    </div>

    <div class="section">
        <h2>2. DATA YANG KAMI KUMPULKAN</h2>

        <h3>a. Data Identitas</h3>
        <ul>
            <li>Nomor Induk Pegawai (NIP)</li>
            <li>Nama lengkap</li>
            <li>Jabatan/posisi</li>
        </ul>

        <h3>b. Data Lokasi</h3>
        <ul>
            <li>Koordinat GPS (latitude & longitude) saat melakukan absensi</li>
            <li>Alamat lokasi absensi</li>
        </ul>

        <h3>c. Data Foto</h3>
        <ul>
            <li>Foto selfie saat clock in dan clock out</li>
            <li>Foto disimpan sebagai bukti kehadiran</li>
        </ul>

        <h3>d. Data Perangkat</h3>
        <ul>
            <li>Token notifikasi (FCM Token)</li>
            <li>Jenis perangkat dan sistem operasi</li>
            <li>Versi aplikasi</li>
        </ul>

        <h3>e. Data Aktivitas</h3>
        <ul>
            <li>Waktu clock in dan clock out</li>
            <li>Riwayat kehadiran</li>
            <li>Pengajuan izin, lembur, dan cuti</li>
        </ul>
    </div>

    <div class="section">
        <h2>3. TUJUAN PENGGUNAAN DATA</h2>
        <p>Data Anda digunakan untuk:</p>
        <ul>
            <li>Mencatat dan memverifikasi kehadiran karyawan</li>
            <li>Memastikan karyawan berada di lokasi kerja yang benar</li>
            <li>Mengirimkan notifikasi terkait jadwal dan absensi</li>
            <li>Membuat laporan kehadiran</li>
            <li>Memproses pengajuan izin, lembur, dan tukar shift</li>
        </ul>
    </div>

    <div class="section">
        <h2>4. PENYIMPANAN DATA</h2>
        <ul>
            <li>Data disimpan di server yang aman dengan enkripsi</li>
            <li>Data foto disimpan selama masa kerja karyawan</li>
            <li>Data absensi disimpan sesuai ketentuan perusahaan</li>
            <li>Kami tidak menjual atau membagikan data ke pihak ketiga</li>
        </ul>
    </div>

    <div class="section">
        <h2>5. KEAMANAN DATA</h2>
        <p>Kami menerapkan langkah-langkah keamanan:</p>
        <ul>
            <li>Enkripsi data saat transmisi (HTTPS/SSL)</li>
            <li>Autentikasi token untuk akses API</li>
            <li>Pembatasan akses berdasarkan peran pengguna</li>
            <li>Deteksi lokasi palsu (fake GPS)</li>
        </ul>
    </div>

    <div class="section">
        <h2>6. HAK PENGGUNA</h2>
        <p>Anda berhak untuk:</p>
        <ul>
            <li>Mengakses data pribadi Anda</li>
            <li>Meminta koreksi data yang tidak akurat</li>
            <li>Meminta penghapusan data setelah tidak lagi menjadi karyawan</li>
            <li>Menolak penggunaan data untuk tujuan tertentu</li>
        </ul>
    </div>

    <div class="section">
        <h2>7. IZIN APLIKASI</h2>
        <p>Aplikasi memerlukan izin berikut:</p>
        <table>
            <thead>
                <tr>
                    <th>Izin</th>
                    <th>Tujuan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Kamera</td>
                    <td>Mengambil foto selfie saat absensi</td>
                </tr>
                <tr>
                    <td>Lokasi</td>
                    <td>Memverifikasi lokasi saat absensi</td>
                </tr>
                <tr>
                    <td>Notifikasi</td>
                    <td>Mengirim pengingat jadwal dan info penting</td>
                </tr>
                <tr>
                    <td>Internet</td>
                    <td>Mengirim dan menerima data dari server</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>8. COOKIES DAN TEKNOLOGI SERUPA</h2>
        <p>Aplikasi menggunakan token lokal untuk:</p>
        <ul>
            <li>Menyimpan sesi login</li>
            <li>Menyimpan preferensi pengguna</li>
            <li>Meningkatkan performa aplikasi</li>
        </ul>
    </div>

    <div class="section">
        <h2>9. PERUBAHAN KEBIJAKAN</h2>
        <p>Kami dapat memperbarui kebijakan ini sewaktu-waktu. Perubahan akan diinformasikan melalui aplikasi atau email.</p>
    </div>

    <div class="section">
        <h2>10. KONTAK</h2>
        <p>Jika ada pertanyaan terkait kebijakan privasi:</p>
        <div class="contact-box">
            <strong>PT. Rasi Bintang Perkasa</strong><br>
            Email: privacy@rasibintang.net.id<br>
            Alamat: [Alamat Perusahaan]
        </div>
    </div>

    <div class="section">
        <h2>11. PERSETUJUAN</h2>
        <p>Dengan menggunakan aplikasi ini, Anda menyetujui pengumpulan dan penggunaan data sesuai kebijakan privasi ini.</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} PT. Rasi Bintang Perkasa. All rights reserved.</p>
        <p>Aplikasi Hadirku - Sistem Absensi Karyawan</p>
    </div>
</body>
</html>
