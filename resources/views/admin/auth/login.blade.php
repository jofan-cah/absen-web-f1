<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Absensi F1</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('F1LOG1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Animated gradient background (right panel) */
        .gradient-panel {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #4c1d95 50%, #7c3aed 75%, #6d28d9 100%);
            background-size: 300% 300%;
            animation: gradientShift 8s ease infinite;
        }
        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating blobs */
        .blob {
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.25;
            position: absolute;
            animation: blobFloat 6s ease-in-out infinite;
        }
        @keyframes blobFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-20px) scale(1.05); }
        }

        /* Input focus glow */
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* Feature card hover */
        .feature-card {
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .feature-card:hover {
            transform: translateX(4px);
            background: rgba(255,255,255,0.12);
        }

        /* Slide-in animation for form */
        .form-panel {
            animation: slideInLeft 0.5s ease-out;
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Right panel slide-in */
        .info-panel {
            animation: slideInRight 0.5s ease-out;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Submit button shimmer */
        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #4338ca, #6d28d9);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
        }
        .btn-login:active {
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="min-h-screen flex">

    {{-- ═══════════════════════════════════════════════════════
         KIRI — Form Login
    ═══════════════════════════════════════════════════════ --}}
    <div class="form-panel w-full lg:w-1/2 xl:w-2/5 flex flex-col justify-center px-6 py-10 sm:px-10 xl:px-16 bg-white relative z-10">

        {{-- Logo Mobile (hanya muncul di mobile, tersembunyi di desktop) --}}
        <div class="flex items-center gap-3 mb-10 lg:hidden">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center shadow-lg">
                <img src="{{ asset('F1LOG1.png') }}" alt="F1" class="w-5 h-5 object-contain" onerror="this.style.display='none'">
            </div>
            <div>
                <span class="text-base font-bold text-gray-900 tracking-wide">ABSENSI F1</span>
                <span class="block text-xs text-gray-400 -mt-0.5">Sistem ISP</span>
            </div>
        </div>

        {{-- Heading --}}
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">
                Selamat datang<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">kembali 👋</span>
            </h1>
            <p class="mt-2 text-sm text-gray-500">Masuk ke panel admin untuk mengelola absensi</p>
        </div>

        {{-- Alert Error --}}
        @if($errors->any())
            <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Alert Success --}}
        @if(session('success'))
            <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.post') }}" class="space-y-5" id="loginForm">
            @csrf

            {{-- NIP --}}
            <div>
                <label for="nip" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    NIP Karyawan
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                    </div>
                    <input
                        id="nip" name="nip" type="text"
                        autocomplete="username" required
                        value="{{ old('nip') }}"
                        placeholder="Masukkan NIP anda"
                        class="input-field w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 bg-gray-50 placeholder-gray-400 focus:outline-none focus:border-indigo-400 focus:bg-white transition-all duration-200"
                    >
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input
                        id="password" name="password" type="password"
                        autocomplete="current-password" required
                        placeholder="Masukkan password anda"
                        class="input-field w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 bg-gray-50 placeholder-gray-400 focus:outline-none focus:border-indigo-400 focus:bg-white transition-all duration-200"
                    >
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                        <svg id="eyeOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="eyeClosed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" id="remember_me" name="remember"
                            class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-checked:bg-indigo-500 rounded-full transition-colors duration-200 peer-focus:ring-2 peer-focus:ring-indigo-300"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                    </div>
                    <span class="text-sm text-gray-600 group-hover:text-gray-800">Ingat saya</span>
                </label>
            </div>

            {{-- Submit Button --}}
            <button type="submit" id="btnLogin"
                class="btn-login w-full py-3.5 px-4 text-white text-sm font-bold rounded-xl shadow-md flex items-center justify-center gap-2">
                <span id="btnText">Masuk ke Panel Admin</span>
                <svg id="btnArrow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
                <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </button>
        </form>

        {{-- Footer --}}
        <p class="mt-10 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Sistem Absensi F1 &mdash; PT. Logistik Murni
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         KANAN — Info / Branding (hanya tampil di lg ke atas)
    ═══════════════════════════════════════════════════════ --}}
    <div class="info-panel hidden lg:flex lg:w-1/2 xl:w-3/5 gradient-panel relative overflow-hidden flex-col items-center justify-center px-12 xl:px-20 py-12">

        {{-- Decorative Blobs --}}
        <div class="blob w-80 h-80 bg-purple-400 top-[-60px] right-[-60px]"></div>
        <div class="blob w-64 h-64 bg-indigo-300 bottom-[-40px] left-[-40px]" style="animation-delay: 2s;"></div>
        <div class="blob w-40 h-40 bg-violet-500 top-1/2 left-1/4" style="animation-delay: 4s;"></div>

        {{-- Decorative grid dots --}}
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;"></div>

        {{-- Content --}}
        <div class="relative z-10 text-white max-w-lg w-full">

            {{-- Logo --}}
            <div class="flex items-center gap-4 mb-10">
                <div class="w-14 h-14 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 shadow-xl">
                    <img src="{{ asset('F1LOG1.png') }}" alt="F1" class="w-8 h-8 object-contain"
                        onerror="this.outerHTML='<svg class=\'w-8 h-8 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\'/></svg>'">
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-wide leading-tight">ABSENSI F1</h2>
                    <p class="text-white/60 text-xs mt-0.5">Sistem Manajemen SDM — ISP</p>
                </div>
            </div>

            {{-- Headline --}}
            <h1 class="text-4xl xl:text-5xl font-extrabold leading-tight mb-4">
                Kelola Absensi<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-200 to-indigo-100">
                    Lebih Mudah
                </span>
            </h1>
            <p class="text-white/70 text-base leading-relaxed mb-10">
                Platform terintegrasi untuk manajemen absensi, jadwal, lembur, izin, dan tunjangan karyawan secara real-time.
            </p>

            {{-- Feature List --}}
            <div class="space-y-3">
                @php
                    $features = [
                        ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',          'label' => 'Absensi real-time dengan GPS & foto',       'color' => 'from-green-400 to-emerald-500'],
                        ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Manajemen jadwal & shift karyawan', 'color' => 'from-blue-400 to-cyan-500'],
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',           'label' => 'Pengajuan lembur & izin online',             'color' => 'from-amber-400 to-orange-500'],
                        ['icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'label' => 'QR Code event & absensi massal', 'color' => 'from-red-400 to-rose-500'],
                        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'Laporan & analitik tunjangan otomatis', 'color' => 'from-purple-400 to-violet-500'],
                    ];
                @endphp

                @foreach($features as $f)
                    <div class="feature-card flex items-center gap-4 bg-white/8 backdrop-blur-sm px-4 py-3 rounded-xl border border-white/10">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $f['color'] }} flex items-center justify-center flex-shrink-0 shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-sm text-white/85 font-medium">{{ $f['label'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4 mt-10 pt-8 border-t border-white/15">
                <div class="text-center">
                    <div class="text-2xl font-extrabold text-white">{{ \App\Models\Karyawan::where('employment_status','active')->count() }}</div>
                    <div class="text-xs text-white/55 mt-0.5">Karyawan Aktif</div>
                </div>
                <div class="text-center border-x border-white/15">
                    <div class="text-2xl font-extrabold text-white">{{ \App\Models\Department::where('is_active',true)->count() }}</div>
                    <div class="text-xs text-white/55 mt-0.5">Department</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-extrabold text-white">{{ \App\Models\Absen::whereDate('date', today())->count() }}</div>
                    <div class="text-xs text-white/55 mt-0.5">Absen Hari Ini</div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pwd       = document.getElementById('password');
        const eyeOpen   = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            pwd.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    });

    // Show loading state on submit
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn     = document.getElementById('btnLogin');
        const text    = document.getElementById('btnText');
        const arrow   = document.getElementById('btnArrow');
        const spinner = document.getElementById('btnSpinner');
        btn.disabled  = true;
        btn.style.opacity = '0.8';
        text.textContent  = 'Memproses...';
        arrow.classList.add('hidden');
        spinner.classList.remove('hidden');
    });

    // Auto focus
    document.getElementById('nip').focus();
</script>

</body>
</html>
