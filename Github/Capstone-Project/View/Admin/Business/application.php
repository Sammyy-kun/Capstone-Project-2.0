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
                <a href="<?= getDashboardUrl() ?>" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition-colors">FixMart Admin</a>
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
                <h1 class="text-3xl font-bold text-gray-800">Business Applications</h1>
                <p class="text-gray-400 text-sm mt-1">Review and approve shop applications</p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Total</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ shops.length }}</p>
                <p class="text-xs text-gray-400 mt-1">Applications submitted</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Pending</span>
                    <div class="w-9 h-9 rounded-xl bg-yellow-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ shops.filter(s => !s.status || s.status === 'Pending').length }}</p>
                <p class="text-xs text-gray-400 mt-1">Awaiting review</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Approved</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ shops.filter(s => s.status === 'Approved').length }}</p>
                <p class="text-xs text-gray-400 mt-1">Active partners</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Rejected</span>
                    <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ shops.filter(s => s.status === 'Rejected').length }}</p>
                <p class="text-xs text-gray-400 mt-1">Declined applications</p>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="p-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Business Name</th>
                            <th class="p-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Owner</th>
                            <th class="p-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Email</th>
                            <th class="p-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Status</th>
                            <th class="p-4 text-xs font-bold uppercase text-gray-500 tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="shop in shops" :key="shop.id" class="hover:bg-gray-50 transition">
                            <td class="p-4 text-sm font-medium text-gray-900">{{ shop.business_name || 'N/A' }}</td>
                            <td class="p-4 text-sm text-gray-600">{{ shop.first_name }} {{ shop.last_name }}</td>
                            <td class="p-4 text-sm text-gray-500">{{ shop.email }}</td>
                            <td class="p-4">
                                <span :class="{'bg-yellow-100 text-yellow-700': (!shop.status || shop.status === 'Pending'), 'bg-emerald-100 text-emerald-700': shop.status === 'Approved', 'bg-red-100 text-red-700': shop.status === 'Rejected'}" class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize">
                                    {{ shop.status || 'Pending' }}
                                </span>
                            </td>
                             <td class="p-4">
                                <a :href="'view.php?id=' + shop.id" class="text-emerald-600 hover:text-emerald-800 text-sm font-semibold hover:bg-emerald-50 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </a>
                            </td>
                        </tr>
                            <td colspan="5" class="p-12 text-center text-gray-400">
                                No applications found.
                            </td>
                        </tr>
                    </tbody>
                </table>
        </div>
    </main>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0" style="display: none;" v-show="showModal">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-lg transform transition-all relative z-10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" id="modal-title">
                    Application Details
                </h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Business Name</span>
                        <span class="text-base font-medium text-gray-900">{{ selectedShop.business_name || 'N/A' }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Owner Name</span>
                            <span class="text-sm font-medium text-gray-900">{{ selectedShop.full_name }}</span>
                        </div>
                         <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Status</span>
                            <span :class="{'text-yellow-600': selectedShop.status === 'Pending', 'text-emerald-600': selectedShop.status === 'Approved', 'text-red-600': selectedShop.status === 'Rejected'}" class="text-sm font-bold">
                                {{ selectedShop.status || 'Pending' }}
                            </span>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Email Address</span>
                        <span class="text-sm font-medium text-gray-900">{{ selectedShop.email }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                <!-- Only show actions if Pending -->
                <template v-if="selectedShop.status === 'Pending' || !selectedShop.status">
                    <button @click="confirmAction('Approve')" type="button" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none sm:text-sm transition">
                        Accept Application
                    </button>
                    <button @click="confirmAction('Reject')" type="button" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-white text-base font-medium text-red-600 border-red-200 hover:bg-red-50 focus:outline-none sm:text-sm transition">
                        Reject
                    </button>
                </template>
                <button @click="closeModal" type="button" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm transition" v-if="selectedShop.status !== 'Pending' && selectedShop.status">
                    Close
                </button>
            </div>
        </div>
    </div>
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
                            { name: 'Dashboard', icon: 'dashboard', link: '../Dashboard/dashboard.php', active: false }
                        ]
                    },
                    {
                        title: 'Admin & System Config',
                        isOpen: true,
                        items: [
                            { name: 'Business Apps', icon: 'business', link: '../Business/application.php', active: true },
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
                // Page Specific Data
                shops: [],
                selectedShop: {},
                showModal: false,
                // Notifications
                showNotifications: false,
                notifications: [],
                unreadCount: 0,
                pollInterval: null
            }
        },
        mounted() {
            this.loadProfile();
            this.loadShops();
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

            // Page Specific Checks
             async loadShops() {
                try {
                    const res = await fetch('../../../Controller/business-controller.php?action=list');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.shops = data.data;
                    }
                } catch (e) { console.error(e); }
            },
            openModal(shop) {
                this.selectedShop = shop;
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
                this.selectedShop = {};
            },
            confirmAction(action) {
                if (action === 'Reject') {
                    const reasonsList = [
                        'Incomplete or missing documentation',
                        'Invalid or expired business permit',
                        'Invalid DTI/SEC registration',
                        'Business type not supported on our platform',
                        'Insufficient business information provided',
                        'Duplicate or existing application on record',
                        'Suspicious or inconsistent information',
                        'Business address outside service area',
                    ];
                    const checkboxHtml = reasonsList.map(r =>
                        `<label style="display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:12px;border:1px solid #e5e7eb;background:#f9fafb;cursor:pointer;transition:border-color 0.15s,background 0.15s;"
                            onmouseover="if(!this.querySelector('input').checked){this.style.borderColor='#fca5a5';this.style.background='#fff7f7';}"
                            onmouseout="if(!this.querySelector('input').checked){this.style.borderColor='#e5e7eb';this.style.background='#f9fafb';}"> 
                            <input type="checkbox" class="swal-reason" value="${r}"
                                style="width:16px;height:16px;accent-color:#ef4444;flex-shrink:0;cursor:pointer;"
                                onchange="const l=this.closest('label');l.style.borderColor=this.checked?'#ef4444':'#e5e7eb';l.style.background=this.checked?'#fef2f2':'#f9fafb';">
                            <span style="font-size:13px;color:#374151;font-weight:500;line-height:1.4;">${r}</span>
                        </label>`
                    ).join('');
                    Swal.fire({
                        title: '',
                        html: `
                            <div>
                                <div style="background:#f9fafb;border-bottom:1px solid #f3f4f6;padding:16px 24px;display:flex;align-items:center;gap:12px;">
                                    <div style="width:38px;height:38px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="17" height="17" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </div>
                                    <div style="text-align:left;">
                                        <h3 style="margin:0 0 2px;font-size:16px;font-weight:700;color:#111827;">Reason for Rejection</h3>
                                        <p style="margin:0;font-size:13px;color:#6b7280;">Select all applicable reasons for this application.</p>
                                    </div>
                                </div>
                                <div style="padding:20px 24px;text-align:left;">
                                    <div style="display:flex;flex-direction:column;gap:6px;max-height:210px;overflow-y:auto;margin-bottom:16px;">
                                        ${checkboxHtml}
                                    </div>
                                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                        Additional Notes <span style="font-weight:400;color:#9ca3af;">(optional)</span>
                                    </label>
                                    <textarea id="swal-reject-note" placeholder="Provide any additional context..."
                                        style="width:100%;border:1px solid #e5e7eb;border-radius:12px;padding:12px 14px;font-size:13px;resize:vertical;min-height:80px;box-sizing:border-box;font-family:inherit;outline:none;color:#374151;background:#f9fafb;"
                                        onfocus="this.style.borderColor='#ef4444';this.style.boxShadow='0 0 0 4px rgba(239,68,68,0.08)';"
                                        onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none';"></textarea>
                                </div>
                            </div>`,
                        width: '500px',
                        padding: '0',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#ffffff',
                        confirmButtonText: 'Confirm Rejection',
                        cancelButtonText: 'Cancel',
                        focusConfirm: false,
                        didOpen: (popup) => {
                            popup.style.borderRadius = '24px';
                            popup.style.overflow = 'hidden';
                            const actions = popup.querySelector('.swal2-actions');
                            if (actions) { actions.style.padding = '0 24px 20px'; actions.style.gap = '10px'; actions.style.justifyContent = 'flex-end'; }
                            const cancelBtn = popup.querySelector('.swal2-cancel');
                            if (cancelBtn) { cancelBtn.style.color = '#374151'; cancelBtn.style.border = '1px solid #e5e7eb'; cancelBtn.style.borderRadius = '12px'; cancelBtn.style.fontWeight = '500'; cancelBtn.style.fontSize = '14px'; cancelBtn.style.padding = '10px 18px'; cancelBtn.style.boxShadow = '0 1px 2px rgba(0,0,0,0.05)'; }
                            const confirmBtn = popup.querySelector('.swal2-confirm');
                            if (confirmBtn) { confirmBtn.style.borderRadius = '12px'; confirmBtn.style.fontWeight = '600'; confirmBtn.style.fontSize = '14px'; confirmBtn.style.padding = '10px 22px'; }
                        },
                        preConfirm: () => {
                            const checked = [...document.querySelectorAll('.swal-reason:checked')].map(cb => cb.value);
                            const note = document.getElementById('swal-reject-note').value.trim();
                            if (checked.length === 0 && !note) {
                                Swal.showValidationMessage('Please select at least one reason or provide additional notes.');
                                return false;
                            }
                            return { reasons: checked, note };
                        }
                    }).then(result => {
                        if (result.isConfirmed) this.processAction('Reject', result.value);
                    });
                } else {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to approve this application?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Yes, Approve it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Final Confirmation',
                                text: 'This action cannot be undone. Are you absolutely sure?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#10B981',
                                cancelButtonColor: '#6B7280',
                                confirmButtonText: 'Yes, Proceed!'
                            }).then((finalResult) => {
                                if (finalResult.isConfirmed) this.processAction('Approve');
                            });
                        }
                    });
                }
            },
            async processAction(action, rejectionData = null) {
                const isReject = action === 'Reject';
                // Rejections go through business-controller (has the business_application record)
                // Approvals use user-controller (existing behaviour)
                const url = isReject
                    ? '../../../Controller/business-controller.php?action=reject'
                    : '../../../Controller/user-controller.php?action=approve_shop';
                const payload = isReject
                    ? { id: this.selectedShop.id, rejection_reasons: rejectionData?.reasons ?? [], rejection_note: rejectionData?.note ?? '' }
                    : { user_id: this.selectedShop.id };
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    
                    if (data.status === 'success') {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        );
                        this.selectedShop.status = action === 'Approve' ? 'Approved' : 'Rejected';
                        this.loadShops(); // Refresh list
                        this.closeModal();
                    } else {
                         Swal.fire(
                            'Error!',
                            data.message || 'Something went wrong.',
                            'error'
                        );
                    }
                } catch (e) {
                    console.error(e);
                    Swal.fire('Error!', 'Server request failed.', 'error');
                }
            }
        }
    }).mount('#app');
</script>
</body>
</html>



