<!-- resources/views/admin/users.blade.php -->
<x-layout>
    <x-slot:title>User Management - Monitoring Mitra</x-slot:title>

    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-4">
                        <a href="/dashboard" class="text-gray-600 hover:text-gray-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <h1 class="text-xl font-bold text-gray-800">User Management</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span id="userInfo" class="text-gray-700"></span>
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
            <!-- Filter & Search -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Search by name or email..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Role</label>
                        <select id="roleFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="kepala">Kepala</option>
                            <option value="pegawai">Pegawai</option>
                            <option value="mitra">Mitra</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="openAddUserModal()"
                            class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">
                            + Add New User
                        </button>
                    </div>
                </div>

                <!-- Import/Export Actions -->
                <div class="border-t pt-4 mt-4">
                    <div class="flex flex-wrap gap-3">
                        <button onclick="downloadTemplate()"
                            class="flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Download Template
                        </button>

                        <button onclick="openImportModal()"
                            class="flex items-center px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                            Import Users
                        </button>

                        <button onclick="exportUsers()"
                            class="flex items-center px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Export Users
                        </button>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created At</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            <span id="paginationInfo">Showing 0 to 0 of 0 results</span>
                        </div>
                        <div class="flex space-x-2" id="paginationButtons">
                            <!-- Pagination buttons will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Add New User</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="userForm" onsubmit="saveUser(event)">
                <input type="hidden" id="userId">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" id="userName" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" id="userEmail" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-4" id="passwordField">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                    <input type="password" id="userPassword" minlength="8"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (when editing)</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                    <select id="userRole" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="kepala">Kepala</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="mitra">Mitra</option>
                    </select>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Import Users from Excel</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="importForm" onsubmit="importUsers(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Excel File *</label>
                    <input type="file" id="importFile" accept=".xlsx,.xls" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-2">
                        Accepted formats: .xlsx, .xls | Max size: 2MB
                    </p>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                    <h4 class="font-semibold text-blue-800 mb-2">Import Instructions:</h4>
                    <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                        <li>Download the template first if you haven't</li>
                        <li>Fill in user data according to the template format</li>
                        <li>Required columns: Name, Email, Password, Role</li>
                        <li>Role options: mitra, pegawai, kepala, admin</li>
                        <li>Email must be unique</li>
                        <li>Password minimum 8 characters</li>
                    </ul>
                </div>

                <!-- Progress Bar -->
                <div id="importProgress" class="hidden mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Importing...</span>
                        <span id="importProgressText" class="text-sm text-gray-500">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="importProgressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                            style="width: 0%">
                        </div>
                    </div>
                </div>

                <!-- Import Results -->
                <div id="importResults" class="hidden mb-4">
                    <div class="bg-gray-50 border rounded-md p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Import Results:</h4>
                        <div class="flex gap-4 mb-3">
                            <div class="flex-1 bg-green-100 rounded p-3 text-center">
                                <div class="text-2xl font-bold text-green-700" id="successCount">0</div>
                                <div class="text-xs text-green-600">Success</div>
                            </div>
                            <div class="flex-1 bg-red-100 rounded p-3 text-center">
                                <div class="text-2xl font-bold text-red-700" id="failedCount">0</div>
                                <div class="text-xs text-red-600">Failed</div>
                            </div>
                        </div>
                        <div id="importErrors" class="max-h-60 overflow-y-auto"></div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeImportModal()"
                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" id="importButton"
                        class="flex-1 bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600 transition duration-200">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const API_BASE = '/api/monitoring-mitra/v1';
            let currentPage = 1;
            let searchTimeout;

            // Check authentication and admin role
            function checkAuth() {
                const token = localStorage.getItem('token');
                const user = localStorage.getItem('user');

                if (!token || !user) {
                    window.location.href = '/login';
                    return null;
                }

                const userData = JSON.parse(user);

                // Check if user is admin
                if (userData.role !== 'admin') {
                    alert('Access Denied: Only administrators can access this page');
                    window.location.href = '/dashboard';
                    return null;
                }

                return userData;
            }

            // Load users with filters
            async function loadUsers(page = 1) {
                try {
                    const search = document.getElementById('searchInput').value;
                    const role = document.getElementById('roleFilter').value;

                    const params = new URLSearchParams({
                        page: page,
                        per_page: 10
                    });

                    if (search) params.append('search', search);
                    if (role) params.append('role', role);

                    const response = await window.axios.get(`${API_BASE}/users?${params}`);

                    if (response.status === 'success') {
                        displayUsers(response.data.data);
                        displayPagination(response.data.meta, response.data.links);
                        currentPage = page;
                    }
                } catch (error) {
                    console.error('Error loading users:', error);
                    alert('Failed to load users');
                }
            }

            // Display users in table
            function displayUsers(users) {
                const tbody = document.getElementById('usersTableBody');
                const currentUser = JSON.parse(localStorage.getItem('user'));

                if (users.length === 0) {
                    tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No users found
                        </td>
                    </tr>
                `;
                    return;
                }

                tbody.innerHTML = users.map(user => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${user.name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">${user.email}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getRoleBadgeClass(user.role)}">
                            ${getRoleLabel(user.role)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${formatDate(user.created_at)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="editUser('${user.id}')" 
                            class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                        ${user.id !== currentUser.id ? `
                                    <button onclick="deleteUser('${user.id}')" 
                                        class="text-red-600 hover:text-red-900">Delete</button>
                                ` : ''}
                    </td>
                </tr>
            `).join('');
            }

            // Display pagination
            function displayPagination(meta, links) {
                const info = document.getElementById('paginationInfo');
                info.textContent = `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total} results`;

                const buttons = document.getElementById('paginationButtons');
                buttons.innerHTML = '';

                // Previous button
                if (links.prev) {
                    buttons.innerHTML += `
                    <button onclick="loadUsers(${meta.current_page - 1})" 
                        class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">
                        Previous
                    </button>
                `;
                }

                // Page numbers
                for (let i = 1; i <= meta.total_pages; i++) {
                    if (i === 1 || i === meta.total_pages || (i >= meta.current_page - 2 && i <= meta.current_page + 2)) {
                        buttons.innerHTML += `
                        <button onclick="loadUsers(${i})" 
                            class="px-3 py-1 border rounded-md ${i === meta.current_page ? 'bg-blue-500 text-white' : 'border-gray-300 hover:bg-gray-50'}">
                            ${i}
                        </button>
                    `;
                    } else if (i === meta.current_page - 3 || i === meta.current_page + 3) {
                        buttons.innerHTML += '<span class="px-2">...</span>';
                    }
                }

                // Next button
                if (links.next) {
                    buttons.innerHTML += `
                    <button onclick="loadUsers(${meta.current_page + 1})" 
                        class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">
                        Next
                    </button>
                `;
                }
            }

            // Modal functions
            function openAddUserModal() {
                document.getElementById('modalTitle').textContent = 'Add New User';
                document.getElementById('userForm').reset();
                document.getElementById('userId').value = '';
                document.getElementById('userPassword').required = true;
                document.getElementById('userModal').classList.remove('hidden');
            }

            async function editUser(userId) {
                try {
                    const response = await window.axios.get(`${API_BASE}/users/${userId}`);

                    if (response.status === 'success') {
                        const user = response.data;
                        document.getElementById('modalTitle').textContent = 'Edit User';
                        document.getElementById('userId').value = user.id;
                        document.getElementById('userName').value = user.name;
                        document.getElementById('userEmail').value = user.email;
                        document.getElementById('userRole').value = user.role;
                        document.getElementById('userPassword').required = false;
                        document.getElementById('userPassword').value = '';
                        document.getElementById('userModal').classList.remove('hidden');
                    }
                } catch (error) {
                    alert('Failed to load user details');
                }
            }

            function closeModal() {
                document.getElementById('userModal').classList.add('hidden');
                document.getElementById('userForm').reset();
            }

            async function saveUser(event) {
                event.preventDefault();

                const userId = document.getElementById('userId').value;
                const userData = {
                    name: document.getElementById('userName').value,
                    email: document.getElementById('userEmail').value,
                    role: document.getElementById('userRole').value
                };

                const password = document.getElementById('userPassword').value;
                if (password) {
                    userData.password = password;
                }

                try {
                    let response;
                    if (userId) {
                        // Update existing user
                        response = await window.axios.put(`${API_BASE}/users/${userId}`, userData);
                    } else {
                        // Create new user
                        response = await window.axios.post(`${API_BASE}/users`, userData);
                    }

                    if (response.status === 'success') {
                        alert(response.message);
                        closeModal();
                        loadUsers(currentPage);
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Failed to save user');
                }
            }

            async function deleteUser(userId) {
                if (!confirm('Are you sure you want to delete this user?')) return;

                try {
                    const response = await window.axios.delete(`${API_BASE}/users/${userId}`);

                    if (response.status === 'success') {
                        alert(response.message);
                        loadUsers(currentPage);
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Failed to delete user');
                }
            }

            // ========== IMPORT/EXPORT FUNCTIONS ==========

            // Download Excel template
            async function downloadTemplate() {
                try {
                    const token = localStorage.getItem('token');
                    const response = await fetch(`${API_BASE}/users/template/download`, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    if (!response.ok) throw new Error('Failed to download template');

                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `user_import_template_${new Date().getTime()}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    alert('Template downloaded successfully!');
                } catch (error) {
                    console.error('Download error:', error);
                    alert('Failed to download template');
                }
            }

            // Open import modal
            function openImportModal() {
                document.getElementById('importForm').reset();
                document.getElementById('importProgress').classList.add('hidden');
                document.getElementById('importResults').classList.add('hidden');
                document.getElementById('importButton').disabled = false;
                document.getElementById('importModal').classList.remove('hidden');
            }

            // Close import modal
            function closeImportModal() {
                document.getElementById('importModal').classList.add('hidden');
                document.getElementById('importForm').reset();
            }

            // Import users from Excel
            async function importUsers(event) {
                event.preventDefault();

                const fileInput = document.getElementById('importFile');
                const file = fileInput.files[0];

                if (!file) {
                    alert('Please select a file');
                    return;
                }

                // Check file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size exceeds 2MB limit');
                    return;
                }

                const formData = new FormData();
                formData.append('file', file);

                // Show progress
                document.getElementById('importProgress').classList.remove('hidden');
                document.getElementById('importResults').classList.add('hidden');
                document.getElementById('importButton').disabled = true;

                // Simulate progress
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 10;
                    if (progress <= 90) {
                        document.getElementById('importProgressBar').style.width = progress + '%';
                        document.getElementById('importProgressText').textContent = progress + '%';
                    }
                }, 200);

                try {
                    const token = localStorage.getItem('token');
                    const response = await fetch(`${API_BASE}/users/import`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`
                        },
                        body: formData
                    });

                    clearInterval(progressInterval);
                    document.getElementById('importProgressBar').style.width = '100%';
                    document.getElementById('importProgressText').textContent = '100%';

                    const result = await response.json();

                    if (result.status === 'success') {
                        // Show results
                        document.getElementById('successCount').textContent = result.data.success_count;
                        document.getElementById('failedCount').textContent = result.data.failed_count;

                        // Show errors if any
                        const errorsDiv = document.getElementById('importErrors');
                        if (result.data.errors && result.data.errors.length > 0) {
                            errorsDiv.innerHTML = `
                            <div class="mt-3">
                                <h5 class="font-semibold text-red-700 text-sm mb-2">Errors:</h5>
                                <div class="space-y-2 text-xs">
                                    ${result.data.errors.map(error => `
                                                <div class="bg-red-50 border border-red-200 rounded p-2">
                                                    <div class="font-semibold text-red-800">Row ${error.row}: ${error.email}</div>
                                                    <ul class="list-disc list-inside text-red-600 ml-2">
                                                        ${error.errors.map(err => `<li>${err}</li>`).join('')}
                                                    </ul>
                                                </div>
                                            `).join('')}
                                </div>
                            </div>
                        `;
                        } else {
                            errorsDiv.innerHTML =
                                '<p class="text-sm text-green-600 mt-2">All users imported successfully!</p>';
                        }

                        document.getElementById('importProgress').classList.add('hidden');
                        document.getElementById('importResults').classList.remove('hidden');

                        // Reload users table
                        setTimeout(() => {
                            loadUsers(1);
                        }, 1000);
                    } else {
                        throw new Error(result.message || 'Import failed');
                    }
                } catch (error) {
                    clearInterval(progressInterval);
                    console. // Continuation from the truncated part in import function
                    error('Import error:', error);
                    document.getElementById('importProgress').classList.add('hidden');
                    alert(error.message || 'Failed to import users');
                    document.getElementById('importButton').disabled = false;
                }
            }

            // Export users to Excel
            async function exportUsers() {
                try {
                    const search = document.getElementById('searchInput').value;
                    const role = document.getElementById('roleFilter').value;

                    const params = new URLSearchParams();
                    if (search) params.append('search', search);
                    if (role) params.append('role', role);

                    const token = localStorage.getItem('token');
                    const response = await fetch(`${API_BASE}/users/export?${params}`, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    if (!response.ok) throw new Error('Failed to export users');

                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `users_export_${new Date().getTime()}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    alert('Users exported successfully!');
                } catch (error) {
                    console.error('Export error:', error);
                    alert('Failed to export users');
                }
            }

            // Helper functions
            function getRoleBadgeClass(role) {
                const classes = {
                    'admin': 'bg-red-100 text-red-800',
                    'kepala': 'bg-purple-100 text-purple-800',
                    'pegawai': 'bg-blue-100 text-blue-800',
                    'mitra': 'bg-green-100 text-green-800'
                };
                return classes[role] || 'bg-gray-100 text-gray-800';
            }

            function getRoleLabel(role) {
                const labels = {
                    'admin': 'Admin',
                    'kepala': 'Kepala',
                    'pegawai': 'Pegawai',
                    'mitra': 'Mitra'
                };
                return labels[role] || role;
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function logout() {
                if (confirm('Are you sure you want to logout?')) {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                }
            }

            // Event listeners
            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadUsers(1);
                }, 500);
            });

            document.getElementById('roleFilter').addEventListener('change', function() {
                loadUsers(1);
            });

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                const user = checkAuth();
                if (user) {
                    document.getElementById('userInfo').textContent = `Welcome, ${user.name}`;
                    loadUsers(1);
                }
            });
        </script>
    @endpush
</x-layout>
