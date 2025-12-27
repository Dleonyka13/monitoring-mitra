<!-- resources/views/components/sidebar.blade.php -->
@props(['active' => ''])

<aside
    class="fixed lg:relative z-30 h-full w-64 transition-all duration-300 ease-in-out bg-gradient-to-b from-blue-900 to-blue-800 text-white">
    <!-- Logo Section -->
    <div class="flex items-center justify-between p-6 border-b border-blue-700">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-white font-bold text-lg leading-tight">Monitoring</h1>
                <p class="text-blue-200 text-xs">Mitra System</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100%-180px)]">
        <!-- Dashboard -->
        <a href="/dashboard"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'dashboard' ? 'bg-white/20 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
            <div
                class="{{ $active === 'dashboard' ? 'bg-white text-blue-600' : 'bg-white/10 text-white' }} w-10 h-10 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Kegiatan Statistik -->
        <a href="/admin/kegiatan-statistik"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'kegiatan-statistik' ? 'bg-white/20 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
            <div
                class="{{ $active === 'kegiatan-statistik' ? 'bg-white text-blue-600' : 'bg-white/10 text-white' }} w-10 h-10 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <span class="font-medium">Kegiatan Statistik</span>
        </a>

        <!-- Manage Users (Admin Only) -->
        <a href="/admin/users" id="manageUsersLink"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'users' ? 'bg-white/20 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
            <div
                class="{{ $active === 'users' ? 'bg-white text-blue-600' : 'bg-white/10 text-white' }} w-10 h-10 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <span class="font-medium">Manage Users</span>
        </a>
    </nav>

    <!-- Sidebar Footer with Logout -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-700">
        <button onclick="logout()"
            class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-red-300 hover:bg-red-500/20 hover:text-red-100 transition-all duration-200">
            <div class="w-10 h-10 rounded-lg bg-red-500/20 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>
            <span class="font-medium">Logout</span>
        </button>
    </div>
</aside>

<script>
    // Check if user is admin and hide manage users if not
    function checkAdminAccess() {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const manageUsersLink = document.getElementById('manageUsersLink');

        if (user.role !== 'admin' && manageUsersLink) {
            manageUsersLink.style.display = 'none';
        }
    }

    // Run on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkAdminAccess);
    } else {
        checkAdminAccess();
    }

    // Logout function
    async function logout() {
        if (!confirm('Apakah Anda yakin ingin logout?')) return;

        const API_BASE = '/api/monitoring-mitra/v1';

        try {
            await window.axios.post(`${API_BASE}/logout`);
        } catch (error) {
            console.log('Logout API error, proceeding with local cleanup');
        }

        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    }
</script>
