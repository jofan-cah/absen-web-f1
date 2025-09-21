<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login - Sistem Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-500 via-purple-600 to-blue-800 min-h-screen">

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">

            <!-- Logo & Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-shield text-3xl text-blue-600"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-white">
                    Admin Login
                </h2>
                <p class="mt-2 text-sm text-blue-100">
                    Sistem Manajemen Absensi Karyawan
                </p>
            </div>

            <!-- Login Form -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p class="text-sm">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- NIP Field -->
                    <div>
                        <label for="nip" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-id-card mr-2"></i>NIP
                        </label>
                        <div class="relative">
                            <input
                                id="nip"
                                name="nip"
                                type="text"
                                autocomplete="username"
                                required
                                value="{{ old('nip') }}"
                                class="appearance-none relative block w-full px-4 py-3 pl-12 border border-white/30 placeholder-gray-400 text-gray-900 rounded-lg bg-white/90 backdrop-blur focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="Masukkan NIP"
                            >
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="appearance-none relative block w-full px-4 py-3 pl-12 pr-12 border border-white/30 placeholder-gray-400 text-gray-900 rounded-lg bg-white/90 backdrop-blur focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="Enter your password"
                            >
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none"
                                >
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember_me"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="remember_me" class="ml-2 block text-sm text-white">
                            Remember me
                        </label>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button
                            type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:scale-[1.02] transition duration-200 shadow-lg"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt group-hover:text-blue-300 transition-colors"></i>
                            </span>
                            Sign in to Admin Panel
                        </button>
                    </div>
                </form>

                <!-- Demo Credentials (Only for development) -->
                @if (app()->environment('local'))
                <div class="mt-6 p-4 bg-yellow-100/20 border border-yellow-300/30 rounded-lg">
                    <p class="text-xs text-yellow-100 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>Demo Credentials:
                    </p>
                    <div class="text-xs text-yellow-100 space-y-1">
                        <p><strong>NIP:</strong> NIP001</p>
                        <p><strong>Password:</strong> password123</p>
                    </div>
                </div>
                @endif

            </div>

            <!-- Footer -->
            <div class="text-center text-white/70 text-sm">
                <p>&copy; {{ date('Y') }} Sistem Absensi. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });

        // Auto-focus on first input
        document.getElementById('nip').focus();

        // Demo credentials click handler (only for development)
        @if (app()->environment('local'))
        document.addEventListener('DOMContentLoaded', function() {
            const demoInfo = document.querySelector('.bg-yellow-100\\/20');
            if (demoInfo) {
                demoInfo.addEventListener('click', function() {
                    document.getElementById('email').value = 'admin@company.com';
                    document.getElementById('password').value = 'password123';
                });
                demoInfo.style.cursor = 'pointer';
                demoInfo.title = 'Click to auto-fill demo credentials';
            }
        });
        @endif
    </script>

</body>
</html>
