<!-- resources/views/dashboard.blade.php -->
<x-layout>
    <x-slot:title>Dashboard - Monitoring Mitra</x-slot:title>

    <style>
        /* Custom Animations */
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse-slow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        @keyframes bounce-slow {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .animate-slide-in-left {
            animation: slideInLeft 0.3s ease-out forwards;
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
        }

        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient-shift 3s ease infinite;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }

        /* Card Hover Effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px -15px rgba(59, 130, 246, 0.3);
        }

        /* Sidebar hover effect */
        .sidebar-item {
            position: relative;
            overflow: hidden;
        }

        .sidebar-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, rgba(255,255,255,0.1), transparent);
            transition: width 0.3s ease;
        }

        .sidebar-item:hover::before {
            width: 100%;
        }

        /* Progress bar animation */
        .progress-bar {
            transition: width 1s ease-in-out;
        }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Dark mode styles */
        .dark .glass {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }

        .dark ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        /* Tooltip */
        .tooltip {
            position: relative;
        }

        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 0.5rem 1rem;
            background: #1e293b;
            color: white;
            font-size: 0.75rem;
            border-radius: 0.5rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
        }

        .tooltip:hover::after {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 5px);
        }

        /* Notification badge pulse */
        .notification-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
    </style>

    <div id="app" class="min-h-screen transition-colors duration-300" :class="darkMode ? 'dark bg-slate-900' : 'bg-gradient-to-br from-blue-50 via-white to-indigo-50'">
        <div class="flex h-screen overflow-hidden">

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
                   class="fixed lg:relative z-30 h-full transition-all duration-300 ease-in-out"
                   :style="darkMode ? 'background: linear-gradient(180deg, #1e3a5f 0%, #0f172a 100%)' : 'background: linear-gradient(180deg, #1e40af 0%, #3b82f6 100%)'">
                
                <!-- Logo Section -->
                <div class="flex items-center justify-between p-4 border-b border-white/10">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg animate-bounce-slow">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white animate-pulse"></div>
                        </div>
                        <div v-show="sidebarOpen" class="animate-slide-in-left">
                            <h1 class="text-white font-bold text-lg leading-tight">Monitoring</h1>
                            <p class="text-blue-200 text-xs">Mitra System</p>
                        </div>
                    </div>
                    <button @click="sidebarOpen = !sidebarOpen" class="text-white/70 hover:text-white transition-colors lg:block hidden">
                        <svg :class="{'rotate-180': !sidebarOpen}" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Menu -->
                <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100%-180px)]">
                    <template v-for="(item, index) in menuItems" :key="index">
                        <a v-if="!item.children" 
                           :href="item.href"
                           :class="[
                               'sidebar-item flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                               item.active 
                                   ? 'bg-white/20 text-white shadow-lg' 
                                   : 'text-blue-100 hover:bg-white/10 hover:text-white'
                           ]"
                           :style="'animation-delay:' + (index * 0.1) + 's'"
                           class="animate-fade-in-up opacity-0">
                            <div :class="item.active ? 'bg-white text-blue-600' : 'bg-white/10 text-white'" 
                                 class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-200">
                                <span v-html="item.icon"></span>
                            </div>
                            <span v-show="sidebarOpen" class="font-medium">text test</span>
                            <span v-if="item.badge && sidebarOpen" 
                                  class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full notification-pulse">
                                text.test
                            </span>
                        </a>

                        <!-- Menu with submenu -->
                        <div v-else>
                            <button @click="item.open = !item.open"
                                    :class="[
                                        'sidebar-item w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                        item.active 
                                            ? 'bg-white/20 text-white shadow-lg' 
                                            : 'text-blue-100 hover:bg-white/10 hover:text-white'
                                    ]"
                                    :style="'animation-delay:' + (index * 0.1) + 's'"
                                    class="animate-fade-in-up opacity-0">
                                <div :class="item.active ? 'bg-white text-blue-600' : 'bg-white/10 text-white'" 
                                     class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-200">
                                    <span v-html="item.icon"></span>
                                </div>
                                <span v-show="sidebarOpen" class="font-medium flex-1 text-left">text test</span>
                                <svg v-show="sidebarOpen" 
                                     :class="{'rotate-180': item.open}" 
                                     class="w-4 h-4 transition-transform duration-200" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div v-show="item.open && sidebarOpen" class="mt-2 ml-4 space-y-1">
                                <a v-for="child in item.children" 
                                   :key="child.name"
                                   :href="child.href"
                                   class="flex items-center space-x-3 px-4 py-2 rounded-lg text-blue-200 hover:text-white hover:bg-white/10 transition-all duration-200">
                                    <span class="w-2 h-2 rounded-full bg-current"></span>
                                    <span class="text-sm"> child.name </span>
                                </a>
                            </div>
                        </div>
                    </template>
                </nav>

                <!-- Sidebar Footer -->
                <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
                    <div v-show="sidebarOpen" class="glass rounded-xl p-3 text-center">
                        <p class="text-blue-100 text-xs">Powered by</p>
                        <p class="text-white font-semibold text-sm">BPS Monitoring</p>
                        <p class="text-blue-200 text-xs">v2.0.0</p>
                    </div>
                </div>
            </aside>

            <!-- Mobile Sidebar Overlay -->
            <div v-show="sidebarOpen" 
                 @click="sidebarOpen = false" 
                 class="fixed inset-0 bg-black/50 z-20 lg:hidden backdrop-blur-sm"></div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">

                <!-- Top Header -->
                <header :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white/80 backdrop-blur-md border-gray-200'" 
                        class="border-b sticky top-0 z-10 transition-colors duration-300">
                    <div class="flex items-center justify-between px-4 lg:px-8 py-4">
                        <!-- Left: Mobile menu & Search -->
                        <div class="flex items-center space-x-4">
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-6 h-6" :class="darkMode ? 'text-white' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>

                            <!-- Search Bar -->
                            <div class="hidden md:flex items-center">
                                <div :class="darkMode ? 'bg-slate-700 border-slate-600' : 'bg-gray-100 border-gray-200'" 
                                     class="relative rounded-xl border transition-all duration-200 focus-within:ring-2 focus-within:ring-blue-500">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" 
                                           placeholder="Search anything..." 
                                           :class="darkMode ? 'bg-transparent text-white placeholder-gray-400' : 'bg-transparent text-gray-700 placeholder-gray-400'"
                                           class="w-64 pl-10 pr-4 py-2 rounded-xl outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Right: Actions -->
                        <div class="flex items-center space-x-3">
                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode" 
                                    class="relative p-2 rounded-xl transition-all duration-300"
                                    :class="darkMode ? 'bg-slate-700 text-yellow-400' : 'bg-blue-100 text-blue-600'">
                                <svg v-if="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <svg v-else class="w-5 h-5 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </button>

                            <!-- Notifications -->
                            <div class="relative">
                                <button @click="notifOpen = !notifOpen" 
                                        class="relative p-2 rounded-xl transition-all duration-200"
                                        :class="darkMode ? 'hover:bg-slate-700 text-gray-300' : 'hover:bg-gray-100 text-gray-600'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full notification-pulse"></span>
                                </button>

                                <!-- Notification Dropdown -->
                                <div v-show="notifOpen" 
                                     @click.away="notifOpen = false"
                                     :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-gray-200'"
                                     class="absolute right-0 mt-2 w-80 rounded-2xl shadow-2xl border overflow-hidden z-50 animate-fade-in-up">
                                    <div :class="darkMode ? 'bg-slate-700' : 'bg-gradient-to-r from-blue-500 to-indigo-600'" 
                                         class="px-4 py-3">
                                        <h3 class="text-white font-semibold">Notifications</h3>
                                        <p class="text-blue-100 text-xs">You have 3 unread messages</p>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <a v-for="notif in notifications" :key="notif.id" 
                                           href="#" 
                                           :class="darkMode ? 'hover:bg-slate-700 border-slate-700' : 'hover:bg-gray-50 border-gray-100'"
                                           class="flex items-start gap-3 p-4 border-b transition-colors">
                                            <div :class="notif.color" class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span v-html="notif.icon"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p :class="darkMode ? 'text-white' : 'text-gray-800'" class="font-medium text-sm">notif.title</p>
                                                <p :class="darkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs truncate">notif.message</p>
                                                <p class="text-blue-500 text-xs mt-1">notif.time</p>
                                            </div>
                                        </a>
                                    </div>
                                    <a href="#" :class="darkMode ? 'bg-slate-700 text-blue-400 hover:bg-slate-600' : 'bg-gray-50 text-blue-600 hover:bg-gray-100'" 
                                       class="block text-center py-3 text-sm font-medium transition-colors">
                                        View all notifications
                                    </a>
                                </div>
                            </div>

                            <!-- User Profile Dropdown -->
                            <div class="relative">
                                <button @click="profileOpen = !profileOpen" 
                                        class="flex items-center space-x-3 p-1.5 rounded-xl transition-all duration-200"
                                        :class="darkMode ? 'hover:bg-slate-700' : 'hover:bg-gray-100'">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg">
                                            <span id="userInitial">U</span>
                                        </div>
                                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></div>
                                    </div>
                                    <div class="hidden md:block text-left">
                                        <p :class="darkMode ? 'text-white' : 'text-gray-700'" class="font-semibold text-sm" id="headerUserName">User Name</p>
                                        <p :class="darkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs" id="headerUserRole">Role</p>
                                    </div>
                                    <svg :class="{'rotate-180': profileOpen}" 
                                         class="w-4 h-4 transition-transform duration-200 hidden md:block" 
                                         :style="darkMode ? 'color: #9ca3af' : 'color: #6b7280'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- Profile Dropdown -->
                                <div v-show="profileOpen" 
                                     @click.away="profileOpen = false"
                                     :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-gray-200'"
                                     class="absolute right-0 mt-2 w-64 rounded-2xl shadow-2xl border overflow-hidden z-50 animate-fade-in-up">
                                    
                                    <!-- User Info Header -->
                                    <div :class="darkMode ? 'bg-slate-700' : 'bg-gradient-to-r from-blue-500 to-indigo-600'" 
                                         class="px-4 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                                                <span id="dropdownUserInitial">U</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-semibold" id="dropdownUserName">User Name</p>
                                                <p class="text-blue-100 text-sm" id="dropdownUserEmail">email@example.com</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Menu Items -->
                                    <div class="py-2">
                                        <a href="#" :class="darkMode ? 'text-gray-300 hover:bg-slate-700' : 'text-gray-700 hover:bg-gray-50'" 
                                           class="flex items-center space-x-3 px-4 py-3 transition-colors">
                                            <div :class="darkMode ? 'bg-slate-600' : 'bg-blue-100'" class="w-8 h-8 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <span>My Profile</span>
                                        </a>
                                        <a href="#" :class="darkMode ? 'text-gray-300 hover:bg-slate-700' : 'text-gray-700 hover:bg-gray-50'" 
                                           class="flex items-center space-x-3 px-4 py-3 transition-colors">
                                            <div :class="darkMode ? 'bg-slate-600' : 'bg-green-100'" class="w-8 h-8 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </div>
                                            <span>Edit Account</span>
                                        </a>
                                        <a href="#" :class="darkMode ? 'text-gray-300 hover:bg-slate-700' : 'text-gray-700 hover:bg-gray-50'" 
                                           class="flex items-center space-x-3 px-4 py-3 transition-colors">
                                            <div :class="darkMode ? 'bg-slate-600' : 'bg-purple-100'" class="w-8 h-8 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                            <span>Settings</span>
                                        </a>
                                        <a href="#" :class="darkMode ? 'text-gray-300 hover:bg-slate-700' : 'text-gray-700 hover:bg-gray-50'" 
                                           class="flex items-center space-x-3 px-4 py-3 transition-colors">
                                            <div :class="darkMode ? 'bg-slate-600' : 'bg-yellow-100'" class="w-8 h-8 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <span>Help Center</span>
                                        </a>
                                    </div>

                                    <!-- Admin Section -->
                                    <div id="adminSection" class="hidden border-t" :class="darkMode ? 'border-slate-700' : 'border-gray-100'">
                                        <div class="py-2">
                                            <p :class="darkMode ? 'text-gray-500' : 'text-gray-400'" class="px-4 py-2 text-xs font-semibold uppercase">Admin</p>
                                            <a href="/admin/users" :class="darkMode ? 'text-gray-300 hover:bg-slate-700' : 'text-gray-700 hover:bg-gray-50'" 
                                               class="flex items-center space-x-3 px-4 py-3 transition-colors">
                                                <div :class="darkMode ? 'bg-slate-600' : 'bg-indigo-100'" class="w-8 h-8 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                    </svg>
                                                </div>
                                                <span>Manage Users</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Logout -->
                                    <div class="border-t py-2" :class="darkMode ? 'border-slate-700' : 'border-gray-100'">
                                        <button @click="logout()" 
                                                class="w-full flex items-center space-x-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                </svg>
                                            </div>
                                            <span class="font-medium">Logout</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto p-4 lg:p-8">
                    <!-- Welcome Section -->
                    <div class="mb-8 animate-fade-in-up">
                        <div :class="darkMode ? 'from-blue-900 to-indigo-900' : 'from-blue-500 to-indigo-600'" 
                             class="bg-gradient-to-r rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden">
                            <!-- Background Pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                    <defs>
                                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                            <circle cx="1" cy="1" r="1" fill="white"/>
                                        </pattern>
                                    </defs>
                                    <rect width="100" height="100" fill="url(#grid)"/>
                                </svg>
                            </div>
                            
                            <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                                <div>
                                    <p class="text-blue-100 mb-2" id="dateTime">Loading...</p>
                                    <h2 class="text-2xl lg:text-3xl font-bold mb-2">
                                        Welcome back, <span id="welcomeUserName">User</span>! 👋
                                    </h2>
                                    <p class="text-blue-100">Here's what's happening with your monitoring today.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button class="px-6 py-3 bg-white text-blue-600 rounded-xl font-semibold hover:bg-blue-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            New Report
                                        </span>
                                    </button>
                                    <button class="px-6 py-3 bg-white/20 text-white rounded-xl font-semibold hover:bg-white/30 transition-all duration-200 backdrop-blur-sm">
                                        View Analytics
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div v-for="(stat, index) in stats" :key="index"
                             :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-gray-100'"
                             class="card-hover rounded-2xl p-6 border shadow-sm animate-fade-in-up opacity-0"
                             :style="'animation-delay:' + ((index + 1) * 0.1) + 's'">
                            <div class="flex items-center justify-between mb-4">
                                <div :class="stat.bgColor" class="w-12 h-12 rounded-xl flex items-center justify-center">
                                    <span :class="stat.iconColor" v-html="stat.icon"></span>
                                </div>
                                <span :class="stat.changeType === 'up' ? 'text-green-500 bg-green-100 dark:bg-green-900/30' : 'text-red-500 bg-red-100 dark:bg-red-900/30'" 
                                      class="text-xs font-semibold px-2 py-1 rounded-full flex items-center gap-1">
                                    <svg v-if="stat.changeType === 'up'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                     stat.change 
                                </span>
                            </div>
                            <h3 :class="darkMode ? 'text-white' : 'text-gray-800'" class="text-2xl font-bold mb-1"> stat.value </h3>
                            <p :class="darkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm"> stat.label </p>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Main Chart -->
                        <div :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-gray-100'" 
                             class="lg:col-span-2 rounded-2xl p-6 border shadow-sm card-hover animate-fade-in-up opacity-0 delay-300">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                                <div>
                                    <h3 :class="darkMode ? 'text-white' : 'text-gray-800'" class="text-lg font-bold">Performance Overview</h3>
                                    <p :class="darkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm">Monthly monitoring statistics</p>
                                </div>
                                <div class="flex gap-2">
                                    <button v-for="period in ['Week', 'Month', 'Year']" :key="period"
                                            @click="selectedPeriod = period"
                                            :class="selectedPeriod === period 
                                                ? 'bg-blue-500 text-white' 
                                                : (darkMode ? 'bg-slate-700 text-gray-300 hover:bg-slate-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200')"
                                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                         period 
                                    </button>
                                </div>
                            </div>
                            <!-- Chart Placeholder -->
                            <div class="h-64 flex items-end justify-between gap-2 px-4">
                                <div v-for="(bar, index) in chartData" :key="index" class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg transition-all duration-500 hover:from-blue-600 hover:to-blue-500"
                                         :style="'height: ' + bar.height + '%'"></div>
                                    <span :class="darkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs"> bar.label </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart / Stats -->
                        <div :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-gray-100'" 
                             class="rounded-2xl p-6 border shadow-sm card-hover animate-fade-in-up opacity-0 delay-400">
                            <h3 :class="darkMode ? 'text-white' : 'text-gray-800'" class="text-lg font-bold mb-6">Mitra by Category</h3>
                            
                            <!-- Donut Chart -->
                            <div class="relative w-40 h-40 mx-auto mb-6">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="40" stroke="#e5e7eb" stroke-width="12" fill="none" :class="darkMode ? 'stroke-slate-700' : ''"/>
                                    <circle cx="50" cy="50" r="40" stroke="#3b82f6" stroke-width="12" fill="none"
                                            stroke-dasharray="251.2" stroke-dashoffset="75.36" class="transition-all duration-1000"/>
                                    <circle cx="50" cy="50" r="40" stroke="#10b981" stroke-width="12" fill="none"
                                            stroke-dasharray="251.2" stroke-dashoffset="188.4" stroke-dasharray="62.8 251.2" class="transition-all duration-1000" style="stroke-dashoffset: -175.84"/>
                                    <circle cx="50" cy="50" r="40" stroke="#f59e0b" stroke-width="12" fill="none"
                                            stroke-dasharray="251.2" stroke-dashoffset="213.52" stroke-dasharray="37.68 251.2" class="transition-all duration-1000" style="stroke-dashoffset: -238.64"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <p :class="darkMode ? 'text-white' : 'text-gray-800'" class="text-2xl font-bold">156</p>
                                        <p :class="darkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">Total</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Legend -->
                            <div class="space-y-3">
                                <div v-for="category in categories" :key="category.name" class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div :class="category.color" class="w-3 h-3 rounded-full"></div>
                                        <span :class="darkMode ? 'text-gray-300' : 'text-gray-600'" class="text-sm"> category.name </span>
                                    </div>
                                    <span :class="darkMode ? 'text-white' : 'text-gray-800'" class="font-semibold"> category.value </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity & Quick Actions -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Recent Activity -->
                        <div :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-gray-100'" 
                             class="rounded-2xl p-6 border shadow-sm card-hover animate-fade-in-up opacity-0 delay-400">
                            <div class="flex justify-between items-center mb-6">
                                <h3 :class="darkMode ? 'text-white' : 'text-gray-800'" class="text-lg font-bold">Recent Activity</h3>
                                <a href="#" class="text-blue-500 text-sm font-medium hover:text-blue-600 transition-colors">View All</a>
                            </div>
                            <div class="space-y-4">
                                <div v-for="activity in recentActivities" :key="activity.id" 
                                     :class="darkMode ? 'hover:bg-slate-700' : 'hover:bg-gray-50'"
                                     class="flex items-start gap-4 p-3 rounded-xl transition-colors cursor-pointer">
                                    <div :class="activity.bgColor" class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <span v-html="activity.icon"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p :class="darkMode ? 'text-white' : 'text-gray-800'" class="font-medium text-sm"> activity.title </p>
                                        <p :class="darkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs mt-1"> activity.description </p>
                                    </div>
                                    <span :class="darkMode ? 'text-gray-500' : 'text-gray-400'" class="text-xs whitespace-nowrap"> activity.time </span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-gray-100'" 
                             class="rounded-2xl p-6 border shadow-sm card-hover animate-fade-in-up opacity-0 delay-500">
                            <h3 :class="darkMode ? 'text-white' : 'text-gray-800'" class="text-lg font-bold mb-6">Quick Actions</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <button v-for="action in quickActions" :key="action.name"
                                        :class="darkMode ? 'bg-slate-700 hover:bg-slate-600 border-slate-600' : 'bg-gray-50 hover:bg-gray-100 border-gray-200'"
                                        class="p-4 rounded-xl border transition-all duration-200 hover:shadow-md group">
                                    <div :class="action.bgColor" class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                        <span v-html="action.icon"></span>
                                    </div>
                                    <p :class="darkMode ? 'text-white' : 'text-gray-800'" class="font-medium text-sm text-center"> action.name </p>
                                </button>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Vue 3 CDN -->
        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        
        <script>
            const { createApp, ref, reactive, onMounted } = Vue;

            const API_BASE = '/api/monitoring-mitra/v1';

            createApp({
                setup() {
                    // State
                    const darkMode = ref(localStorage.getItem('darkMode') === 'true');
                    const sidebarOpen = ref(window.innerWidth >= 1024);
                    const profileOpen = ref(false);
                    const notifOpen = ref(false);
                    const selectedPeriod = ref('Month');

                    // Menu Items
                    const menuItems = ref([
                        {
                            name: 'Dashboard',
                            href: '/dashboard',
                            active: true,
                            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'
                        },
                        {
                            name: 'Monitoring',
                            href: '#',
                            active: false,
                            open: false,
                            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
                            children: [
                                { name: 'Daily Report', href: '/monitoring/daily' },
                                { name: 'Weekly Report', href: '/monitoring/weekly' },
                                { name: 'Monthly Report', href: '/monitoring/monthly' }
                            ]
                        },
                        {
                            name: 'Mitra',
                            href: '/mitra',
                            active: false,
                            badge: '12',
                            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
                        },
                        {
                            name: 'Tasks',
                            href: '/tasks',
                            active: false,
                            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
                        },
                        {
                            name: 'Schedule',
                            href: '/schedule',
                            active: false,
                            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                        },
                        {
                            name: 'Reports',
                            href: '/reports',
                            active: false,
                            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                        },
                        {
                            name: 'Settings',
                            href: '/settings',
                            active: false,
                            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
                        }
                    ]);

                    // Stats Data
                    const stats = ref([
                        {
                            label: 'Total Mitra',
                            value: '156',
                            change: '+12%',
                            changeType: 'up',
                            bgColor: 'bg-blue-100 dark:bg-blue-900/30',
                            iconColor: 'text-blue-500',
                            icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
                        },
                        {
                            label: 'Active Tasks',
                            value: '42',
                            change: '+5%',
                            changeType: 'up',
                            bgColor: 'bg-green-100 dark:bg-green-900/30',
                            iconColor: 'text-green-500',
                            icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2
                                                    {
                            label: 'Active Tasks',
                            value: '42',
                            change: '+5%',
                            changeType: 'up',
                            bgColor: 'bg-green-100 dark:bg-green-900/30',
                            iconColor: 'text-green-500',
                            icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
                        },
                        {
                            label: 'Completed',
                            value: '89%',
                            change: '+8%',
                            changeType: 'up',
                            bgColor: 'bg-purple-100 dark:bg-purple-900/30',
                            iconColor: 'text-purple-500',
                            icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                        },
                        {
                            label: 'Pending Review',
                            value: '18',
                            change: '-3%',
                            changeType: 'down',
                            bgColor: 'bg-orange-100 dark:bg-orange-900/30',
                            iconColor: 'text-orange-500',
                            icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                        }
                    ]);

                    // Chart Data
                    const chartData = ref([
                        { label: 'Jan', height: 45 },
                        { label: 'Feb', height: 62 },
                        { label: 'Mar', height: 78 },
                        { label: 'Apr', height: 55 },
                        { label: 'May', height: 89 },
                        { label: 'Jun', height: 72 },
                        { label: 'Jul', height: 95 },
                        { label: 'Aug', height: 68 },
                        { label: 'Sep', height: 82 },
                        { label: 'Oct', height: 76 },
                        { label: 'Nov', height: 88 },
                        { label: 'Dec', height: 65 }
                    ]);

                    // Categories for Pie Chart
                    const categories = ref([
                        { name: 'Surveyor', value: 70, color: 'bg-blue-500' },
                        { name: 'Enumerator', value: 45, color: 'bg-green-500' },
                        { name: 'Pencacah', value: 25, color: 'bg-yellow-500' },
                        { name: 'Pengawas', value: 16, color: 'bg-purple-500' }
                    ]);

                    // Notifications
                    const notifications = ref([
                        {
                            id: 1,
                            title: 'New Mitra Registered',
                            message: 'Ahmad Fauzi has been registered as new mitra',
                            time: '5 min ago',
                            color: 'bg-blue-100 dark:bg-blue-900/30',
                            icon: '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>'
                        },
                        {
                            id: 2,
                            title: 'Task Completed',
                            message: 'Survey Desa Sukamaju has been completed',
                            time: '1 hour ago',
                            color: 'bg-green-100 dark:bg-green-900/30',
                            icon: '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                        },
                        {
                            id: 3,
                            title: 'Report Submitted',
                            message: 'Monthly report December has been submitted',
                            time: '3 hours ago',
                            color: 'bg-purple-100 dark:bg-purple-900/30',
                            icon: '<svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                        }
                    ]);

                    // Recent Activities
                    const recentActivities = ref([
                        {
                            id: 1,
                            title: 'Data Entry Completed',
                            description: 'Budi completed data entry for Kecamatan Cibiru',
                            time: '2 min ago',
                            bgColor: 'bg-green-100 dark:bg-green-900/30',
                            icon: '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                        },
                        {
                            id: 2,
                            title: 'New Assignment',
                            description: 'Siti was assigned to Survey Ekonomi 2025',
                            time: '15 min ago',
                            bgColor: 'bg-blue-100 dark:bg-blue-900/30',
                            icon: '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>'
                        },
                        {
                            id: 3,
                            title: 'Report Reviewed',
                            description: 'Weekly report has been reviewed by supervisor',
                            time: '1 hour ago',
                            bgColor: 'bg-purple-100 dark:bg-purple-900/30',
                            icon: '<svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
                        },
                        {
                            id: 4,
                            title: 'Payment Processed',
                            description: 'Honor for December has been processed',
                            time: '2 hours ago',
                            bgColor: 'bg-yellow-100 dark:bg-yellow-900/30',
                            icon: '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                        }
                    ]);

                    // Quick Actions
                    const quickActions = ref([
                        {
                            name: 'Add Mitra',
                            bgColor: 'bg-blue-100 dark:bg-blue-900/30',
                            icon: '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>'
                        },
                        {
                            name: 'Create Task',
                            bgColor: 'bg-green-100 dark:bg-green-900/30',
                            icon: '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>'
                        },
                        {
                            name: 'Generate Report',
                            bgColor: 'bg-purple-100 dark:bg-purple-900/30',
                            icon: '<svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                        },
                        {
                            name: 'Export Data',
                            bgColor: 'bg-orange-100 dark:bg-orange-900/30',
                            icon: '<svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>'
                        }
                    ]);

                    // Methods
                    const checkAuth = () => {
                        const token = localStorage.getItem('token');
                        const user = localStorage.getItem('user');

                        if (!token || !user) {
                            window.location.href = '/login';
                            return null;
                        }

                        return JSON.parse(user);
                    };

                    const displayUserInfo = () => {
                        const user = checkAuth();
                        if (!user) return;

                        // Show admin section if user is admin
                        if (user.role === 'admin') {
                            document.getElementById('adminSection').classList.remove('hidden');
                        }

                        // Get user initial
                        const initial = user.name ? user.name.charAt(0).toUpperCase() : 'U';

                        // Update various user displays
                        document.getElementById('userInitial').textContent = initial;
                        document.getElementById('headerUserName').textContent = user.name || 'User';
                        document.getElementById('headerUserRole').textContent = getRoleLabel(user.role);
                        document.getElementById('dropdownUserInitial').textContent = initial;
                        document.getElementById('dropdownUserName').textContent = user.name || 'User';
                        document.getElementById('dropdownUserEmail').textContent = user.email || 'email@example.com';
                        document.getElementById('welcomeUserName').textContent = user.name ? user.name.split(' ')[0] : 'User';
                    };

                    const getRoleLabel = (role) => {
                        const roles = {
                            'mitra': 'Mitra',
                            'pegawai': 'Pegawai BPS',
                            'kepala': 'Kepala',
                            'admin': 'Administrator'
                        };
                        return roles[role] || role;
                    };

                    const updateDateTime = () => {
                        const now = new Date();
                        const options = { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        };
                        document.getElementById('dateTime').textContent = now.toLocaleDateString('id-ID', options);
                    };

                    const logout = async () => {
                        if (!confirm('Apakah Anda yakin ingin logout?')) return;

                        try {
                            await window.axios.post(`${API_BASE}/logout`);
                        } catch (error) {
                            console.log('Logout API error, proceeding with local cleanup');
                        }

                        // Clear local storage
                        localStorage.removeItem('token');
                        localStorage.removeItem('user');
                        
                        // Redirect to login
                        window.location.href = '/login';
                    };

                    // Watch dark mode changes
                    const watchDarkMode = () => {
                        if (darkMode.value) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                        localStorage.setItem('darkMode', darkMode.value);
                    };

                    // Lifecycle
                    onMounted(() => {
                        displayUserInfo();
                        updateDateTime();
                        watchDarkMode();

                        // Update time every minute
                        setInterval(updateDateTime, 60000);

                        // Handle window resize for sidebar
                        window.addEventListener('resize', () => {
                            if (window.innerWidth >= 1024) {
                                sidebarOpen.value = true;
                            }
                        });
                    });

                    // Watch for dark mode changes
                    const toggleDarkMode = () => {
                        darkMode.value = !darkMode.value;
                        watchDarkMode();
                    };

                    return {
                        darkMode,
                        sidebarOpen,
                        profileOpen,
                        notifOpen,
                        selectedPeriod,
                        menuItems,
                        stats,
                        chartData,
                        categories,
                        notifications,
                        recentActivities,
                        quickActions,
                        logout,
                        toggleDarkMode
                    };
                }
            }).mount('#app');
        </script>
    @endpush
</x-layout>
