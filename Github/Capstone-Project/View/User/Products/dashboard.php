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
                
                <div class="relative">
                    <button @click="toggleNotifications" class="relative p-2 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span v-if="unreadCount > 0" class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">{{ unreadCount }}</span>
                    </button>
                    
                    <!-- Notifications Dropdown -->
                    <transition name="slide-fade">
                        <div v-show="showNotifications" class="absolute right-0 top-12 w-80 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-[100]">
                            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                                <h3 class="font-bold text-gray-800 text-sm">Notifications</h3>
                                <button @click="markAllRead" v-if="notifications.length > 0" class="text-[10px] text-emerald-600 font-bold hover:text-emerald-700 uppercase tracking-tighter">Mark all read</button>
                            </div>
                            <div class="max-h-[400px] overflow-y-auto">
                                <div v-if="notifications.length === 0" class="p-8 text-center text-gray-400 text-sm">
                                    No notifications yet
                                </div>
                                <div v-for="notif in notifications" :key="notif.id" 
                                     @click="handleNotifClick(notif)"
                                     :class="notif.is_read ? 'opacity-60' : 'bg-emerald-50/10'"
                                     class="p-4 border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer relative">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full" :class="notif.is_read ? 'bg-gray-300' : 'bg-emerald-500'"></span>
                                            <span class="font-bold text-gray-800 text-xs truncate">{{ notif.title }}</span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 leading-relaxed px-4">{{ notif.message }}</p>
                                        <span class="text-[9px] text-gray-400 mt-1 px-4">{{ notif.created_at }}</span>
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
                            <span>{{ group.title }}</span>
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

    <main class="transition-all duration-300 ease-in-out mt-14 px-4 sm:px-10 py-10" :class="{'lg:ml-64': sidebarOpen}">
         <h1 class="font-bold text-3xl text-gray-800 mb-2">Shop Products</h1>
         <p class="text-gray-500 mb-6">Browse products from our trusted business partners.</p>

        <!-- Search & Category Filter -->
        <div class="mb-8 space-y-4">
            <!-- Search Bar -->
            <div class="relative max-w-xl">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input v-model="searchInput" @keyup.enter="applySearch" type="text"
                       placeholder="Search products by name..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 transition">
                <button v-if="searchInput" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <!-- Appliance Category Pills -->
            <div class="flex flex-wrap gap-2">
                <button v-for="cat in categories" :key="cat.label"
                        @click="selectCategory(cat)"
                        :class="activeCategory === cat.label ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-emerald-400 hover:text-emerald-600'"
                        class="px-4 py-1.5 rounded-full text-xs font-semibold border transition-all flex items-center gap-1.5">
                    <span>{{ cat.icon }}</span>
                    <span>{{ cat.label }}</span>
                </button>
            </div>
        </div>

        <!-- Store Groups -->
        <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
            <div v-for="i in 10" :key="i" class="bg-gray-100 animate-pulse rounded-2xl h-64"></div>
        </div>

        <div v-else-if="stores.length > 0" class="space-y-8">
            <div v-for="store in stores" :key="store.id" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <!-- Store Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img :src="store.profile_picture || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(store.business_name || store.full_name) + '&background=10b981&color=fff'"
                             class="w-10 h-10 rounded-xl object-cover border border-gray-100 shadow-sm"
                             @error="handleImageError">
                        <div>
                            <h2 class="font-bold text-gray-900 text-sm leading-tight">{{ store.business_name || store.full_name }}</h2>
                            <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mt-0.5">{{ store.product_count }} products available</p>
                        </div>
                    </div>
                    <a :href="'../Store/profile.php?id=' + store.id"
                       class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg">
                        Visit Store
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Product Grid -->
                <div class="p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        <div v-for="product in store.products" :key="product.id"
                             @click="viewProduct(product)"
                             class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col cursor-pointer">
                            <!-- Image -->
                            <div class="relative bg-gray-50 aspect-square flex items-center justify-center overflow-hidden">
                                <img v-if="product.image_url" :src="product.image_url" :alt="product.product_name"
                                     class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-300"
                                     @error="handleImageError(product)">
                                <div v-else class="flex flex-col items-center justify-center text-gray-300 text-[9px] font-bold uppercase tracking-widest">
                                    <svg class="w-7 h-7 mb-1 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    NO IMAGE
                                </div>
                                <span v-if="product.qty <= 0" class="absolute top-2 right-2 bg-red-500 text-white text-[8px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Out of Stock</span>
                            </div>
                            <!-- Info -->
                            <div class="p-3 flex flex-col flex-1">
                                <h3 class="font-bold text-gray-900 text-xs leading-tight line-clamp-2 mb-1">{{ product.product_name }}</h3>
                                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mb-2">{{ +product.sold_count }} sold</p>
                                <div class="mt-auto flex items-center justify-between gap-2">
                                    <span class="text-emerald-600 font-bold text-sm">₱{{ parseFloat(product.price).toLocaleString() }}</span>
                                    <button v-if="product.qty > 0" @click.stop="addToCart(product)"
                                            class="shrink-0 bg-emerald-500 hover:bg-emerald-600 text-white p-1.5 rounded-lg shadow-sm transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-center gap-2 pt-4">
                <button @click="prevPage" :disabled="page === 1"
                        class="p-2 rounded-lg border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="flex items-center gap-1">
                    <button v-for="p in totalPages" :key="p" @click="page = p"
                            :class="page === p ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                            class="w-9 h-9 rounded-lg border font-semibold text-sm transition flex items-center justify-center">
                        {{ p }}
                    </button>
                </div>
                <button @click="nextPage" :disabled="page === totalPages"
                        class="p-2 rounded-lg border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-gray-100 shadow-sm text-gray-400">
            <div class="bg-gray-50 w-16 h-16 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-700 mb-1">No products found</h3>
            <p class="text-sm text-gray-400">Try a different search or category.</p>
            <button @click="clearSearch" class="mt-4 text-sm text-emerald-600 hover:text-emerald-700 font-semibold transition">Clear filter</button>
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
                stores: [],
                loading: true,
                page: 1,
                limit: 5,
                total: 0,
                notifications: [],
                unreadCount: 0,
                showNotifications: false,
                searchInput: '',
                activeSearch: '',
                activeCategory: 'All',
                activeCategoryValue: '',
                categories: [
                    { label: 'All',              value: '',                icon: '🛒' },
                    { label: 'Air Conditioner',  value: 'Air Conditioner', icon: '❄️' },
                    { label: 'Refrigerator',     value: 'Refrigerator',    icon: '🧊' },
                    { label: 'Washing Machine',  value: 'Washing Machine', icon: '🫧' },
                    { label: 'TV / Monitor',     value: 'TV/Monitor',      icon: '📺' },
                    { label: 'Microwave',        value: 'Microwave',       icon: '📡' },
                    { label: 'Water Dispenser',  value: 'Water Dispenser', icon: '💧' },
                    { label: 'Fan',              value: 'Fan',             icon: '🌀' },
                    { label: 'Laptop',           value: 'Laptop',          icon: '💻' },
                ],
                menuGroups: typeof calculateActiveUserMenu !== "undefined" ? calculateActiveUserMenu(JSON.parse(JSON.stringify(userSidebarMenu))) : []
            }
        },
        computed: {
            totalPages() {
                return Math.ceil(this.total / this.limit);
            }
        },
        watch: {
            page() {
                this.loadStores();
            }
        },        mounted() {
            this.loadProfile();
            this.loadStores();
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
            toggleMenu(index) { this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen; },
            getIcon(iconName) { return typeof getUserIcon !== "undefined" ? getUserIcon(iconName) : ""; },
            toggleNotifications() { this.showNotifications = !this.showNotifications; },

            async loadStores() {
                this.loading = true;
                const offset = (this.page - 1) * this.limit;
                const searchParam   = this.activeSearch        ? encodeURIComponent(this.activeSearch)        : '';
                const categoryParam = this.activeCategoryValue ? encodeURIComponent(this.activeCategoryValue) : '';
                try {
                    const res = await fetch(`../../../Controller/product-controller.php?action=list_by_store&limit=${this.limit}&offset=${offset}&search=${searchParam}&category=${categoryParam}`);
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.stores = data.data;
                        this.total = data.total;
                    }
                } catch(e) { console.error('Failed to load stores', e); }
                this.loading = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            applySearch() {
                this.activeSearch = this.searchInput.trim();
                this.activeCategory = 'All';
                this.activeCategoryValue = '';
                this.page = 1;
                this.loadStores();
            },
            clearSearch() {
                this.searchInput = '';
                this.activeSearch = '';
                this.activeCategory = 'All';
                this.activeCategoryValue = '';
                this.page = 1;
                this.loadStores();
            },
            selectCategory(cat) {
                this.activeCategory = cat.label;
                this.activeCategoryValue = cat.value;
                this.activeSearch = '';
                this.searchInput = '';
                this.page = 1;
                this.loadStores();
            },

            prevPage() { if (this.page > 1) this.page--; },
            nextPage() { if (this.page < this.totalPages) this.page++; },

            async addToCart(product) {
                const formData = new FormData();
                formData.append('product_id', product.id);
                formData.append('quantity', 1);
                try {
                    const res = await fetch('../../../Controller/cart-controller.php?action=add', { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.fetchNotifications();
                        Swal.fire({
                            icon: 'success',
                            title: 'Added to Cart!',
                            text: `${product.product_name} added to your cart.`,
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
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not add to cart.' });
                }
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
                } catch(e) { console.error(notif); }
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

            async loadProfile() {
                try {
                    const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user.name = data.data.full_name || data.data.username;
                        this.user.image = data.data.profile_picture || null;
                    }
                } catch(e) {}
            },
            handleImageError(product) {
                product.image_url = null;
            },
            viewProduct(product) {
                window.location.href = `view-product.php?id=${product.id}`;
            }
        }
    }).mount('#app');
</script>
</body>
</html>


