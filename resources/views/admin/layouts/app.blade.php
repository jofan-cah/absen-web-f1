<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Logistik Murni') }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Improved Sidebar Styles */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.5);
        }

        /* Navigation Items */
        .nav-item {
            color: #6b7280;
            position: relative;
        }

        .nav-item:hover {
            color: #374151;
            background-color: #f3f4f6;
            transform: translateX(2px);
        }

        .nav-item.active {
            color: #1d4ed8;
            background-color: #dbeafe;
            font-weight: 600;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background-color: #1d4ed8;
            border-radius: 0 4px 4px 0;
        }

        /* Navigation Icons */
        .nav-icon {
            background-color: #f3f4f6;
            transition: all 0.2s ease;
        }

        .nav-item:hover .nav-icon {
            background-color: #e5e7eb;
        }

        .nav-item.active .nav-icon {
            background-color: #1d4ed8;
            color: white;
        }

        /* Badges */
        .nav-badge {
            font-size: 0.6875rem;
            font-weight: 600;
            line-height: 1;
        }

        /* Glass Effect */
        .glass-effect {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Critical: Consistent Layout System */
        .main-container {
            min-height: 100vh;
            display: flex;
            position: relative;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            /* Prevent overflow */
            transition: margin-left 0.3s ease-in-out;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            /* Prevent horizontal overflow */
            background-color: #f9fafb;
            position: relative;
        }

        /* Mobile Layout (< 1024px) */
        @media (max-width: 1023px) {
            .sidebar-mobile {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar-mobile.open {
                transform: translateX(0);
            }

            .content-wrapper {
                margin-left: 0 !important;
                width: 100%;
            }

            /* Prevent content overflow on mobile */
            .main-content {
                padding-left: 0;
                padding-right: 0;
            }

            /* Mobile specific utilities */
            .mobile-container {
                padding-left: 1rem;
                padding-right: 1rem;
                max-width: 100%;
                overflow-x: hidden;
            }
        }

        /* Desktop Layout (≥ 1024px) */
        @media (min-width: 1024px) {
            .sidebar-desktop {
                transform: translateX(0);
            }

            .content-wrapper {
                margin-left: 16rem;
                /* 64 * 0.25rem = 16rem */
            }
        }

        /* Sidebar transitions */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }

        /* Overlay styles */
        .sidebar-overlay {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        /* Smooth animations */
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Focus styles for accessibility */
        .focus-ring {
            @apply focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2;
        }

        /* Better button states */
        .btn-transition {
            transition: all 0.2s ease-in-out;
        }

        .btn-transition:hover {
            transform: translateY(-1px);
        }

        .btn-transition:active {
            transform: translateY(0);
        }

        /* Fix page actions responsive */
        .page-actions-container {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Ensure content doesn't exceed viewport */
        .content-container {
            max-width: 100%;
            min-height: 100%;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased">
    <div class="main-container" x-data="{ sidebarOpen: false }" x-init="// Handle window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            sidebarOpen = false;
        }
    });">

        <!-- SIDEBAR -->
        @include('admin.layouts.partials.sidebar')

        <!-- MAIN CONTENT WRAPPER -->
        <div class="content-wrapper">

            <!-- HEADER -->
            @include('admin.layouts.partials.header')

            <!-- PAGE CONTENT -->
            <main class="main-content">
                <!-- Content Container -->
                <div class="content-container">
                    <div class="space-y-6 p-6 lg:p-8">

                        @yield('content')
                    </div>
                </div>
            </main>

        </div>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-black bg-opacity-50 sidebar-overlay lg:hidden" style="display: none;">
        </div>

    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 z-50 bg-white/80 glass-effect hidden items-center justify-center">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mb-4"></div>
            <p class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        // Global helper functions
        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('hidden');
            document.getElementById('loading-overlay').classList.add('flex');
        }

        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
            document.getElementById('loading-overlay').classList.remove('flex');
        }

        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                showLoading();

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.logout') }}';

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                form.appendChild(token);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide loading after page load
            setTimeout(hideLoading, 100);

            // Add smooth scrolling
            document.documentElement.style.scrollBehavior = 'smooth';
        });

        // Global CSRF token for AJAX requests
        window.Laravel = {
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        // Global utility functions
        window.formatCurrency = function(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        };

        window.formatNumber = function(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        };

        window.showToast = function(message, type = 'success') {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            }`;
            toast.textContent = message;

            // Animation
            toast.style.transform = 'translateX(100%)';
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);

            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        };

        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // ESC to close sidebar on mobile
            if (e.key === 'Escape' && window.innerWidth < 1024) {
                const sidebarData = Alpine.$data(document.querySelector('[x-data]'));
                if (sidebarData) {
                    sidebarData.sidebarOpen = false;
                }
            }
        });

        // Handle touch gestures for mobile sidebar
        let startX = null;
        let currentX = null;

        document.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
        });

        document.addEventListener('touchmove', function(e) {
            if (!startX) return;
            currentX = e.touches[0].clientX;
        });

        document.addEventListener('touchend', function(e) {
            if (!startX || !currentX) return;

            const diffX = startX - currentX;
            const threshold = 50;
            const sidebarData = Alpine.$data(document.querySelector('[x-data]'));

            if (!sidebarData) return;

            // Swipe left to close sidebar
            if (diffX > threshold && sidebarData.sidebarOpen) {
                sidebarData.sidebarOpen = false;
            }

            // Swipe right to open sidebar (only from left edge)
            if (diffX < -threshold && startX < 20 && !sidebarData.sidebarOpen) {
                sidebarData.sidebarOpen = true;
            }

            startX = null;
            currentX = null;
        });
    </script>

    @stack('scripts')
</body>

</html>
