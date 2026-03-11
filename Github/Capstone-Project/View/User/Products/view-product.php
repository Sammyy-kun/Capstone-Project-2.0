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

    <main class="transition-all duration-300 ease-in-out mt-14 px-4 sm:px-10 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div v-if="loading" class="flex flex-col items-center justify-center min-h-[60vh]">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500 mb-4"></div>
            <p class="text-gray-500 font-medium">Loading product details...</p>
        </div>

        <div v-else-if="!product" class="flex flex-col items-center justify-center min-h-[60vh] text-gray-400">
            <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-lg font-semibold">Product not found</p>
            <a href="dashboard.php" class="mt-4 text-emerald-500 hover:underline text-sm font-bold">Back to Shop</a>
        </div>

        <div v-else class="">
             <!-- Breadcrumbs -->
             <nav class="flex mb-6 text-sm font-medium text-gray-400" aria-label="Breadcrumb">
                <ol class="inline-flex items-center gap-2">
                    <li><a href="dashboard.php" class="hover:text-emerald-600 transition font-semibold">Browse Products</a></li>
                    <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                    <li class="text-gray-700 font-semibold truncate max-w-xs">{{ product.product_name }}</li>
                </ol>
            </nav>

             <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <!-- Image Section -->
                  <div class="space-y-4">
                      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex items-center justify-center" style="height: 420px;">
                          <img v-if="mainImage" class="w-full h-full object-contain p-8 transition-opacity duration-300" :src="mainImage" :alt="product.product_name" @error="handleImageError">
                          <div v-else class="flex flex-col items-center justify-center text-gray-300 font-bold uppercase tracking-widest text-xs">
                                <svg class="w-16 h-16 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                NO IMAGE
                          </div>
                      </div>
                      <div class="flex gap-3 overflow-x-auto pb-1" v-if="product.image_url || product.image_url_2 || product.image_url_3">
                          <template v-for="img in [product.image_url, product.image_url_2, product.image_url_3]">
                              <div v-if="img"
                                   @click="mainImage = img"
                                   class="bg-white rounded-xl border border-gray-100 p-2 w-20 h-20 flex-shrink-0 flex items-center justify-center cursor-pointer transition-all duration-200"
                                   :class="mainImage === img ? 'border-emerald-500 ring-2 ring-emerald-500/20 shadow-sm' : 'hover:border-emerald-300 hover:shadow-sm'">
                                  <img class="w-full h-full object-contain" :src="img" alt="Thumbnail">
                              </div>
                          </template>
                      </div>
                  </div>
                  
                  <!-- Details Section -->
                  <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 h-fit">
                      <div>
                          <div class="flex items-center justify-between mb-4">
                              <div class="flex items-center gap-2">
                                  <span v-if="product.category" class="text-emerald-600 font-bold text-xs uppercase tracking-wider bg-emerald-50 px-3 py-1 rounded-full">{{ product.category }}</span>
                                  <span v-else-if="product.brand" class="text-emerald-600 font-bold text-xs uppercase tracking-wider bg-emerald-50 px-3 py-1 rounded-full">{{ product.brand }}</span>
                              </div>
                              <a :href="'../Store/profile.php?id=' + product.owner_id" class="flex items-center gap-1.5 text-xs text-gray-600 hover:text-emerald-600 bg-gray-50 hover:bg-emerald-50 px-3 py-1.5 rounded-lg border border-gray-100 hover:border-emerald-200 transition font-semibold">
                                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                  Visit Store
                              </a>
                          </div>
                          <h1 class="text-2xl font-bold text-gray-900 mb-1 leading-tight">{{ product.product_name }}</h1>
                          <p v-if="product.model" class="text-gray-400 text-sm mb-3 font-medium">{{ product.model }}</p>
                          <p class="text-3xl font-bold text-emerald-600 mb-5">₱{{ parseFloat(product.price).toLocaleString('en-PH', { minimumFractionDigits: 2 }) }}</p>
                      </div>
                      
                      <div v-if="product.description" class="text-sm text-gray-500 leading-relaxed mb-6 pb-6 border-b border-gray-100">
                          <p class="whitespace-pre-line">{{ product.description }}</p>
                          <div v-if="product.specs" class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                              <h4 class="text-gray-900 font-bold mb-2 text-xs uppercase tracking-wider">Specifications</h4>
                              <p class="text-xs leading-relaxed">{{ product.specs }}</p>
                          </div>
                      </div>
                      
                      <div class="space-y-4">
                          <!-- Quantity -->
                          <div class="flex items-center justify-between">
                             <span class="text-sm font-semibold text-gray-700">Quantity</span>
                             <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-gray-50">
                                 <button @click="quantity = Math.max(1, quantity - 1)" class="px-4 py-2.5 hover:bg-gray-100 text-gray-600 transition font-bold text-lg leading-none disabled:opacity-40" :disabled="product.qty <= 0">−</button>
                                 <span class="w-12 text-center text-gray-800 font-bold text-sm">{{ quantity }}</span>
                                 <button @click="quantity = Math.min(product.qty, quantity + 1)" class="px-4 py-2.5 hover:bg-gray-100 text-gray-600 transition font-bold text-lg leading-none disabled:opacity-40" :disabled="product.qty <= 0">+</button>
                             </div>
                          </div>
                          
                          <!-- Availability -->
                          <div class="bg-gray-50 px-4 py-3 rounded-xl text-sm border border-gray-100 flex justify-between items-center">
                              <span class="text-gray-500 font-medium text-xs uppercase tracking-wider">Availability</span>
                              <div class="flex flex-col items-end">
                                  <span :class="product.qty > 0 ? 'text-emerald-600' : 'text-red-500'" class="font-bold text-sm">
                                      {{ product.qty > 0 ? product.qty + ' Units In Stock' : 'Out of Stock' }}
                                  </span>
                                  <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mt-0.5">{{ +product.sold_count }} items sold</span>
                              </div>
                          </div>

                           <!-- Actions -->
                          <div class="flex gap-3 pt-1">
                              <button @click="addToCart" :disabled="product.qty <= 0" 
                                      class="flex-1 bg-white border-2 border-emerald-500 text-emerald-600 py-3.5 rounded-xl font-bold hover:bg-emerald-50 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                  Add to Cart
                              </button>
                              <button @click="buyNow" :disabled="product.qty <= 0"
                                      class="flex-1 bg-emerald-500 text-white py-3.5 rounded-xl font-bold hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/25 disabled:opacity-50 disabled:cursor-not-allowed">
                                  Buy Now
                              </button>
                          </div>
                      </div>
                      
                       <div class="mt-6 pt-5 border-t border-gray-100 text-[10px] text-center text-gray-400 font-medium tracking-wide uppercase">
                          <p>Free shipping on orders over ₱5,000 • Secure Payment Methods</p>
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
                product: null,
                loading: true,
                mainImage: null,
                quantity: 1,
                cartCount: 0,
                notifications: [],
                unreadCount: 0,
                showNotifications: false,
                menuGroups: typeof calculateActiveUserMenu !== "undefined" ? calculateActiveUserMenu(JSON.parse(JSON.stringify(userSidebarMenu))) : []
            }
        },
        mounted() {
            this.loadProfile();
            this.fetchNotifications();
            this.loadProduct();
            this.loadCartCount();
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

            async loadCartCount() {
                try {
                    const res = await fetch('../../../Controller/cart-controller.php?action=count');
                    const data = await res.json();
                    if (data.status === 'success') this.cartCount = data.count;
                } catch(e) {}
            },

            async loadProduct() {
                const urlParams = new URLSearchParams(window.location.search);
                const id = urlParams.get('id');
                if (!id) {
                    window.location.href = 'dashboard.php';
                    return;
                }
                this.loading = true;
                try {
                    const res = await fetch(`../../../Controller/product-controller.php?action=get&id=${id}`);
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.product = data.data;
                        this.mainImage = this.product.image_url;
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Product not found.' }).then(() => {
                            window.location.href = 'dashboard.php';
                        });
                    }
                } catch(e) { console.error('Failed to load product', e); }
                this.loading = false;
            },

            async addToCart() {
                const formData = new FormData();
                formData.append('product_id', this.product.id);
                formData.append('quantity', this.quantity);
                try {
                    const res = await fetch('../../../Controller/cart-controller.php?action=add', { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.cartCount = data.count;
                        this.fetchNotifications();
                        Swal.fire({
                            icon: 'success',
                            title: 'Added to Cart!',
                            text: `${this.product.product_name} added to your cart.`,
                            timer: 1800,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        // Create notification
                        const notifData = new FormData();
                        notifData.append('title', 'Added to Cart');
                        notifData.append('message', `You added ${this.product.product_name} to your cart successfully.`);
                        notifData.append('type', 'success');
                        notifData.append('target_url', '../Cart/index.php');
                        await fetch('../../../Controller/notification-controller.php?action=create', { method: 'POST', body: notifData });
                        await this.fetchNotifications();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                } catch(e) { Swal.fire({ icon: 'error', title: 'Error', text: 'Could not add to cart.' }); }
            },

            async buyNow() {
                await this.addToCart();
                window.location.href = '../Cart/index.php';
            },

            handleImageError(event) {
                this.mainImage = null;
            }
        }
    }).mount('#app');
</script>
</body>
</html>
