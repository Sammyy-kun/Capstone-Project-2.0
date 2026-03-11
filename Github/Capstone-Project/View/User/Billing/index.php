<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('user');
?>
<?php 
require_once '../../Layouts/auth_check.php';
require '../../Layouts/header.php'; 
?>
<script src="../../../Public/js/User/sidebar.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/User/sidebar.js') ?>"></script>
<div id="app" v-cloak>
    <!-- Sidebar / Navbar Structure -->
    <header>
       <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200" >
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <img src="../../../Public/pictures/menu.svg" alt="Menu" class="w-6 h-6">
                </button>
                <a href="../Dashboard/index.php" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition">FixMart</a>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700">Hello, {{ user.name }}</span>
                
                <!-- Notification Bell -->
                <div class="relative">
                    <button @click="toggleNotifications" class="relative p-2 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span v-if="unreadCount > 0" class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">{{ unreadCount }}</span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <transition name="slide-fade">
                        <div v-show="showNotifications" class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-[100]">
                            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-emerald-50/50">
                                <h3 class="font-bold text-gray-800">Notifications</h3>
                                <button v-if="unreadCount > 0" @click="markAllRead" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold transition">Mark all read</button>
                            </div>
                            <div class="max-h-[400px] overflow-y-auto">
                                <div v-if="notifications.length === 0" class="p-8 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <p class="text-sm">No notifications yet</p>
                                </div>
                                <div v-else v-for="notif in notifications" :key="notif.id" 
                                     @click="handleNotifClick(notif)"
                                     class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer relative group"
                                     :class="{'bg-emerald-50/30': !notif.is_read}">
                                    <div class="flex gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0" v-if="!notif.is_read"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-800 mb-0.5">{{ notif.title }}</p>
                                            <p class="text-xs text-gray-500 leading-relaxed">{{ notif.message }}</p>
                                            <p class="text-[10px] text-gray-400 mt-2 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ notif.time_ago }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                
                <div class="relative">
                    <button id="profileMenuBtn" @click="toggleProfileMenu" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition relative">
                        <img :src="user.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=e5e7eb&color=374151'" @error="handleImageError" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-200 bg-white">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                     <div v-show="showProfileMenu" class="absolute right-0 top-12 w-44 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-[100]">
                        <a href="../Profile/edit.php" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>
                        <a href="../../Auth/logout.php" class="flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition border-t border-gray-100" onclick="event.preventDefault(); const targetUrl = this.href || '../../Auth/logout.php'; Swal.fire({ title: 'Logout', text: 'Are you sure you want to logout?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10B981', cancelButtonColor: '#EF4444', confirmButtonText: 'Yes, logout' }).then((result) => { if (result.isConfirmed) { window.location.href = targetUrl; } });">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

                <transition name="slide-in">
            <aside v-show="sidebarOpen" class="fixed left-0 top-[3.5rem] flex flex-col w-64 h-[calc(100vh-3.5rem)] px-4 py-6 overflow-y-auto bg-white border-r border-gray-200 z-50">
            <div class="flex flex-col justify-between flex-1">
                <nav>
                    <div v-for="(group, index) in menuGroups" :key="index" class="mb-4">
                        <button @click="toggleMenu(index)" class="w-full flex items-center justify-between px-4 py-2 text-xs uppercase font-bold text-gray-400 tracking-wider hover:text-gray-500 transition-colors">
                            <span class="whitespace-nowrap truncate text-xs uppercase font-bold text-gray-400 tracking-wider">{{ group.title }}</span>
                            <svg :class="{'rotate-180': group.isOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="mt-auto pt-6 border-t border-gray-200">
                    <a href="../Profile/edit.php" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-300 transform rounded-md mb-2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="mx-4 font-medium">Settings</span>
                    </a>
                    <a href="../../Auth/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-300 transform rounded-md" onclick="event.preventDefault(); const targetUrl = this.href || '../../Auth/logout.php'; Swal.fire({ title: 'Logout', text: 'Are you sure you want to logout?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10B981', cancelButtonColor: '#EF4444', confirmButtonText: 'Yes, logout' }).then((result) => { if (result.isConfirmed) { window.location.href = targetUrl; } });">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="mx-4 font-medium">Logout</span>
                    </a>
                </div>
            </div>
            </aside>
        </transition>
    </header>

    <main class="transition-all duration-300 ease-in-out mt-14 px-6 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div>
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">My Invoices</h1>
                <p class="text-gray-500 mt-1">Manage your payments and transaction history.</p>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-3 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Total</span>
                        <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ invoices.length }}</p>
                    <p class="text-xs text-gray-400 mt-1">All invoices</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Pending</span>
                        <div class="w-9 h-9 rounded-xl bg-yellow-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ invoices.filter(i=>i.status!=='paid').length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Awaiting payment</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Paid</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ invoices.filter(i=>i.status==='paid').length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Settled</p>
                </div>
            </div>

            <!-- Invoice List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-800">Invoice History</h2>
                    <p class="text-sm text-gray-400">All your billing records</p>
                </div>

                <div v-if="loading" class="px-6 py-14 text-center text-gray-400">Loading invoices...</div>

                <div v-else-if="invoices.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
                    <svg class="w-12 h-12 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <p class="font-medium text-gray-500">No Invoices</p>
                    <p class="text-sm mt-1">You have no pending or past invoices.</p>
                </div>

                <div v-else class="divide-y divide-gray-50">
                    <div v-for="inv in invoices" :key="inv.id"
                         class="px-6 py-5 hover:bg-gray-50 transition-colors flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0"
                                 :class="inv.status === 'paid' ? 'bg-emerald-100' : 'bg-yellow-100'">
                                <svg class="w-5 h-5" :class="inv.status === 'paid' ? 'text-emerald-500' : 'text-yellow-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                    <h3 class="font-bold text-gray-800">Invoice #{{ inv.id }}</h3>
                                    <span v-if="inv.status === 'paid'" class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2.5 py-0.5 rounded-full">Paid</span>
                                    <span v-else class="text-xs font-semibold bg-yellow-100 text-yellow-700 px-2.5 py-0.5 rounded-full">Pending</span>
                                </div>
                                <p class="text-sm text-gray-400 truncate">{{ inv.description }} &bull; Repair #{{ inv.repair_id }}</p>
                                <p class="text-xs text-gray-300 mt-0.5">{{ new Date(inv.created_at).toLocaleDateString() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 flex-shrink-0">
                            <span class="text-xl font-bold text-gray-800">₱{{ parseFloat(inv.total_amount).toFixed(2) }}</span>
                            <button v-if="inv.status !== 'paid'" @click="openPayment(inv)"
                                    class="inline-flex items-center gap-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm shadow-emerald-500/25 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                Pay Now
                            </button>
                            <span v-else class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 bg-emerald-50 px-4 py-2.5 rounded-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Settled
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Payment Modal -->
    <div v-if="selectedInvoice" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-md p-8">
            <div class="flex items-center justify-between mb-7">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Pay Invoice #{{ selectedInvoice.id }}</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Complete your payment below</p>
                </div>
                <button @click="selectedInvoice = null" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="mb-6 bg-emerald-50 border border-emerald-100 rounded-2xl p-5 text-center">
                <p class="text-xs text-emerald-600 uppercase tracking-widest font-semibold mb-1">Amount Due</p>
                <p class="text-4xl font-bold text-emerald-600">₱{{ parseFloat(selectedInvoice.total_amount).toFixed(2) }}</p>
            </div>

            <div class="space-y-2 mb-6">
                <label class="text-sm font-semibold text-gray-700 ml-1">Payment Method</label>
                <select v-model="paymentMethod"
                        class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm">
                    <option value="cash">Cash on Hand</option>
                    <option value="card">Credit / Debit Card</option>
                    <option value="online">GCash / E-Wallet</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button @click="selectedInvoice = null"
                        class="flex-1 py-3.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition-all">
                    Cancel
                </button>
                <button @click="processPayment" :disabled="processing"
                        class="flex-1 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <span v-if="processing" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                    {{ processing ? 'Processing...' : 'Confirm Payment' }}
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
                    name: 'User',
                    image: null
                },
                loading: true,
                invoices: [],
                selectedInvoice: null,
                paymentMethod: 'cash',
                processing: false,
                notifications: [],
                unreadCount: 0,
                showNotifications: false,
                pollInterval: null,
                menuGroups: typeof calculateActiveUserMenu !== "undefined" ? calculateActiveUserMenu(JSON.parse(JSON.stringify(userSidebarMenu))) : []
            }
        },
        mounted() {
            this.loadProfile();
            this.fetchNotifications();
            this.loadInvoices();
            window.addEventListener('resize', this.handleResize);
        },
        beforeUnmount() {
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
            getIcon(iconName) { return typeof getUserIcon !== "undefined" ? getUserIcon(iconName) : ""; },
            
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
                    const res = await fetch('../../../Controller/notification-controller.php?action=mark_all_read');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.notifications.forEach(n => n.is_read = 1);
                        this.unreadCount = 0;
                    }
                } catch(e) { console.error(e); }
            },
            async handleNotifClick(notif) {
                if (Number(notif.is_read) === 0) {
                    const formData = new FormData();
                    formData.append('id', notif.id);
                    await fetch('../../../Controller/notification-controller.php?action=mark_read', { method: 'POST', body: formData });
                }
                if (notif.target_url) {
                    window.location.href = notif.target_url;
                }
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
            
            // Invoices
            async loadInvoices() {
                try {
                    const res = await fetch('../../../Controller/billing-controller.php?action=my_invoices');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.invoices = data.data;
                    }
                } catch (e) { console.error(e); } 
                finally { this.loading = false; }
            },
            openPayment(invoice) {
                this.selectedInvoice = invoice;
                this.paymentMethod = 'cash';
            },
            async processPayment() {
                this.processing = true;
                
                try {
                    const res = await fetch('../../../Controller/billing-controller.php?action=pay', {
                        method: 'POST',
                        body: JSON.stringify({
                            invoice_id: this.selectedInvoice.id,
                            amount: this.selectedInvoice.total_amount,
                            method: this.paymentMethod
                        })
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Successful!',
                            text: 'Your payment has been processed.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        this.selectedInvoice = null;
                        this.loadInvoices();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Payment Failed',
                            text: data.message
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "Error processing payment."
                    });
                } finally {
                    this.processing = false;
                }
            }
        }
    }).mount('#app');
</script>
</body>
</html>

