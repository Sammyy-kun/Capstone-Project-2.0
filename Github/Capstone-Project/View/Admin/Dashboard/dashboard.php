<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('admin');
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
                <a href="<?= getDashboardUrl() ?>" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition">FixMart Admin</a>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-700">Hello, <span class="font-medium">{{ user.name }}</span></span>
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
                            <button @click="markAllRead" class="text-xs text-emerald-500 hover:text-emerald-600 font-medium">Mark all read</button>
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
                        <img :src="user.image" alt="Profile" class="w-8 h-8 rounded-full object-cover">
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
    </header>

        <transition name="slide-in">
            <!-- Sidebar with Fixed Footer -->
            <aside v-show="sidebarOpen" class="fixed left-0 top-[3.5rem] flex flex-col w-64 h-[calc(100vh-3.5rem)] bg-white border-r border-gray-200 z-50">
                
                <!-- Scrollable Menu Area -->
                <div class="flex-1 overflow-y-auto py-6 px-4">
                    <nav>
                        <div v-for="(group, index) in menuGroups" :key="index" class="mb-4">
                            <button @click="toggleMenu(index)" class="w-full flex items-center justify-between px-4 py-2 text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors mt-6 first:mt-0">
                                <span>{{ group.title }}</span>
                                <svg :class="{'rotate-180': group.isOpen}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <transition name="slide-fade">
                                <div v-show="group.isOpen" class="mt-2 space-y-1">
                                    <a v-for="item in group.items" :key="item.name" :href="item.link" 
                                       :class="item.active ? 'text-gray-700 bg-gray-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-700'"
                                       class="flex items-center px-4 py-2 text-sm transition-colors duration-300 transform rounded-md">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path :d="getIcon(item.icon)" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span class="mx-4 font-medium">{{ item.name }}</span>
                                    </a>
                                </div>
                            </transition>
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
    <main class="transition-all duration-300 ease-in-out mt-14 px-6 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview</h1>
                <p class="text-gray-400 text-sm mt-1">System-wide metrics and quick access</p>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Total Users</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ metrics.users }}</p>
                <p class="text-xs text-gray-400 mt-1">Registered accounts</p>
            </div>
            <!-- Business Partners -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Business Partners</span>
                    <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ metrics.owners }}</p>
                <p class="text-xs text-gray-400 mt-1">Approved shops on platform</p>
            </div>
            <!-- Technicians -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Technicians</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ metrics.technicians }}</p>
                <p class="text-xs text-gray-400 mt-1">Active field technicians</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-base font-bold text-gray-800">Quick Actions</h3>
                <p class="text-xs text-gray-400 mt-0.5">Jump to common admin tasks</p>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="../Business/application.php" class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-100 transition group">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 group-hover:bg-blue-200 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Review Applications</p>
                        <p class="text-xs text-gray-400">Approve or reject shops</p>
                    </div>
                </a>
                <a href="../Accounts/accounts.php" class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 hover:bg-emerald-50 border border-gray-100 hover:border-emerald-100 transition group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-200 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Manage Users</p>
                        <p class="text-xs text-gray-400">Edit roles and access</p>
                    </div>
                </a>
                <a href="../Technicians/index.php" class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 hover:bg-orange-50 border border-gray-100 hover:border-orange-100 transition group">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 group-hover:bg-orange-200 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Technicians</p>
                        <p class="text-xs text-gray-400">Track field technicians</p>
                    </div>
                </a>
                <a href="../Schedule/index.php" class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 hover:bg-purple-50 border border-gray-100 hover:border-purple-100 transition group">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 group-hover:bg-purple-200 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Schedule</p>
                        <p class="text-xs text-gray-400">Manage appointments</p>
                    </div>
                </a>
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
                user: {
                    name: 'Admin',
                    image: null
                },
                menuGroups: [
                    {
                        title: 'Main',
                        isOpen: true,
                        items: [
                            { name: 'Dashboard', icon: 'dashboard', link: '../Dashboard/dashboard.php', active: true }
                        ]
                    },
                    {
                        title: 'Admin & System Config',
                        isOpen: true,
                        items: [
                            { name: 'Business Apps', icon: 'business', link: '../Business/application.php', active: false },
                            { name: 'Accounts', icon: 'users', link: '../Accounts/accounts.php', active: false },
                            { name: 'Schedule', icon: 'schedule', link: '../Schedule/index.php', active: false }
                        ]
                    },
                    {
                        title: 'Reporting & Analytics',
                        isOpen: true,
                        items: [
                            { name: 'Technicians Tracking', icon: 'technicians', link: '../Technicians/index.php', active: false },
                            { name: 'Business Partners', icon: 'partners', link: '../Business/partners.php', active: false }
                        ]
                    }
                ],
                logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },
                metrics: {
                    users: 0,
                    owners: 0,
                    technicians: 0,
                    revenue: '₱0.00'
                },
                showNotifications: false,
                notifications: [],
                unreadCount: 0,
                pollInterval: null
            }
        },
        mounted() {
            this.loadProfile();
            this.loadMetrics();
            this.fetchNotifications();
            this.pollInterval = setInterval(this.fetchNotifications, 30000);
            window.addEventListener('resize', this.handleResize);
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
                    business: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    partners: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    technicians: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    users: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                    schedule: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    settings: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                    logout: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'
                };
                return icons[iconName] || '';
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
                event.target.src = 'https://ui-avatars.com/api/?name=Admin&background=e5e7eb&color=374151';
            },
            async loadProfile() {
                try {
                    const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user.name = data.data.full_name || data.data.username;
                        this.user.image = data.data.profile_picture || this.user.image;
                    }
                } catch (e) { console.error("Failed to load profile", e); }
            },
            async loadMetrics() {
                 this.metrics.users = 5;
                 this.metrics.owners = 2;
                 this.metrics.technicians = 1;
            }
        }
    }).mount('#app');
</script>
</body>
</html>




