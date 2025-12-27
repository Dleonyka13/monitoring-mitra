<!-- resources/views/components/header.blade.php -->
@props(['title' => 'Dashboard'])

<header class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="flex items-center justify-between px-6 py-4">
        <!-- Left: Page Title -->
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
            <p class="text-sm text-gray-500" id="currentDateTime"></p>
        </div>

        <!-- Right: User Profile -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <button class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User Menu -->
            <div class="relative">
                <button class="flex items-center space-x-3 focus:outline-none">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-700" id="headerUserName">User Name</p>
                        <p class="text-xs text-gray-500" id="headerUserRole">Role</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                        <span id="headerUserInitial">U</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</header>

<script>
    // Update current date time
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        const dateTimeElement = document.getElementById('currentDateTime');
        if (dateTimeElement) {
            dateTimeElement.textContent = now.toLocaleDateString('id-ID', options);
        }
    }

    // Display user info in header
    function displayHeaderUserInfo() {
        const user = JSON.parse(localStorage.getItem('user') || '{}');

        if (user.name) {
            const initial = user.name.charAt(0).toUpperCase();
            const nameElement = document.getElementById('headerUserName');
            const roleElement = document.getElementById('headerUserRole');
            const initialElement = document.getElementById('headerUserInitial');

            if (nameElement) nameElement.textContent = user.name;
            if (roleElement) roleElement.textContent = getRoleLabel(user.role);
            if (initialElement) initialElement.textContent = initial;
        }
    }

    function getRoleLabel(role) {
        const roles = {
            'mitra': 'Mitra',
            'pegawai': 'Pegawai BPS',
            'kepala': 'Kepala',
            'admin': 'Administrator'
        };
        return roles[role] || role;
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            displayHeaderUserInfo();
            setInterval(updateDateTime, 60000); // Update every minute
        });
    } else {
        updateDateTime();
        displayHeaderUserInfo();
        setInterval(updateDateTime, 60000);
    }
</script>
