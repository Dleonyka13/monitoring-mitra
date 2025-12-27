<!-- resources/views/dashboard.blade.php -->
<x-layout>
    <x-slot:title>Dashboard - Monitoring Mitra</x-slot:title>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        .delay-400 {
            animation-delay: 0.4s;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar Component -->
        <x-sidebar active="dashboard" />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header title="Dashboard" />

            <!-- Content -->
            <div class="p-6">
                <!-- Welcome Section -->
                <div
                    class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 mb-6 text-white animate-fade-in-up">
                    <h2 class="text-2xl font-bold mb-2">
                        Selamat Datang, <span id="welcomeUserName">User</span>! 👋
                    </h2>
                    <p class="text-blue-100">Berikut adalah ringkasan monitoring mitra hari ini.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Total Mitra -->
                    <div class="bg-white rounded-xl p-6 shadow-sm card-hover animate-fade-in-up delay-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-green-500 bg-green-100 px-2 py-1 rounded-full">
                                +12%
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-1">156</h3>
                        <p class="text-sm text-gray-500">Total Mitra</p>
                    </div>

                    <!-- Active Activities -->
                    <div class="bg-white rounded-xl p-6 shadow-sm card-hover animate-fade-in-up delay-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-green-500 bg-green-100 px-2 py-1 rounded-full">
                                +5%
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-1">12</h3>
                        <p class="text-sm text-gray-500">Kegiatan Aktif</p>
                    </div>

                    <!-- Completed Tasks -->
                    <div class="bg-white rounded-xl p-6 shadow-sm card-hover animate-fade-in-up delay-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-green-500 bg-green-100 px-2 py-1 rounded-full">
                                89%
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-1">342</h3>
                        <p class="text-sm text-gray-500">Tugas Selesai</p>
                    </div>

                    <!-- Pending Review -->
                    <div class="bg-white rounded-xl p-6 shadow-sm card-hover animate-fade-in-up delay-400">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-orange-500 bg-orange-100 px-2 py-1 rounded-full">
                                18
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-1">18</h3>
                        <p class="text-sm text-gray-500">Menunggu Review</p>
                    </div>
                </div>

                <!-- Charts and Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Activities -->
                    <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Aktivitas Terbaru</h3>
                            <a href="#" class="text-sm text-blue-500 hover:text-blue-600">Lihat Semua</a>
                        </div>

                        <div class="space-y-4">
                            <!-- Activity Item 1 -->
                            <div class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-lg transition">
                                <div
                                    class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm text-gray-800">Data Entry Selesai</p>
                                    <p class="text-xs text-gray-500 mt-1">Budi menyelesaikan entri data Kecamatan Cibiru
                                    </p>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">2 menit lalu</span>
                            </div>

                            <!-- Activity Item 2 -->
                            <div class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-lg transition">
                                <div
                                    class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm text-gray-800">Penugasan Baru</p>
                                    <p class="text-xs text-gray-500 mt-1">Siti ditugaskan ke Survey Ekonomi 2025</p>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">15 menit lalu</span>
                            </div>

                            <!-- Activity Item 3 -->
                            <div class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-lg transition">
                                <div
                                    class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm text-gray-800">Laporan Direview</p>
                                    <p class="text-xs text-gray-500 mt-1">Laporan mingguan telah direview supervisor
                                    </p>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">1 jam lalu</span>
                            </div>

                            <!-- Activity Item 4 -->
                            <div class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-lg transition">
                                <div
                                    class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm text-gray-800">Pembayaran Diproses</p>
                                    <p class="text-xs text-gray-500 mt-1">Honor bulan Desember telah diproses</p>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">2 jam lalu</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-800 mb-6">Statistik Mitra</h3>

                        <div class="space-y-4">
                            <!-- Stat Item 1 -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Surveyor</span>
                                    <span class="text-sm font-semibold text-gray-800">70</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>

                            <!-- Stat Item 2 -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Enumerator</span>
                                    <span class="text-sm font-semibold text-gray-800">45</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 29%"></div>
                                </div>
                            </div>

                            <!-- Stat Item 3 -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Pencacah</span>
                                    <span class="text-sm font-semibold text-gray-800">25</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 16%"></div>
                                </div>
                            </div>

                            <!-- Stat Item 4 -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Pengawas</span>
                                    <span class="text-sm font-semibold text-gray-800">16</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 10%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="mt-6 pt-4 border-t">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-700">Total Mitra</span>
                                <span class="text-2xl font-bold text-gray-800">156</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Display welcome user name
            function displayWelcomeName() {
                const user = JSON.parse(localStorage.getItem('user') || '{}');
                const welcomeElement = document.getElementById('welcomeUserName');

                if (user.name && welcomeElement) {
                    welcomeElement.textContent = user.name.split(' ')[0]; // First name only
                }
            }

            // Check authentication
            function checkAuth() {
                const token = localStorage.getItem('token');
                const user = localStorage.getItem('user');

                if (!token || !user) {
                    window.location.href = '/login';
                    return null;
                }

                return JSON.parse(user);
            }

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                checkAuth();
                displayWelcomeName();
            });
        </script>
    @endpush
</x-layout>
