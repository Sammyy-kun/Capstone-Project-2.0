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

                <!-- Profile Menu -->
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

    <main class="transition-all duration-300 ease-in-out mt-14 px-4 sm:px-10 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Shopping Cart</h1>
                <p class="text-gray-400 text-sm mt-1">Review your selected items</p>
            </div>

            <!-- Empty Cart -->
            <div v-if="!loading && cartItems.length === 0" class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-gray-100 shadow-sm text-gray-400">
                <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-lg font-semibold text-gray-500">Your cart is empty</p>
                <p class="text-sm mt-1 text-gray-400">Browse stores and add some products!</p>
                <a href="../Products/dashboard.php" class="mt-6 px-6 py-2.5 bg-emerald-500 text-white rounded-xl text-sm font-medium hover:bg-emerald-600 transition shadow-sm">Browse Products</a>
            </div>

            <div v-else-if="loading" class="space-y-4">
                <div v-for="i in 3" :key="i" class="bg-gray-100 animate-pulse rounded-2xl h-24"></div>
            </div>

            <!-- Cart Items & Summary -->
            <div v-else class="flex flex-col lg:flex-row gap-8 items-start">
                <!-- Cart Items List -->
                <div class="flex-1 min-w-0 space-y-3">
                    <!-- Select All Row -->
                    <div class="flex items-center justify-between bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-3">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" :checked="allSelected" @change="toggleSelectAll"
                                   class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-sm font-semibold text-gray-700">Select All</span>
                            <span class="text-xs text-gray-400">({{ selectedCount }} of {{ cartItems.length }} selected)</span>
                        </label>
                        <button v-if="selectedCount > 0" @click="removeSelected"
                                class="text-xs text-red-400 hover:text-red-600 font-medium transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Remove Selected
                        </button>
                    </div>

                    <div v-for="item in cartItems" :key="item.id"
                         :class="item.selected ? 'border-emerald-200 bg-emerald-50/20' : 'border-gray-100'"
                         class="bg-white rounded-2xl border shadow-sm p-4 flex items-center gap-4 transition-colors">
                        <!-- Checkbox -->
                        <input type="checkbox" v-model="item.selected"
                               class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 cursor-pointer shrink-0">
                        <!-- Product Image -->
                        <img :src="item.image_url || 'https://via.placeholder.com/80x80/f3f4f6/6b7280?text=?'"
                             @error="handleProductImgError"
                             class="w-16 h-16 rounded-xl object-cover border border-gray-100 shrink-0">
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ item.product_name }}</p>
                            <p class="text-emerald-600 font-bold mt-0.5">₱{{ parseFloat(item.price).toLocaleString('en-PH', {minimumFractionDigits: 2}) }}</p>
                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-2 mt-2">
                                <button @click="changeQty(item, item.quantity - 1)"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 transition text-gray-700 font-bold text-base">−</button>
                                <span class="text-sm font-semibold text-gray-800 min-w-[24px] text-center">{{ item.quantity }}</span>
                                <button @click="changeQty(item, item.quantity + 1)"
                                        :disabled="item.quantity >= item.stock"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 transition text-gray-700 font-bold text-base disabled:opacity-40">+</button>
                                <span class="text-xs text-gray-400">({{ item.stock }} in stock)</span>
                            </div>
                        </div>
                        <!-- Subtotal & Remove -->
                        <div class="text-right shrink-0">
                            <p class="font-bold text-gray-900">₱{{ (item.price * item.quantity).toLocaleString('en-PH', {minimumFractionDigits: 2}) }}</p>
                            <button @click="removeItem(item)"
                                    class="mt-2 text-xs text-red-400 hover:text-red-600 transition flex items-center gap-1 ml-auto">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="w-full lg:w-80 shrink-0">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-20">
                        <h2 class="text-base font-bold text-gray-800 mb-5">Order Summary</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Selected items</span>
                                <span class="font-medium">{{ selectedCount }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium">₱{{ selectedTotal.toLocaleString('en-PH', {minimumFractionDigits: 2}) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="text-emerald-600 font-medium">Free</span>
                            </div>
                            <div class="border-t border-gray-100 pt-3 flex justify-between font-bold text-gray-800 text-base">
                                <span>Total</span>
                                <span class="text-emerald-600">₱{{ selectedTotal.toLocaleString('en-PH', {minimumFractionDigits: 2}) }}</span>
                            </div>
                        </div>
                        <button @click="checkout" :disabled="selectedCount === 0"
                                :class="selectedCount === 0 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm'"
                                class="w-full mt-6 py-3 rounded-xl font-bold transition text-sm">
                            Checkout ({{ selectedCount }} item{{ selectedCount !== 1 ? 's' : '' }})
                        </button>
                        <a href="../Products/dashboard.php" class="block text-center mt-3 text-sm text-emerald-500 hover:text-emerald-600 transition">
                            ← Continue Shopping
                        </a>
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
                user: { name: 'User', image: null },
                menuGroups: typeof calculateActiveUserMenu !== "undefined" ? calculateActiveUserMenu(JSON.parse(JSON.stringify(userSidebarMenu))) : [],
                cartItems: [],
                total: 0,
                loading: true,
                notifications: [],
                unreadCount: 0,
                showNotifications: false
            };
        },
        computed: {
            totalItems() {
                return this.cartItems.reduce((s, i) => s + i.quantity, 0);
            },
            selectedCount() {
                return this.cartItems.filter(i => i.selected).length;
            },
            selectedTotal() {
                return this.cartItems
                    .filter(i => i.selected)
                    .reduce((s, i) => s + parseFloat(i.price) * i.quantity, 0);
            },
            allSelected() {
                return this.cartItems.length > 0 && this.cartItems.every(i => i.selected);
            }
        },
        mounted() {
            this.loadProfile();
            this.loadCart();
            this.fetchNotifications();
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
            toggleMenu(index) { this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen; },
            getIcon(iconName) { return typeof getUserIcon !== "undefined" ? getUserIcon(iconName) : ""; },
            handleImageError(e) { e.target.src = 'https://ui-avatars.com/api/?name=User&background=e5e7eb&color=374151'; },
            handleProductImgError(e) { e.target.src = 'https://via.placeholder.com/80x80/f3f4f6/6b7280?text=?'; },

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

            async loadProfile() {
                try {
                    const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user.name = data.data.full_name || data.data.username;
                        this.user.image = data.data.profile_picture;
                    }
                } catch(e) {}
            },
            async loadCart() {
                this.loading = true;
                try {
                    const res = await fetch('../../../Controller/cart-controller.php?action=get');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.cartItems = (data.data || []).map(i => ({ ...i, selected: true }));
                        this.total = parseFloat(data.total || 0);
                    }
                } catch(e) {}
                this.loading = false;
            },
            async changeQty(item, newQty) {
                if (newQty < 1) {
                    await this.removeItem(item);
                    return;
                }
                if (newQty > item.stock) return;

                const formData = new FormData();
                formData.append('cart_id', item.id);
                formData.append('quantity', newQty);

                try {
                    const res = await fetch('../../../Controller/cart-controller.php?action=update', { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.status === 'success') {
                        item.quantity = newQty;
                        this.recalcTotal();
                    }
                } catch(e) {}
            },
            async removeItem(item) {
                const formData = new FormData();
                formData.append('cart_id', item.id);
                try {
                    const res = await fetch('../../../Controller/cart-controller.php?action=remove', { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.cartItems = this.cartItems.filter(i => i.id !== item.id);
                        this.recalcTotal();
                    }
                } catch(e) {}
            },
            recalcTotal() {
                this.total = this.cartItems.reduce((s, i) => s + parseFloat(i.price) * i.quantity, 0);
            },
            toggleSelectAll() {
                const newVal = !this.allSelected;
                this.cartItems.forEach(i => i.selected = newVal);
            },
            async removeSelected() {
                const selected = this.cartItems.filter(i => i.selected);
                if (selected.length === 0) return;
                const result = await Swal.fire({
                    title: `Remove ${selected.length} item${selected.length !== 1 ? 's' : ''}?`,
                    text: 'Selected items will be removed from your cart.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Remove'
                });
                if (!result.isConfirmed) return;
                for (const item of selected) {
                    await this.removeItem(item);
                }
            },
            checkout() {
                const selected = this.cartItems.filter(i => i.selected);
                if (selected.length === 0) {
                    Swal.fire('No Items Selected', 'Please select at least one item to checkout.', 'warning');
                    return;
                }
                const totalQty = selected.reduce((s, i) => s + i.quantity, 0);
                if (totalQty > 200) {
                    Swal.fire('Limit Exceeded', 'You can only checkout up to 200 items at a time.', 'warning');
                    return;
                }
                // Pass selected cart_item IDs to checkout page via sessionStorage
                const ids = selected.map(i => i.id);
                sessionStorage.setItem('checkout_cart_ids', JSON.stringify(ids));
                window.location.href = '../Checkout/index.php';
            }
        }
    }).mount('#app');
</script>
</body>
</html>
