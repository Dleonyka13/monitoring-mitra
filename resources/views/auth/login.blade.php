<!-- resources/views/auth/login.blade.php -->
<x-layout>
    <x-slot:title>Login - Monitoring Mitra</x-slot:title>

    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Monitoring Mitra</h2>

                <!-- Tab Navigation -->
                <div class="flex mb-6 border-b">
                    <button onclick="switchTab('login')" id="loginTab"
                        class="flex-1 py-2 px-4 text-center font-medium border-b-2 border-blue-500 text-blue-500">
                        Login
                    </button>
                    <button onclick="switchTab('register')" id="registerTab"
                        class="flex-1 py-2 px-4 text-center font-medium text-gray-500 hover:text-gray-700">
                        Register
                    </button>
                </div>

                <!-- Alert Messages -->
                <div id="alertMessage" class="hidden mb-4 p-3 rounded"></div>

                <!-- Login Form -->
                <form id="loginForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                        Login
                    </button>
                </form>

                <!-- Register Form -->
                <form id="registerForm" class="space-y-4 hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Role</option>
                            <option value="mitra">Mitra</option>
                            <option value="pegawai">Pegawai</option>
                            <option value="kepala">Kepala</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                        Register
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const API_BASE = '/api/monitoring-mitra/v1';

            function switchTab(tab) {
                const loginForm = document.getElementById('loginForm');
                const registerForm = document.getElementById('registerForm');
                const loginTab = document.getElementById('loginTab');
                const registerTab = document.getElementById('registerTab');

                if (tab === 'login') {
                    loginForm.classList.remove('hidden');
                    registerForm.classList.add('hidden');
                    loginTab.classList.add('border-blue-500', 'text-blue-500');
                    loginTab.classList.remove('text-gray-500');
                    registerTab.classList.remove('border-blue-500', 'text-blue-500');
                    registerTab.classList.add('text-gray-500');
                } else {
                    loginForm.classList.add('hidden');
                    registerForm.classList.remove('hidden');
                    registerTab.classList.add('border-blue-500', 'text-blue-500');
                    registerTab.classList.remove('text-gray-500');
                    loginTab.classList.remove('border-blue-500', 'text-blue-500');
                    loginTab.classList.add('text-gray-500');
                }
                hideAlert();
            }

            function showAlert(message, type = 'error') {
                const alert = document.getElementById('alertMessage');
                alert.className =
                    `mb-4 p-3 rounded ${type === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`;
                alert.textContent = message;
                alert.classList.remove('hidden');
            }

            function hideAlert() {
                document.getElementById('alertMessage').classList.add('hidden');
            }

            // Login Handler
            document.getElementById('loginForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData);

                try {
                    const response = await window.axios.post(`${API_BASE}/login`, data);

                    if (response.status === 'success') {
                        localStorage.setItem('token', response.data.access_token);
                        localStorage.setItem('user', JSON.stringify(response.data.user));
                        showAlert('Login successful! Redirecting...', 'success');
                        setTimeout(() => window.location.href = '/dashboard', 1000);
                    } else {
                        showAlert(response.message || 'Login failed');
                    }
                } catch (error) {
                    showAlert('An error occurred. Please try again.');
                }
            });

            // Register Handler
            document.getElementById('registerForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData);

                try {
                    const response = await window.axios.post(`${API_BASE}/register`, data);

                    if (response.status === 'success') {
                        localStorage.setItem('token', response.data.access_token);
                        localStorage.setItem('user', JSON.stringify(response.data.user));
                        showAlert('Registration successful! Redirecting...', 'success');
                        setTimeout(() => window.location.href = '/dashboard', 1000);
                    } else {
                        const errorMsg = response.data?.errors ?
                            Object.values(response.data.errors).flat().join(', ') :
                            response.message;
                        showAlert(errorMsg || 'Registration failed');
                    }
                } catch (error) {
                    showAlert('An error occurred. Please try again.');
                }
            });

            // Check if already logged in
            if (localStorage.getItem('token')) {
                window.location.href = '/dashboard';
            }
        </script>
    @endpush
</x-layout>
