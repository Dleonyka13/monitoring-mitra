<!-- resources/views/dashboard.blade.php -->
<x-layout>
    <x-slot:title>Dashboard - Monitoring Mitra</x-slot:title>

    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-800">Monitoring Mitra</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span id="userInfo" class="text-gray-700"></span>
                        <!-- Admin Menu -->
                        <div id="adminMenu" class="hidden">
                            <a href="/admin/users"
                                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">
                                Manage Users
                            </a>
                        </div>
                        <button onclick="logout()"
                            class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition duration-200">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">
                        Welcome to Dashboard! 👋
                    </h2>
                    <div id="greeting" class="text-lg text-gray-600 mb-6"></div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
                        <h3 class="text-xl font-semibold text-blue-800 mb-3">User Information</h3>
                        <div id="userDetails" class="text-left space-y-2"></div>
                    </div>

                    <div class="mt-8">
                        <button onclick="testApiMe()"
                            class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition duration-200">
                            Test API /me
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const API_BASE = '/api/monitoring-mitra/v1';

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

            // Display user info
            function displayUserInfo() {
                const user = checkAuth();
                if (!user) return;

                // Show admin menu if user is admin
                if (user.role === 'admin') {
                    document.getElementById('adminMenu').classList.remove('hidden');
                }

                // Update header
                document.getElementById('userInfo').textContent = `${user.name} (${user.role})`;

                // Update greeting
                const greeting = document.getElementById('greeting');
                greeting.innerHTML = `
                <p class="text-2xl font-semibold text-gray-800">Hello, <span class="text-blue-600">${user.name}</span>!</p>
                <p class="text-gray-600 mt-2">Your role: <span class="font-medium text-gray-800">${getRoleLabel(user.role)}</span></p>
            `;

                // Update user details
                const userDetails = document.getElementById('userDetails');
                userDetails.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-semibold text-gray-700">Name:</span>
                        <span class="text-gray-600">${user.name}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Email:</span>
                        <span class="text-gray-600">${user.email}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Role:</span>
                        <span class="text-gray-600">${getRoleLabel(user.role)}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">User ID:</span>
                        <span class="text-gray-600 text-xs">${user.id}</span>
                    </div>
                </div>
            `;
            }

            function getRoleLabel(role) {
                const roles = {
                    'mitra': 'Mitra',
                    'pegawai': 'Pegawai',
                    'kepala': 'Kepala',
                    'admin': 'Admin'
                };
                return roles[role] || role;
            }

            // Test API /me
            async function testApiMe() {
                try {
                    const response = await window.axios.get(`${API_BASE}/me`);

                    if (response.status === 'success') {
                        alert('API Test Success!\n\n' + JSON.stringify(response.data, null, 2));
                        // Update local storage with fresh data
                        localStorage.setItem('user', JSON.stringify(response.data));
                        displayUserInfo();
                    } else {
                        alert('API Test Failed: ' + response.message);
                    }
                } catch (error) {
                    alert('API Test Error: ' + error.message);
                }
            }

            // Logout function
            async function logout() {
                if (!confirm('Are you sure you want to logout?')) return;

                try {
                    const response = await window.axios.post(`${API_BASE}/logout`);

                    // Clear local storage
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');

                    // Redirect to login
                    window.location.href = '/login';
                } catch (error) {
                    // Even if API fails, clear local storage and redirect
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                }
            }

            // Initialize
            displayUserInfo();
        </script>
    @endpush
</x-layout>