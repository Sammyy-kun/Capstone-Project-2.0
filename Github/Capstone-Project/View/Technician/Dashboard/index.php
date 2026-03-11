<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('technician');
?>
<?php 
require_once '../../Layouts/auth_check.php';
require '../../Layouts/header.php'; 
?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div id="app" v-cloak>
    <!-- Sidebar / Navbar Structure -->
    <header>
       <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200" >
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <img src="../../../Public/pictures/menu.svg" alt="Menu" class="w-6 h-6">
                </button>
                <a href="<?= getDashboardUrl() ?>" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition">FixMart Tech</a>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Status Pill (Desktop) -->
                <div class="hidden md:flex bg-gray-100 rounded-full p-1 border border-gray-200 items-center mr-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold cursor-pointer transition-colors" :class="{'bg-emerald-500 text-white': status==='active', 'text-gray-500 hover:bg-gray-200': status!=='active'}" @click="updateStatus('active')">Online</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold cursor-pointer transition-colors" :class="{'bg-gray-500 text-white': status==='offline', 'text-gray-500 hover:bg-gray-200': status!=='offline'}" @click="updateStatus('offline')">Offline</span>
                </div>

                <span class="text-sm text-gray-700 hidden sm:inline">Hello, <span class="font-medium">{{ user.full_name }}</span></span>
                
                <!-- Notifications -->
                <div class="relative">
                    <button @click="toggleNotifications" class="relative p-2 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span v-if="unreadCount > 0" class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">{{ unreadCount }}</span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div v-show="showNotifications" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-50 border border-gray-100" style="display: none;">
                        <div class="p-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-700">Notifications</h3>
                            <button @click="markAllRead" class="text-xs text-blue-500 hover:text-blue-600 font-medium">Mark all read</button>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <div v-for="notif in notifications" :key="notif.id" :class="{'bg-blue-50': Number(notif.is_read) === 0}" class="p-3 border-b border-gray-100 hover:bg-gray-50 transition relative group">
                                <div @click="markRead(notif)" class="cursor-pointer">
                                    <p class="text-sm font-medium text-gray-800" :class="{'font-bold': Number(notif.is_read) === 0}">{{ notif.title }}</p>
                                    <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ notif.message }}</p>
                                    <p class="text-[10px] text-gray-400 mt-2 flex justify-between items-center">
                                        <span>{{ formatDate(notif.created_at) }}</span>
                                        <span class="uppercase text-[9px] px-1 py-0.5 rounded border" :class="getTypeClass(notif.type)">{{ notif.type }}</span>
                                    </p>
                                </div>
                                <button @click.stop="removeNotification(notif.id)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition p-1 bg-white/80 rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500 text-sm flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <span>No notifications</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Profile Dropdown Trigger -->
                <div class="relative">
                    <button class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition" @click="toggleProfileMenu">
                        <img :src="user.profile_picture || 'https://ui-avatars.com/api/?name=Tech&background=e5e7eb&color=374151'" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                        <svg class="w-4 h-4 text-gray-600 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <!-- Profile Dropdown -->
                    <div v-show="showProfileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl overflow-hidden z-50 border border-gray-100" style="display: none;">
                        <a href="../Profile/edit.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                        <a href="../../Auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50" onclick="event.preventDefault(); const targetUrl = this.href || '../../Auth/logout.php'; Swal.fire({ title: 'Logout', text: 'Are you sure you want to logout?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10B981', cancelButtonColor: '#EF4444', confirmButtonText: 'Yes, logout' }).then((result) => { if (result.isConfirmed) { window.location.href = targetUrl; } });">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <transition name="slide-in">
            <!-- Sidebar with Fixed Footer -->
            <aside v-show="sidebarOpen" class="fixed left-0 top-[3.5rem] flex flex-col w-64 h-[calc(100vh-3.5rem)] bg-white border-r border-gray-200 z-50">
                
                <!-- Scrollable Menu Area -->
                <div class="flex-1 overflow-y-auto py-6 px-4">
                    <nav>
                        <div v-for="(group, index) in menuGroups" :key="index" class="mb-4">
                            <button @click="toggleMenu(index)" class="w-full flex items-center justify-between px-4 py-2 text-md font-medium text-gray-600 hover:text-gray-700 transition-colors">
                                <span class="whitespace-nowrap truncate text-xs uppercase font-bold text-gray-400 tracking-wider">{{ group.title }}</span>
                                <svg :class="{'rotate-180': group.isOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <transition name="slide-fade">
                                <div v-show="group.isOpen" class="mt-2 space-y-1">
                                    <a v-for="item in group.items" :key="item.name" :href="item.link" 
                                       :class="item.active ? 'text-gray-700 bg-gray-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-700'"
                                       class="flex items-center px-4 py-2 text-sm transition-colors duration-300 transform rounded-md justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path :d="getIcon(item.icon)" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <span class="mx-4 font-medium">{{ item.name }}</span>
                                        </div>
                                        <span v-if="item.badge && pendingCount > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ pendingCount }}</span>
                                    </a>
                                </div>
                            </transition>
                        </div>

                         <!-- Mobile Status Toggle in Sidebar -->
                        <div class="md:hidden mt-6 pt-4 border-t border-gray-100">
                            <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Status</p>
                            <div class="flex gap-2">
                                <button @click="updateStatus('active')" :class="{'bg-emerald-500 text-white': status==='active', 'bg-gray-100 text-gray-600': status!=='active'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition">Online</button>
                                <button @click="updateStatus('offline')" :class="{'bg-gray-500 text-white': status==='offline', 'bg-gray-100 text-gray-600': status!=='offline'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition">Offline</button>
                            </div>
                        </div>

                    </nav>
                </div>

                <!-- Fixed Bottom Area -->
                <div class="p-4 border-t border-gray-200 bg-white">
                    <nav class="space-y-1">
                        <!-- Settings Link -->
                        <a href="../Profile/edit.php" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors duration-300 transform rounded-md">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path :d="getIcon('settings')" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="mx-4 font-medium">Settings</span>
                        </a>

                        <!-- Logout Link -->
                        <a :href="logoutLink.link" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-300 transform rounded-md" onclick="event.preventDefault(); const targetUrl = this.href || '../../Auth/logout.php'; Swal.fire({ title: 'Logout', text: 'Are you sure you want to logout?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10B981', cancelButtonColor: '#EF4444', confirmButtonText: 'Yes, logout' }).then((result) => { if (result.isConfirmed) { window.location.href = targetUrl; } });">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path :d="getIcon(logoutLink.icon)" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="mx-4 font-medium">{{ logoutLink.name }}</span>
                        </a>
                    </nav>
                </div>

            </aside>
        </transition>
    </header>

    <main class="flex-1 min-w-0 transition-all duration-300 ease-in-out mt-14 px-6 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div>
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Technician Dashboard</h1>
                <p class="text-gray-500 mt-1">Welcome back, {{ user.full_name }}. Here's your performance at a glance.</p>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Jobs</p>
                        <p class="text-2xl font-bold text-gray-800">{{ jobs.length }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Pending</p>
                        <p class="text-2xl font-bold text-gray-800">{{ pendingCount }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Avg Rating</p>
                        <p class="text-2xl font-bold text-gray-800">{{ stats.average_rating ? Number(stats.average_rating).toFixed(1) : '—' }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Reviews</p>
                        <p class="text-2xl font-bold text-gray-800">{{ stats.total_reviews || 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-800">Performance</h3>
                        <p class="text-sm text-gray-400">Rating trends over time</p>
                    </div>
                    <div class="p-6 h-64">
                        <canvas id="ratingChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-base font-bold text-gray-800">Job Requests</h3>
                        <p class="text-sm text-gray-400">Monthly job volume</p>
                    </div>
                    <div class="p-6 h-64">
                        <canvas id="reviewsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Jobs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Recent Assignments</h3>
                        <p class="text-sm text-gray-400">Your latest job dispatches</p>
                    </div>
                    <span class="text-xs font-semibold bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full">{{ jobs.length }} total</span>
                </div>
                <div class="divide-y divide-gray-50">
                    <div v-for="job in jobs" :key="job.id" class="px-6 py-4 hover:bg-gray-50 transition-colors flex justify-between items-center gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-sm font-bold text-emerald-700 flex-shrink-0">
                                {{ (job.customer_name || 'C').charAt(0) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-semibold text-gray-800 truncate">{{ job.customer_name || 'Customer' }}</h4>
                                <p class="text-sm text-gray-400 truncate">{{ job.address || 'No address' }}</p>
                                <div class="flex gap-2 mt-1">
                                    <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-semibold">{{ job.service_type }}</span>
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ job.created_at }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="../Jobs/index.php" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition flex-shrink-0">View →</a>
                    </div>
                    <div v-if="jobs.length === 0" class="px-6 py-14 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        No jobs assigned yet.
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                showProfileMenu: false,
                user: { full_name: 'Technician', profile_picture: '' }, 
                status: 'offline', 
                menuGroups: [
                    {
                        title: 'Main',
                        isOpen: true,
                        items: [
                            { name: 'Dashboard', icon: 'dashboard', link: '#', active: true }
                        ]
                    },
                    {
                        title: 'Services',
                        isOpen: true,
                        items: [
                            { name: 'Job Requests', icon: 'jobs', link: '../Jobs/index.php', active: false, badge: true },
                            { name: 'My Reviews', icon: 'reviews', link: '../Reviews/index.php', active: false },
                            { name: 'My Schedule', icon: 'schedule', link: '../Schedule/index.php', active: false },
                            { name: 'My Profile', icon: 'settings', link: '../Profile/edit.php', active: false }
                        ]
                    }
                ],
                logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },
                jobs: [],
                reviews: [],
                stats: { average_rating: 0, total_reviews: 0 },
                loading: true,
                showNotifications: false,
                notifications: [],
                unreadCount: 0,
                pollInterval: null
            }
        },
        computed: {
            pendingCount() {
                return this.jobs.filter(j => j.status === 'Pending').length;
            }
        },
        mounted() {
            window.addEventListener('resize', this.handleResize);
            this.initData();
        },
        beforeUnmount() {
            if (this.pollInterval) clearInterval(this.pollInterval);
            window.removeEventListener('resize', this.handleResize);
        },
        methods: {
            handleResize() {
                if (window.innerWidth < 1024) {
                    this.sidebarOpen = false;
                } else {
                    this.sidebarOpen = true;
                }
            },
            toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
            toggleProfileMenu() { this.showProfileMenu = !this.showProfileMenu; },
            toggleMenu(index) { this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen; },
            getIcon(iconName) {
                const icons = {
                    dashboard: 'M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17',
                    jobs: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                    reviews: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                    schedule: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    settings: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                    logout: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'
                };
                return icons[iconName] || '';
            },

            async initData() {
                this.loading = true;
                 await Promise.all([this.loadProfile(), this.loadJobs(), this.loadReviews(), this.fetchNotifications()]);
                 this.initCharts();
                 this.loading = false;
                 this.pollInterval = setInterval(this.fetchNotifications, 30000);
            },
            async loadProfile() {
                try {
                    const res = await fetch('../../../Controller/tech-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user = data.data;
                        this.status = data.data.status || 'offline';
                    }
                } catch (e) { console.error(e); }
            },
            async loadJobs() {
                try {
                    const res = await fetch('../../../Controller/tech-controller.php?action=my_jobs');
                    const data = await res.json();
                    if (data.status === 'success') this.jobs = data.data || [];
                } catch (e) { console.error(e); }
            },
            async loadReviews() {
                 try {
                    const res = await fetch('../../../Controller/review-controller.php?action=get_reviews');
                    const data = await res.json();
                    if (data.status === 'success') this.reviews = data.data || [];
                } catch (e) { console.error(e); }
            },
            updateStatus(newStatus) {
                 this.status = newStatus;
                 fetch('../../../Controller/tech-controller.php?action=update_profile', {
                    method: 'POST',
                    body: JSON.stringify({ status: newStatus })
                });
            },

            // Notifications
             toggleNotifications() {
                this.showNotifications = !this.showNotifications;
            },
            async fetchNotifications() {
                try {
                    const res = await fetch('../../../Controller/notification-controller.php?action=fetch');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.notifications = data.data;
                        this.unreadCount = data.unread_count;
                    }
                } catch(e) { console.error(e); }
            },
            async markRead(notif) {
                if (Number(notif.is_read) === 1) return;
                try {
                    const formData = new FormData();
                    formData.append('id', notif.id);
                    await fetch('../../../Controller/notification-controller.php?action=mark_read', { method: 'POST', body: formData });
                    notif.is_read = 1;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch(e) { console.error(e); }
            },
            async markAllRead() {
                if (this.unreadCount === 0) return;
                try {
                    await fetch('../../../Controller/notification-controller.php?action=mark_all_read');
                    this.notifications.forEach(n => n.is_read = 1);
                    this.unreadCount = 0;
                } catch(e) { console.error(e); }
            },
            async removeNotification(id) {
                if (!confirm('Remove notification?')) return;
                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    await fetch('../../../Controller/notification-controller.php?action=delete', { method: 'POST', body: formData });
                    this.notifications = this.notifications.filter(n => n.id !== id);
                    this.fetchNotifications();
                } catch(e) { console.error(e); }
            },
            formatDate(dateStr) {
                return new Date(dateStr).toLocaleString();
            },
            getTypeClass(type) {
                const classes = {
                    info: 'bg-blue-100 text-blue-600',
                    success: 'bg-green-100 text-green-600',
                    warning: 'bg-yellow-100 text-yellow-600',
                    error: 'bg-red-100 text-red-600'
                };
                return classes[type] || classes.info;
            },
            handleImageError(event) {
                event.target.src = 'https://ui-avatars.com/api/?name=Tech&background=e5e7eb&color=374151';
            },

            initCharts() {
                // Same chart logic as before
                const months = [];
                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                const today = new Date();
                for (let i = 5; i >= 0; i--) {
                    const d = new Date(today.getFullYear(), today.getMonth() - i, 1);
                    months.push(monthNames[d.getMonth()]);
                }

                const jobCounts = [0, 0, 0, 0, 0, 0];
                this.jobs.forEach(job => {
                    const date = new Date(job.created_at);
                    const monthDiff = (today.getMonth() - date.getMonth()) + (12 * (today.getFullYear() - date.getFullYear()));
                    if (monthDiff >= 0 && monthDiff < 6) {
                        jobCounts[5 - monthDiff]++;
                    }
                });

                const ratingSums = [0, 0, 0, 0, 0, 0];
                const ratingCounts = [0, 0, 0, 0, 0, 0];
                this.reviews.forEach(review => {
                    const date = new Date(review.created_at);
                    const monthDiff = (today.getMonth() - date.getMonth()) + (12 * (today.getFullYear() - date.getFullYear()));
                    if (monthDiff >= 0 && monthDiff < 6) {
                        ratingSums[5 - monthDiff] += parseFloat(review.rating);
                        ratingCounts[5 - monthDiff]++;
                    }
                });
                const ratingAvgs = ratingSums.map((sum, i) => ratingCounts[i] ? (sum / ratingCounts[i]).toFixed(1) : 0);

                const ctxRating = document.getElementById('ratingChart');
                if(ctxRating) {
                     new Chart(ctxRating, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Rating',
                                data: ratingAvgs,
                                borderColor: '#10B981',
                                tension: 0.4,
                                fill: false
                            }]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, max: 5 }
                            }
                        }
                    });
                }

                const ctxReviews = document.getElementById('reviewsChart');
                 if(ctxReviews) {
                    new Chart(ctxReviews, {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Requests',
                                data: jobCounts,
                                backgroundColor: '#10B981',
                                borderRadius: 4
                            }]
                        },
                         options: { 
                             responsive: true, 
                             maintainAspectRatio: false,
                             scales: {
                                 y: { beginAtZero: true, ticks: { stepSize: 1 } }
                             }
                         }
                    });
                }
            }
        }
    }).mount('#app');
</script>
</body>
</html>

