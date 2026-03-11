<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('user');
?>
<?php require '../../Layouts/header.php'; ?>
<script src="../../../Public/js/User/sidebar.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/User/sidebar.js') ?>"></script>

<div id="app" v-cloak>
    <!-- Navbar -->
    <header>
        <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200">
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
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span v-if="unreadCount > 0" class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">{{ unreadCount }}</span>
                    </button>
                    <transition name="slide-fade">
                        <div v-show="showNotifications" class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-[100]">
                            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-emerald-50/50">
                                <h3 class="font-bold text-gray-800">Notifications</h3>
                                <button v-if="unreadCount > 0" @click="markAllRead" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold transition">Mark all read</button>
                            </div>
                            <div class="max-h-[400px] overflow-y-auto">
                                <div v-if="notifications.length === 0" class="p-8 text-center text-gray-400">
                                    <p class="text-sm">No notifications yet</p>
                                </div>
                                <div v-else v-for="notif in notifications" :key="notif.id"
                                     @click="handleNotifClick(notif)"
                                     class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer"
                                     :class="{'bg-emerald-50/30': !notif.is_read}">
                                    <div class="flex gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0" v-if="!notif.is_read"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-800 mb-0.5">{{ notif.title }}</p>
                                            <p class="text-xs text-gray-500">{{ notif.message }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <!-- Profile -->
                <div class="relative">
                    <button @click="toggleProfileMenu" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition">
                        <img :src="user.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=e5e7eb&color=374151'" @error="handleImageError" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-200 bg-white">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div v-show="showProfileMenu" class="absolute right-0 top-12 w-44 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-[100]">
                        <a href="../Profile/edit.php" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>
                        <a href="../../Auth/logout.php" class="flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition border-t border-gray-100" onclick="event.preventDefault(); Swal.fire({title:'Logout',text:'Are you sure?',icon:'warning',showCancelButton:true,confirmButtonText:'Yes, logout'}).then(r=>{if(r.isConfirmed)window.location.href=this.href;});">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <transition name="slide-in">
            <aside v-show="sidebarOpen" class="fixed left-0 top-[3.5rem] flex flex-col w-64 h-[calc(100vh-3.5rem)] px-4 py-6 overflow-y-auto bg-white border-r border-gray-200 z-50">
                <div class="flex flex-col justify-between flex-1">
                    <nav>
                        <div v-for="(group, index) in menuGroups" :key="index" class="mb-4">
                            <button @click="toggleMenu(index)" class="w-full flex items-center justify-between px-4 py-2 text-xs uppercase font-bold text-gray-400 tracking-wider">
                                <span>{{ group.title }}</span>
                                <svg :class="{'rotate-180': group.isOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <transition name="slide-fade">
                                <div v-show="group.isOpen" class="mt-2 space-y-1">
                                    <a v-for="item in group.items" :key="item.name" :href="item.link"
                                       :class="item.active ? 'text-gray-700 bg-gray-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-700'"
                                       class="flex items-center px-4 py-2 text-sm transition rounded-md">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path :d="getIcon(item.icon)" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span class="mx-4 font-medium">{{ item.name }}</span>
                                    </a>
                                </div>
                            </transition>
                        </div>
                    </nav>
                </div>
            </aside>
        </transition>
    </header>

    <main class="transition-all duration-300 ease-in-out mt-14 px-4 sm:px-10 py-10" :class="{'lg:ml-64': sidebarOpen}">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">My Orders</h1>
            <p class="text-gray-400 text-sm mt-1">Track and manage your purchases</p>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 4" :key="i" class="bg-gray-100 animate-pulse rounded-2xl h-28"></div>
        </div>

        <!-- Empty -->
        <div v-else-if="orders.length === 0" class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-gray-100 shadow-sm text-gray-400">
            <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-lg font-semibold text-gray-500">No orders yet</p>
            <p class="text-sm mt-1">Start shopping and your orders will appear here.</p>
            <a href="../Products/dashboard.php" class="mt-6 px-6 py-2.5 bg-emerald-500 text-white rounded-xl text-sm font-medium hover:bg-emerald-600 transition shadow-sm">Browse Products</a>
        </div>

        <!-- Orders List -->
        <div v-else class="space-y-4">
            <div v-for="order in orders" :key="order.id"
                 class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4 hover:border-emerald-200 transition-colors">

                <!-- Product Thumbnail -->
                <img :src="order.first_image || 'https://placehold.co/64x64/f3f4f6/9ca3af?text=?'" @error="handleProductImgError"
                     class="w-16 h-16 rounded-xl object-cover border border-gray-100 shrink-0">

                <!-- Order Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-bold text-gray-800 text-sm">Order #{{ order.id }}</p>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold"
                              :class="{
                                'bg-yellow-100 text-yellow-700': order.status === 'Pending',
                                'bg-blue-100 text-blue-700':    order.status === 'Processing',
                                'bg-purple-100 text-purple-700':order.status === 'Shipped',
                                'bg-emerald-100 text-emerald-700': order.status === 'Delivered',
                                'bg-red-100 text-red-600':      order.status === 'Cancelled',
                              }">{{ order.status }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                        {{ order.first_item }}{{ order.item_count > 1 ? ' +' + (order.item_count - 1) + ' more' : '' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ formatDate(order.created_at) }}</p>
                </div>

                <!-- Total & CTA -->
                <div class="text-right shrink-0">
                    <p class="font-bold text-emerald-600">₱{{ fmtMoney(order.total_amount) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ order.item_count }} item{{ order.item_count != 1 ? 's' : '' }}</p>
                    <a :href="'tracking.php?order_id=' + order.id"
                       class="inline-flex items-center gap-1 mt-2 text-xs text-emerald-500 hover:text-emerald-600 font-semibold transition">
                        View Details
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
[v-cloak] { display: none; }
.slide-fade-enter-active, .slide-fade-leave-active { transition: all .2s ease; }
.slide-fade-enter-from, .slide-fade-leave-to { opacity: 0; transform: translateY(-6px); }
.slide-in-enter-active, .slide-in-leave-active { transition: transform .25s ease; }
.slide-in-enter-from, .slide-in-leave-to { transform: translateX(-100%); }
</style>

