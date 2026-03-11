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
                <!-- Cart Icon -->
                <a href="../Cart/index.php" class="relative p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span v-if="cartCount > 0" class="absolute top-0 right-0 min-w-[18px] h-[18px] flex items-center justify-center bg-emerald-500 text-white text-[10px] font-bold rounded-full px-1">{{ cartCount }}</span>
                </a>

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
                                <div v-for="notif in notifications" :key="notif.id"
                                     @click="handleNotifClick(notif)"
                                     class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer"
                                     :class="{'bg-emerald-50/30': !notif.is_read}">
                                    <div class="flex gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0" v-if="!notif.is_read"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-800 mb-0.5">{{ notif.title }}</p>
                                            <p class="text-xs text-gray-500 leading-relaxed">{{ notif.message }}</p>
                                            <p class="text-[10px] text-gray-400 mt-2">{{ notif.time_ago }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div class="relative">
                    <button @click="toggleProfileMenu" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition">
                        <img :src="user.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=e5e7eb&color=374151'" @error="handleImageError" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div v-show="showProfileMenu" class="absolute right-0 top-12 w-44 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-[100]">
                        <a href="../Profile/edit.php" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>
                        <a href="../../Auth/logout.php" class="flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition border-t border-gray-100" onclick="event.preventDefault(); Swal.fire({ title: 'Logout', text: 'Are you sure?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10B981', cancelButtonColor: '#EF4444', confirmButtonText: 'Yes, logout' }).then((r) => { if (r.isConfirmed) window.location.href = this.href; });">
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
                            <button @click="toggleMenu(index)" class="w-full flex items-center justify-between px-4 py-2 text-xs uppercase font-bold text-gray-400 tracking-wider hover:text-gray-600 transition">
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

        <!-- Store Not Found -->
        <div v-if="storeNotFound" class="flex flex-col items-center justify-center min-h-[60vh] text-gray-400">
            <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-lg font-semibold">Store not found</p>
            <a href="../Products/dashboard.php" class="mt-4 text-emerald-500 hover:underline text-sm">Browse Products</a>
        </div>

        <!-- Store Profile -->
        <div v-else>
            <!-- Store Info Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8 flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <img :src="store.profile_picture || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(store.business_name || store.full_name || 'Store') + '&background=10b981&color=fff&size=128'"
                     @error="handleStoreImgError"
                     class="w-20 h-20 rounded-2xl object-cover border border-gray-100 shadow-sm shrink-0">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ store.business_name || store.full_name }}</h1>
                    <p class="text-sm text-gray-400 mt-0.5">@{{ store.username }}</p>
                    <div class="flex items-center gap-4 mt-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            {{ store.product_count }} Products
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            Verified Partner
                        </span>
                    </div>
                </div>
                <a href="../Products/dashboard.php" class="shrink-0 text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Browse
                </a>
            </div>

            <!-- Products Section -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">All Products</h2>
                        <p class="text-sm text-gray-400 mt-0.5">{{ products.length }} item{{ products.length != 1 ? 's' : '' }} available</p>
                    </div>
                </div>

                <!-- Loading Skeleton -->
                <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <div v-for="i in 5" :key="i" class="bg-gray-100 animate-pulse rounded-2xl h-72"></div>
                </div>

                <!-- Empty State -->
                <div v-else-if="products.length === 0" class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-gray-100 shadow-sm text-gray-400">
                    <svg class="w-12 h-12 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <p class="font-semibold text-gray-500">No products yet</p>
                    <p class="text-sm mt-1">This store hasn't added any products.</p>
                </div>

                <!-- Product Grid -->
                <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <div v-for="product in products" :key="product.id" @click="viewProduct(product)"
                         class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden flex flex-col cursor-pointer">
                        <!-- Image -->
                        <div class="relative aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                            <img v-if="product.image_url" :src="product.image_url"
                                 @error="handleProductImgError(product)"
                                 :alt="product.product_name"
                                 class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-300">
                            <div v-else class="flex flex-col items-center justify-center text-gray-300 font-bold uppercase tracking-widest text-[10px]">
                                <svg class="w-8 h-8 mb-1 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                NO IMAGE
                            </div>
                            <span v-if="product.qty <= 0" class="absolute top-2 right-2 bg-red-500 text-white text-[8px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Out of Stock</span>
                        </div>
                        <!-- Info -->
                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight line-clamp-2 mb-1">{{ product.product_name }}</h3>
                            <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mb-2">{{ +product.sold_count }} sold</p>
                            <p class="text-emerald-600 font-bold text-base mb-3">₱{{ parseFloat(product.price).toLocaleString('en-PH', { minimumFractionDigits: 2 }) }}</p>
                            <div class="mt-auto flex flex-col gap-2">
                                <button @click.stop="addToCart(product)"
                                        :disabled="product.qty <= 0"
                                        class="w-full py-2 border border-emerald-500 text-emerald-600 rounded-xl text-xs font-bold hover:bg-emerald-50 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    Add to Cart
                                </button>
                                <button @click.stop="buyNow(product)"
                                        :disabled="product.qty <= 0"
                                        class="w-full py-2 bg-emerald-500 text-white rounded-xl text-xs font-bold hover:bg-emerald-600 transition shadow-sm disabled:opacity-40 disabled:cursor-not-allowed">
                                    Buy Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const OWNER_ID = new URLSearchParams(window.location.search).get('id');
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                showProfileMenu: false,
                user: { name: 'User', image: null },
                menuGroups: typeof calculateActiveUserMenu !== "undefined" ? calculateActiveUserMenu(JSON.parse(JSON.stringify(userSidebarMenu))) : [],
                store: {},
                products: [],
                loading: true,
                storeNotFound: false,
                cartCount: 0,
                notifications: [],
                unreadCount: 0,
                showNotifications: false
            };
        },
        async mounted() {
            this.loadProfile();
            this.loadCartCount();

            if (!OWNER_ID) {
                this.storeNotFound = true;
                this.loading = false;
                return;
            }

            await this.loadStore();
            await this.loadProducts();
            this.fetchNotifications();
            window.addEventListener('resize', this.handleResize);
        },
        beforeUnmount() {
            window.removeEventListener('resize', this.handleResize);
        },
        methods: {
            handleResize() {
                this.sidebarOpen = window.innerWidth >= 1024;
            },
            toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
            toggleProfileMenu() { this.showProfileMenu = !this.showProfileMenu; },
            toggleMenu(index) { this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen; },
            getIcon(iconName) { return typeof getUserIcon !== "undefined" ? getUserIcon(iconName) : ""; },
            handleImageError(e) { e.target.src = 'https://ui-avatars.com/api/?name=User&background=e5e7eb&color=374151'; },
            handleStoreImgError(e) { e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(this.store.business_name || 'Store')}&background=10B981&color=fff&size=128`; },
            handleProductImgError(product) { product.image_url = null; },
            toggleNotifications() { this.showNotifications = !this.showNotifications; },

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
            async loadCartCount() {
                try {
                    const res = await fetch('../../../Controller/cart-controller.php?action=count');
                    const data = await res.json();
                    if (data.status === 'success') this.cartCount = data.count;
                } catch(e) {}
            },
            async loadStore() {
                try {
                    const res = await fetch(`../../../Controller/product-controller.php?action=store_info&owner_id=${OWNER_ID}`);
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.store = data.data;
                    } else {
                        this.storeNotFound = true;
                    }
                } catch(e) {
                    this.storeNotFound = true;
                }
            },
            async loadProducts() {
                this.loading = true;
                try {
                    const res = await fetch(`../../../Controller/product-controller.php?action=list_store&owner_id=${OWNER_ID}`);
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.products = data.data;
                    }
                } catch(e) {}
                this.loading = false;
            },
            async addToCart(product) {
                const formData = new FormData();
                formData.append('product_id', product.id);
                formData.append('quantity', 1);

                try {
                    const res = await fetch('../../../Controller/cart-controller.php?action=add', { method: 'POST', body: formData });
                    const data = await res.json();

                    if (data.status === 'success') {
                        this.cartCount = data.count;
                        Swal.fire({
                            icon: 'success',
                            title: 'Added to Cart!',
                            text: `${product.product_name} has been added to your cart.`,
                            timer: 1800,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        // Create notification
                        const notifData = new FormData();
                        notifData.append('title', 'Added to Cart');
                        notifData.append('message', `You added ${product.product_name} to your cart successfully.`);
                        notifData.append('type', 'success');
                        notifData.append('target_url', '../Cart/index.php');
                        await fetch('../../../Controller/notification-controller.php?action=create', { method: 'POST', body: notifData });
                        await this.fetchNotifications();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                } catch(e) {
                    Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not add to cart.' });
                }
            },
            async buyNow(product) {
                await this.addToCart(product);
                window.location.href = '../Cart/index.php';
            },
            viewProduct(product) {
                window.location.href = `../Products/view-product.php?id=${product.id}`;
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
            async markAllRead() {
                try {
                    const res = await fetch('../../../Controller/notification-controller.php?action=mark_all_read');
                    const data = await res.json();
                    if (data.status === 'success') {
                        await this.fetchNotifications();
                    }
                } catch(e) { console.error(e); }
            },
            async handleNotifClick(notif) {
                try {
                    if (!notif.is_read) {
                        const formData = new FormData();
                        formData.append('id', notif.id);
                        await fetch('../../../Controller/notification-controller.php?action=mark_read', { method: 'POST', body: formData });
                    }
                    if (notif.target_url) {
                        window.location.href = notif.target_url;
                    }
                    this.showNotifications = false;
                    await this.fetchNotifications();
                } catch(e) { console.error(e); }
            },
        }
    }).mount('#app');
</script>
</body>
</html>
