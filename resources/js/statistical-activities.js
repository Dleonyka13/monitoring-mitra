// Statistical Activities JavaScript - FIXED VERSION
const API_BASE = "/api/monitoring-mitra/v1";
let currentPage = 1;
let searchTimeout;
let currentUser = null;

// ============================================
// AUTHENTICATION & UTILITIES
// ============================================

function checkAuth() {
    const token = localStorage.getItem("token");
    const user = localStorage.getItem("user");

    if (!token || !user) {
        window.location.href = "/login";
        return null;
    }

    try {
        currentUser = JSON.parse(user);
        return currentUser;
    } catch (error) {
        console.error("Error parsing user data:", error);
        window.location.href = "/login";
        return null;
    }
}

function isAdmin() {
    return currentUser && currentUser.role === "admin";
}

function formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function formatDateTimeLocal(dateString) {
    if (!dateString) return "";
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function showNotification(message, type = "success") {
    const bgColor =
        type === "success"
            ? "bg-green-500"
            : type === "error"
            ? "bg-red-500"
            : "bg-blue-500";
    const notification = document.createElement("div");
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = "0";
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ============================================
// LOAD DATA FUNCTIONS
// ============================================

async function loadSummary() {
    try {
        const token = localStorage.getItem("token");
        const response = await fetch(
            `${API_BASE}/kegiatan-statistik/statistics/summary`,
            {
                method: "GET",
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        const result = await response.json();

        if (response.ok && result.status === "success") {
            const summary = result.data;
            document.getElementById("totalActivities").textContent =
                summary.total || 0;
            document.getElementById("completedActivities").textContent =
                summary.completed || 0;
            document.getElementById("ongoingActivities").textContent =
                summary.ongoing || 0;
            document.getElementById("completionRate").textContent =
                (summary.completion_rate || 0) + "%";
        }
    } catch (error) {
        console.error("Error loading summary:", error);
    }
}

async function loadActivities(page = 1) {
    try {
        const search = document.getElementById("searchInput").value.trim();
        const isDone = document.getElementById("statusFilter").value;
        const orderBy = document.getElementById("sortBy").value;

        const params = new URLSearchParams({
            page: page,
            per_page: 10,
            order_by: orderBy,
            order_direction: "desc",
        });

        if (search) params.append("search", search);
        if (isDone !== "") params.append("is_done", isDone);

        const token = localStorage.getItem("token");
        const response = await fetch(
            `${API_BASE}/kegiatan-statistik?${params}`,
            {
                method: "GET",
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        const result = await response.json();

        if (response.ok && result.status === "success") {
            displayActivities(result.data.data);
            displayPagination(result.data.meta, result.data.links);
            currentPage = page;
        }
    } catch (error) {
        console.error("Error loading activities:", error);
        showNotification("Failed to load activities", "error");
    }
}

// ============================================
// DISPLAY FUNCTIONS
// ============================================

function displayActivities(activities) {
    const tbody = document.getElementById("activitiesTableBody");
    const isAdminUser = isAdmin();

    if (activities.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-500">
                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>No activities found</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = activities
        .map(
            (activity) => `
        <tr class="hover:bg-gray-50 transition duration-150">
            <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">${
                    activity.name
                }</div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-gray-500">
                    <div>${formatDate(activity.start_date)}</div>
                    <div class="text-xs text-gray-400">to ${formatDate(
                        activity.end_date
                    )}</div>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
                ${activity.total_target.toLocaleString()}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
                ${activity.duration_days} days
            </td>
            <td class="px-6 py-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                    activity.is_done
                        ? "bg-green-100 text-green-800"
                        : "bg-yellow-100 text-yellow-800"
                }">
                    ${activity.status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button onclick="window.viewActivity('${activity.id}')" 
                    class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                ${
                    isAdminUser
                        ? `
                    <button onclick="window.editActivity('${activity.id}')" 
                        class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                    <button onclick="window.deleteActivity('${activity.id}')" 
                        class="text-red-600 hover:text-red-900">Delete</button>
                `
                        : ""
                }
            </td>
        </tr>
    `
        )
        .join("");
}

function displayPagination(meta, links) {
    const info = document.getElementById("paginationInfo");
    info.textContent = `Showing ${meta.from || 0} to ${meta.to || 0} of ${
        meta.total
    } results`;

    const buttons = document.getElementById("paginationButtons");
    buttons.innerHTML = "";

    if (links.prev) {
        buttons.innerHTML += `
            <button onclick="window.loadActivities(${meta.current_page - 1})" 
                class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">
                Previous
            </button>
        `;
    }

    for (let i = 1; i <= meta.total_pages; i++) {
        if (
            i === 1 ||
            i === meta.total_pages ||
            (i >= meta.current_page - 2 && i <= meta.current_page + 2)
        ) {
            buttons.innerHTML += `
                <button onclick="window.loadActivities(${i})" 
                    class="px-3 py-1 border rounded-md ${
                        i === meta.current_page
                            ? "bg-blue-500 text-white"
                            : "border-gray-300 hover:bg-gray-50"
                    }">
                    ${i}
                </button>
            `;
        } else if (i === meta.current_page - 3 || i === meta.current_page + 3) {
            buttons.innerHTML += '<span class="px-2">...</span>';
        }
    }

    if (links.next) {
        buttons.innerHTML += `
            <button onclick="window.loadActivities(${meta.current_page + 1})" 
                class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">
                Next
            </button>
        `;
    }
}

// ============================================
// CRUD OPERATIONS
// ============================================

function openAddModal() {
    if (!isAdmin()) {
        showNotification("Only administrators can add activities", "error");
        return;
    }
    document.getElementById("modalTitle").textContent =
        "Add Statistical Activity";
    document.getElementById("activityForm").reset();
    document.getElementById("activityId").value = "";
    document.getElementById("activityModal").classList.remove("hidden");
}

async function editActivity(id) {
    if (!isAdmin()) {
        showNotification("Only administrators can edit activities", "error");
        return;
    }

    try {
        const token = localStorage.getItem("token");
        const response = await fetch(`${API_BASE}/kegiatan-statistik/${id}`, {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        const result = await response.json();

        if (response.ok && result.status === "success") {
            const activity = result.data;
            document.getElementById("modalTitle").textContent =
                "Edit Statistical Activity";
            document.getElementById("activityId").value = activity.id;
            document.getElementById("activityName").value = activity.name;
            document.getElementById("startDate").value = formatDateTimeLocal(
                activity.start_date
            );
            document.getElementById("endDate").value = formatDateTimeLocal(
                activity.end_date
            );
            document.getElementById("totalTarget").value =
                activity.total_target;
            document.getElementById("isDone").value =
                activity.is_done.toString();
            document.getElementById("activityModal").classList.remove("hidden");
        }
    } catch (error) {
        console.error("Error loading activity:", error);
        showNotification("Failed to load activity details", "error");
    }
}

async function viewActivity(id) {
    try {
        const token = localStorage.getItem("token");
        const response = await fetch(`${API_BASE}/kegiatan-statistik/${id}`, {
            method: "GET",
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        const result = await response.json();

        if (response.ok && result.status === "success") {
            const activity = result.data;
            const content = document.getElementById("viewContent");
            content.innerHTML = `
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-500">Activity Name</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900">${
                                activity.name
                            }</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full ${
                            activity.is_done
                                ? "bg-green-100 text-green-800"
                                : "bg-yellow-100 text-yellow-800"
                        }">
                            ${activity.status}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Start Date</h4>
                            <p class="mt-1 text-sm text-gray-900">${formatDate(
                                activity.start_date
                            )}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">End Date</h4>
                            <p class="mt-1 text-sm text-gray-900">${formatDate(
                                activity.end_date
                            )}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Total Target</h4>
                            <p class="mt-1 text-sm text-gray-900">${activity.total_target.toLocaleString()}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Duration</h4>
                            <p class="mt-1 text-sm text-gray-900">${
                                activity.duration_days
                            } days</p>
                        </div>
                    </div>
                    
                    <div class="pt-3 border-t">
                        <h4 class="text-sm font-medium text-gray-500">Created At</h4>
                        <p class="mt-1 text-sm text-gray-900">${formatDate(
                            activity.created_at
                        )}</p>
                    </div>
                    
                    <div class="pt-3 border-t">
                        <h4 class="text-sm font-medium text-gray-500">Last Updated</h4>
                        <p class="mt-1 text-sm text-gray-900">${formatDate(
                            activity.updated_at
                        )}</p>
                    </div>
                </div>
            `;
            document.getElementById("viewModal").classList.remove("hidden");
        }
    } catch (error) {
        console.error("Error loading activity:", error);
        showNotification("Failed to load activity details", "error");
    }
}

function closeModal() {
    document.getElementById("activityModal").classList.add("hidden");
    document.getElementById("activityForm").reset();
}

function closeViewModal() {
    document.getElementById("viewModal").classList.add("hidden");
}

async function saveActivity(event) {
    event.preventDefault();

    if (!isAdmin()) {
        showNotification("Only administrators can save activities", "error");
        return;
    }

    const activityId = document.getElementById("activityId").value;
    const activityData = {
        name: document.getElementById("activityName").value,
        start_date:
            document.getElementById("startDate").value.replace("T", " ") +
            ":00",
        end_date:
            document.getElementById("endDate").value.replace("T", " ") + ":00",
        total_target: parseInt(document.getElementById("totalTarget").value),
        is_done: document.getElementById("isDone").value === "true",
    };

    try {
        const token = localStorage.getItem("token");
        let response;

        if (activityId) {
            // Update existing activity
            response = await fetch(
                `${API_BASE}/kegiatan-statistik/${activityId}`,
                {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`,
                    },
                    body: JSON.stringify(activityData),
                }
            );
        } else {
            // Create new activity
            response = await fetch(`${API_BASE}/kegiatan-statistik`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify(activityData),
            });
        }

        const result = await response.json();

        if (response.ok && result.status === "success") {
            showNotification(
                result.message || "Activity saved successfully!",
                "success"
            );
            closeModal();
            loadActivities(currentPage);
            loadSummary();
        } else {
            throw new Error(result.message || "Failed to save activity");
        }
    } catch (error) {
        console.error("Error saving activity:", error);
        showNotification(error.message || "Failed to save activity", "error");
    }
}

async function deleteActivity(id) {
    if (!isAdmin()) {
        showNotification("Only administrators can delete activities", "error");
        return;
    }

    if (!confirm("Are you sure you want to delete this activity?")) return;

    try {
        const token = localStorage.getItem("token");
        const response = await fetch(`${API_BASE}/kegiatan-statistik/${id}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
            },
        });

        const result = await response.json();

        if (response.ok && result.status === "success") {
            showNotification(
                result.message || "Activity deleted successfully!",
                "success"
            );
            loadActivities(currentPage);
            loadSummary();
        } else {
            throw new Error(result.message || "Failed to delete activity");
        }
    } catch (error) {
        console.error("Error deleting activity:", error);
        showNotification(error.message || "Failed to delete activity", "error");
    }
}

// ============================================
// EXPORT/IMPORT FUNCTIONS
// ============================================

async function downloadTemplate() {
    try {
        const token = localStorage.getItem("token");
        const response = await fetch(
            `${API_BASE}/kegiatan-statistik/template/download`,
            {
                method: "GET",
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        if (!response.ok) throw new Error("Failed to download template");

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `statistical_activity_template_${new Date().getTime()}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        showNotification("Template downloaded successfully!", "success");
    } catch (error) {
        console.error("Download error:", error);
        showNotification("Failed to download template", "error");
    }
}

function openImportModal() {
    if (!isAdmin()) {
        showNotification("Only administrators can import activities", "error");
        return;
    }
    document.getElementById("importForm").reset();
    document.getElementById("importProgress").classList.add("hidden");
    document.getElementById("importResults").classList.add("hidden");
    document.getElementById("importButton").disabled = false;
    document.getElementById("importModal").classList.remove("hidden");
}

function closeImportModal() {
    document.getElementById("importModal").classList.add("hidden");
    document.getElementById("importForm").reset();
}

async function importActivities(event) {
    event.preventDefault();

    const fileInput = document.getElementById("importFile");
    const file = fileInput.files[0];

    if (!file) {
        showNotification("Please select a file", "error");
        return;
    }

    const formData = new FormData();
    formData.append("file", file);

    document.getElementById("importProgress").classList.remove("hidden");
    document.getElementById("importResults").classList.add("hidden");
    document.getElementById("importButton").disabled = true;

    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 10;
        if (progress <= 90) {
            document.getElementById("importProgressBar").style.width =
                progress + "%";
            document.getElementById("importProgressText").textContent =
                progress + "%";
        }
    }, 200);

    try {
        const token = localStorage.getItem("token");
        const response = await fetch(
            `${API_BASE}/kegiatan-statistik/import/excel`,
            {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${token}`,
                },
                body: formData,
            }
        );

        clearInterval(progressInterval);
        document.getElementById("importProgressBar").style.width = "100%";
        document.getElementById("importProgressText").textContent = "100%";

        const result = await response.json();

        if (result.status === "success") {
            document.getElementById("successCount").textContent =
                result.data.success_count || 0;
            document.getElementById("failedCount").textContent =
                result.data.failed_count || 0;

            const errorsDiv = document.getElementById("importErrors");
            if (result.data.errors && result.data.errors.length > 0) {
                errorsDiv.innerHTML = `
                    <div class="mt-3">
                        <h5 class="font-semibold text-red-700 text-sm mb-2">Errors:</h5>
                        <div class="space-y-2 text-xs">
                            ${result.data.errors
                                .map(
                                    (error) => `
                                <div class="bg-red-50 border border-red-200 rounded p-2">
                                    <div class="font-semibold text-red-800">Row ${
                                        error.row
                                    }: ${error.name || "N/A"}</div>
                                    <ul class="list-disc list-inside text-red-600 ml-2">
                                        ${error.errors
                                            .map((err) => `<li>${err}</li>`)
                                            .join("")}
                                    </ul>
                                </div>
                            `
                                )
                                .join("")}
                        </div>
                    </div>
                `;
            } else {
                errorsDiv.innerHTML =
                    '<p class="text-sm text-green-600 mt-2">All activities imported successfully!</p>';
            }

            document.getElementById("importProgress").classList.add("hidden");
            document.getElementById("importResults").classList.remove("hidden");

            showNotification(result.message, "success");

            setTimeout(() => {
                loadActivities(1);
                loadSummary();
            }, 1000);
        } else {
            throw new Error(result.message || "Import failed");
        }
    } catch (error) {
        clearInterval(progressInterval);
        console.error("Import error:", error);
        showNotification(
            "Failed to import activities: " + error.message,
            "error"
        );
        document.getElementById("importProgress").classList.add("hidden");
        document.getElementById("importButton").disabled = false;
    }
}

async function exportActivities() {
    try {
        const token = localStorage.getItem("token");
        const response = await fetch(
            `${API_BASE}/kegiatan-statistik/export/excel`,
            {
                method: "GET",
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        if (!response.ok) throw new Error("Failed to export activities");

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `statistical_activities_export_${new Date().getTime()}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        showNotification("Activities exported successfully!", "success");
    } catch (error) {
        console.error("Export error:", error);
        showNotification("Failed to export activities", "error");
    }
}

// ============================================
// EVENT LISTENERS & INITIALIZATION
// ============================================

document.addEventListener("DOMContentLoaded", function () {
    checkAuth();

    // Hide admin actions if not admin
    if (!isAdmin()) {
        const adminActions = document.getElementById("adminActions");
        const addButtonContainer =
            document.getElementById("addButtonContainer");
        if (adminActions) adminActions.style.display = "none";
        if (addButtonContainer) addButtonContainer.style.display = "none";
    }

    // Search input with debounce
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadActivities(1);
            }, 500);
        });
    }

    // Filter changes
    const statusFilter = document.getElementById("statusFilter");
    if (statusFilter) {
        statusFilter.addEventListener("change", function () {
            loadActivities(1);
        });
    }

    const sortBy = document.getElementById("sortBy");
    if (sortBy) {
        sortBy.addEventListener("change", function () {
            loadActivities(1);
        });
    }

    // Initial load
    loadSummary();
    loadActivities(1);
});

// ============================================
// EXPOSE FUNCTIONS TO GLOBAL SCOPE
// ============================================

// Make all functions accessible globally for inline onclick handlers
window.loadActivities = loadActivities;
window.openAddModal = openAddModal;
window.editActivity = editActivity;
window.viewActivity = viewActivity;
window.deleteActivity = deleteActivity;
window.closeModal = closeModal;
window.closeViewModal = closeViewModal;
window.saveActivity = saveActivity;
window.downloadTemplate = downloadTemplate;
window.openImportModal = openImportModal;
window.closeImportModal = closeImportModal;
window.importActivities = importActivities;
window.exportActivities = exportActivities;