<script>
const { createApp } = Vue;
createApp({
    data() {
        return {
            sidebarOpen:       window.innerWidth >= 1024,
            showProfileMenu:   false,
            showNotifications: false,
            user:       { name: 'User', image: null },
            menuGroups: typeof calculateActiveUserMenu !== 'undefined'
                        ? calculateActiveUserMenu(JSON.parse(JSON.stringify(userSidebarMenu)))
                        : [],
            notifications: [],
            unreadCount:   0,
            orders:  [],
            loading: true,
        };
    },
    mounted() {
        Promise.all([this.loadProfile(), this.fetchNotifications(), this.loadOrders()])
               .finally(() => this.loading = false);
        window.addEventListener('resize', this.handleResize);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize);
    },
    methods: {
        handleResize() { this.sidebarOpen = window.innerWidth >= 1024; },
        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
        toggleProfileMenu() { this.showProfileMenu = !this.showProfileMenu; },
        toggleNotifications() { this.showNotifications = !this.showNotifications; },
        toggleMenu(i) { this.menuGroups[i].isOpen = !this.menuGroups[i].isOpen; },
        getIcon(n) { return typeof getUserIcon !== 'undefined' ? getUserIcon(n) : ''; },
        handleImageError(e) { e.target.src = 'https://ui-avatars.com/api/?name=User&background=e5e7eb&color=374151'; },
        handleProductImgError(e) { e.target.src = 'https://placehold.co/64x64/f3f4f6/9ca3af?text=?'; },
        fmtMoney(v) { return parseFloat(v).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
        formatDate(dt) {
            return new Date(dt).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        async loadProfile() {
            try {
                const r = await fetch('../../../Controller/user-controller.php?action=get_profile');
                const d = await r.json();
                if (d.status === 'success') { this.user.name = d.data.full_name || d.data.username; this.user.image = d.data.profile_picture; }
            } catch(e) {}
        },
        async fetchNotifications() {
            try {
                const r = await fetch('../../../Controller/notification-controller.php?action=fetch');
                const d = await r.json();
                if (d.status === 'success') { this.notifications = d.data; this.unreadCount = d.unread_count; }
            } catch(e) {}
        },
        async markAllRead() {
            try {
                await fetch('../../../Controller/notification-controller.php?action=mark_all_read');
                this.notifications.forEach(n => n.is_read = 1); this.unreadCount = 0;
            } catch(e) {}
        },
        async handleNotifClick(notif) {
            if (Number(notif.is_read) === 0) {
                const fd = new FormData(); fd.append('id', notif.id);
                await fetch('../../../Controller/notification-controller.php?action=mark_read', { method: 'POST', body: fd });
            }
            if (notif.target_url) window.location.href = notif.target_url;
        },
        async loadOrders() {
            try {
                const r = await fetch('../../../Controller/checkout-controller.php?action=get_orders');
                const d = await r.json();
                if (d.status === 'success') this.orders = d.data;
            } catch(e) {}
        },
    },
}).mount('#app');
</script>
</body>
</html>
