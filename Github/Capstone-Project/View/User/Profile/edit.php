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
                <h1 class="text-3xl font-bold text-gray-800">Account Settings</h1>
                <p class="text-gray-500 mt-1">Manage your personal information and account settings.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Avatar Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col items-center text-center">
                        <div class="relative mb-5">
                            <img :src="previewImage || user.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=e5e7eb&color=374151'"
                                 @error="handleImageError"
                                 class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-md">
                            <label class="absolute bottom-0 right-0 w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center cursor-pointer hover:bg-emerald-600 transition shadow">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden">
                            </label>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">{{ form.full_name || user.name }}</h2>
                        <p class="text-sm text-gray-400 mt-0.5">{{ form.email }}</p>
                        <span class="mt-3 text-xs font-semibold bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">Customer</span>
                        <p class="text-xs text-gray-400 mt-5">Click the camera icon to change your photo</p>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-7 py-5 border-b border-gray-100">
                            <h2 class="text-base font-bold text-gray-800">Personal Information</h2>
                            <p class="text-sm text-gray-400">Your basic account details</p>
                        </div>
                        <form @submit.prevent="updateProfile" class="p-7">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">Full Name</label>
                                    <input v-model="form.full_name" type="text" required
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">Email</label>
                                    <input v-model="form.email" type="email" required
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">Username <span class="text-gray-400 font-normal">(read-only)</span></label>
                                    <input v-model="form.username" type="text" disabled
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 outline-none bg-gray-100 text-gray-400 text-sm cursor-not-allowed">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">Phone</label>
                                    <input v-model="form.phone" type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm">
                                </div>
                                <div class="space-y-2 sm:col-span-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">Address</label>
                                    <input v-model="form.address" type="text"
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm">
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-6 mb-6">
                                <h3 class="text-sm font-bold text-gray-700 mb-4">Change Password</h3>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">New Password <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input v-model="form.password" type="password" placeholder="Leave blank to keep current password"
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm">
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" :disabled="loading"
                                        class="inline-flex items-center gap-2 bg-emerald-500 text-white px-6 py-3 rounded-xl hover:bg-emerald-600 font-semibold transition shadow-sm shadow-emerald-500/30 disabled:opacity-50">
                                    <span v-if="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Save Changes
                                </button>
                            </div>
                        </form>
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
                form: {
                    full_name: '',
                    email: '',
                    username: '',
                    address: '',
                    phone: '',
                    password: ''
                },
                profileFile: null,
                previewImage: null,
                loading: false,
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

            handleFileUpload(event) {
                this.profileFile = event.target.files[0];
                if (this.profileFile) {
                    this.previewImage = URL.createObjectURL(this.profileFile);
                }
            },
            async loadProfile() {
                try {
                    const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.form = { ...this.form, ...data.data };
                        this.user.name = data.data.full_name;
                        this.user.image = data.data.profile_picture;
                    }
                } catch(e) { console.error(e); }
            },
            async updateProfile() {
                this.loading = true;
                const formData = new FormData();
                for (const key in this.form) {
                    formData.append(key, this.form[key]);
                }
                if (this.profileFile) {
                    formData.append('profile_picture', this.profileFile);
                }

                try {
                    const res = await fetch('../../../Controller/user-controller.php?action=update_profile', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                   
                    if (data.status === 'success') {
                         Swal.fire({
                            icon: 'success',
                            title: 'Profile Updated',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        this.user.name = this.form.full_name;
                        if (data.image) {
                            this.user.image = data.image; // Update avatar immediately
                        }
                    } else {
                         Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: data.message
                        });
                    }
                } catch(e) { 
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "Error updating profile"
                    });
                } finally {
                    this.loading = false;
                }
            }
        }
    }).mount('#app');
</script>
</body>
</html>

