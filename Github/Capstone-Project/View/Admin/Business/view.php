<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('admin');
?>
<?php 
require_once '../../Layouts/auth_check.php';
require '../../Layouts/header.php'; 
?>
<div id="app" v-cloak>
    <!--Navbar-->
    <header>
       <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200" >
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <img src="../../../Public/pictures/menu.svg" alt="Menu" class="w-6 h-6">
                </button>
                <a href="<?= getDashboardUrl() ?>" class="text-xl font-semibold text-emerald-500 hover:opacity-80 transition">FixMart Admin</a>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-700">Hello, <span class="font-medium">{{ user.name }}</span></span>
                <button class="relative p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span v-if="notifications > 0" class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">{{ notifications }}</span>
                </button>
                <button id="adminProfileBtn" @click="toggleProfileMenu" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition relative">
                    <img :src="user.image" @error="handleImageError" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                    <svg class="w-4 h-4 text-gray-600 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <!-- Profile Dropdown -->
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
                </button>
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
    <main class="transition-all duration-300 ease-in-out mt-14 px-4 sm:px-6 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div class="max-w-5xl mx-auto">
            <a :href="isPartners ? 'partners.php' : 'application.php'" class="group flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-emerald-600 transition-colors mb-6">
                <svg class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ isPartners ? 'Back to Business Partners' : 'Back to Business Requests' }}
            </a> 

            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500"></div>
            </div>

            <div v-else-if="application" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-8 lg:p-10">
                    <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Application Summary</h1>
                        <span :class="{'bg-yellow-100 text-yellow-700': application.status === 'Pending', 'bg-emerald-100 text-emerald-700': application.status === 'Approved', 'bg-red-100 text-red-700': application.status === 'Rejected'}" class="px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                            {{ application.status }}
                        </span>
                    </div>
                    
                    <!--Owners Info-->
                    <div class="mb-10">
                        <h2 class="font-bold text-lg text-gray-800 mb-6 flex items-center gap-2">
                            <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                            Owners Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Firstname</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.first_name }}</h1>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Lastname</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.last_name }}</h1>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Email Address</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.email }}</h1>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Mobile Number</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.phone }}</h1>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">ID Type</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.id_type }}</h1>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Government ID Number</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.gov_id }}</h1>
                            </div>
                        </div>
                    </div>

                    <!--Business Info-->
                    <div class="mb-10 border-t border-gray-100 pt-10">
                        <h2 class="font-bold text-lg text-gray-800 mb-6 flex items-center gap-2">
                            <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                            Business Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Business Name</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.business_name }}</h1>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Business Type</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.business_type }}</h1>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Business Email</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.business_email }}</h1>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Business Contact No</p>
                                <h1 class="text-base font-semibold text-gray-900">{{ application.business_phone }}</h1>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Business Address</p>
                                <h1 class="text-base font-semibold text-gray-900 leading-relaxed">{{ application.business_address }}</h1>
                            </div>
                        </div>
                    </div>

                    <!--Product Services-->
                    <div class="mb-10 border-t border-gray-100 pt-10">
                        <h2 class="font-bold text-lg text-gray-800 mb-6 flex items-center gap-2">
                            <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                            Product/Services Details
                        </h2>
                        <div class="space-y-6">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Service Description</p>
                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">
                                    {{ application.offer_details }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Business Legitimacy (Documents)-->
                    <div class="mb-6 border-t border-gray-100 pt-10">
                        <h2 class="font-bold text-lg text-gray-800 mb-6 flex items-center gap-2">
                            <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                            Business Legitimacy
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Business Permit -->
                            <div class="space-y-3">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Business Permit Photo</p>
                                <div v-if="application.business_permit"
                                     @click="openImageModal(application.business_permit)"
                                     class="group relative aspect-video bg-gray-100 rounded-xl border border-gray-200 overflow-hidden cursor-pointer">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition-all z-10">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </div>
                                    <img :src="application.business_permit" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" @error="e => e.target.parentElement.innerHTML = '<p class=\'text-gray-400 text-sm text-center pt-16\'>Image not available</p>'">
                                </div>
                                <div v-else class="aspect-video bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                    <p class="text-gray-400 text-sm">No file uploaded</p>
                                </div>
                            </div>

                            <!-- DTI Registration -->
                            <div class="space-y-3">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">DTI Registration Photo</p>
                                <div v-if="application.dti_registration"
                                     @click="openImageModal(application.dti_registration)"
                                     class="group relative aspect-video bg-gray-100 rounded-xl border border-gray-200 overflow-hidden cursor-pointer">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition-all z-10">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </div>
                                    <img :src="application.dti_registration" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" @error="e => e.target.parentElement.innerHTML = '<p class=\'text-gray-400 text-sm text-center pt-16\'>Image not available</p>'">
                                </div>
                                <div v-else class="aspect-video bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                    <p class="text-gray-400 text-sm">No file uploaded</p>
                                </div>
                            </div>

                            <!-- Government / Tax ID -->
                            <div class="space-y-3">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tax Identification Photo</p>
                                <div v-if="application.gov_id_file"
                                     @click="openImageModal(application.gov_id_file)"
                                     class="group relative aspect-video bg-gray-100 rounded-xl border border-gray-200 overflow-hidden cursor-pointer">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition-all z-10">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </div>
                                    <img :src="application.gov_id_file" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" @error="e => e.target.parentElement.innerHTML = '<p class=\'text-gray-400 text-sm text-center pt-16\'>Image not available</p>'">
                                </div>
                                <div v-else class="aspect-video bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                    <p class="text-gray-400 text-sm">No file uploaded</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div v-if="application.status === 'Pending'" class="bg-gray-50 px-10 py-8 border-t border-gray-100 flex justify-end gap-4">
                    <button @click="confirmAction('Reject')" :disabled="isProcessing" class="px-10 py-3.5 rounded-xl border border-red-200 text-red-600 font-bold hover:bg-red-50 transition transform active:scale-95 disabled:opacity-50">Reject Application</button>
                    <button @click="confirmAction('Approve')" :disabled="isProcessing" class="px-14 py-3.5 rounded-xl bg-emerald-500 text-white font-bold hover:bg-emerald-600 shadow-lg shadow-emerald-200 transition transform hover:scale-105 active:scale-95 disabled:opacity-50">Approve Business</button>
                </div>
                <div v-else class="bg-gray-50 px-10 py-8 border-t border-gray-100 text-center">
                    <p class="text-gray-400 font-semibold italic">This application has been processed and is now {{ application.status }}.</p>
                </div>
            </div>

            <div v-else class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                <p class="text-gray-500 font-medium">No application data found for this ID.</p>
                <a href="application.php" class="text-emerald-500 font-bold mt-4 inline-block hover:underline">Return to Requests List</a>
            </div>
        </div>

        <!-- Image Modal -->
        <div v-if="showImageModal" @click="closeImageModal" 
             class="fixed inset-0 bg-black/80 z-[100] flex items-center justify-center p-6 backdrop-blur-sm">
            <div class="relative max-w-5xl w-full" @click.stop>
                <button @click="closeImageModal" class="absolute -top-12 right-0 text-white hover:text-emerald-400 transition-colors">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="bg-white p-2 rounded-2xl shadow-2xl">
                    <img :src="currentImage" alt="Document Preview" class="w-full h-auto rounded-lg max-h-[85vh] object-contain">
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
                sidebarOpen: true,
                applicationId: new URLSearchParams(window.location.search).get('id'),
                application: null,
                loading: true,
                isProcessing: false,
                showImageModal: false,
                currentImage: '',
                showProfileMenu: false,
                notifications: 0,
                user: { name: 'Admin', image: null },
                isPartners: new URLSearchParams(window.location.search).get('source') === 'partners',
                menuGroups: (() => {
                    const isPar = new URLSearchParams(window.location.search).get('source') === 'partners';
                    return [
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
                                { name: 'Business Apps', icon: 'business', link: '../Business/application.php', active: !isPar },
                                { name: 'Accounts', icon: 'users', link: '../Accounts/accounts.php', active: false },
                                { name: 'Schedule', icon: 'schedule', link: '../Schedule/index.php', active: false }
                            ]
                        },
                        {
                            title: 'Reporting & Analytics',
                            isOpen: true,
                            items: [
                                { name: 'Technicians Tracking', icon: 'technicians', link: '../Technicians/index.php', active: false },
                                { name: 'Business Partners', icon: 'partners', link: '../Business/partners.php', active: isPar }
                            ]
                        }
                    ];
                })(),
                logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' }
            }
        },
        mounted() {
            this.fetchApplication();
            this.loadProfile();
            this.loadNotifications();
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#adminProfileBtn')) this.showProfileMenu = false;
            });
        },
        methods: {
            async loadProfile() {
                try {
                    const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user.name = data.data.full_name || data.data.username;
                        this.user.image = data.data.profile_picture ||
                            `https://ui-avatars.com/api/?name=${encodeURIComponent(data.data.full_name)}&background=e5e7eb&color=374151`;
                    }
                } catch (e) { console.error(e); }
            },
            async loadNotifications() {
                try {
                    const res = await fetch('../../../Controller/notification-controller.php?action=fetch');
                    const data = await res.json();
                    if (data.status === 'success') this.notifications = data.unread_count || 0;
                } catch (e) { console.error(e); }
            },
            async fetchApplication() {
                if (!this.applicationId) { this.loading = false; return; }
                try {
                    const res = await fetch(`../../../Controller/business-controller.php?action=get&id=${this.applicationId}`);
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.application = data.data;
                        // Fix image paths ? prepend BASE_URL so browser can load them
                        if (this.application) {
                            ['gov_id_file', 'business_permit', 'dti_registration'].forEach(key => {
                                if (this.application[key] && !this.application[key].startsWith('http')) {
                                    this.application[key] = BASE_URL + this.application[key];
                                }
                            });
                        }
                    }
                } catch (e) { console.error(e); }
                finally { this.loading = false; }
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
                        title: 'Confirm Approve?',
                        text: 'Are you sure you want to approve this business application?',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Yes, Approve!'
                    }).then((result) => {
                        if (result.isConfirmed) this.processAction('Approve');
                    });
                }
            },
            async processAction(action, rejectionData = null) {
                const endpoint = action === 'Approve' ? 'approve' : 'reject';
                this.isProcessing = true;
                try {
                    const payload = { id: this.applicationId };
                    if (rejectionData) {
                        payload.rejection_reasons = rejectionData.reasons;
                        payload.rejection_note    = rejectionData.note;
                    }
                    const res = await fetch(`../../../Controller/business-controller.php?action=${endpoint}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        Swal.fire({ title: 'Success!', text: data.message, icon: 'success' })
                            .then(() => this.fetchApplication());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Server request failed.', 'error');
                } finally {
                    setTimeout(() => this.isProcessing = false, 1000);
                }
            },
            openImageModal(img) { this.currentImage = img; this.showImageModal = true; },
            closeImageModal() { this.showImageModal = false; },
            toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
            toggleMenu(i) { this.menuGroups[i].isOpen = !this.menuGroups[i].isOpen; },
            toggleProfileMenu(e) { e.stopPropagation(); this.showProfileMenu = !this.showProfileMenu; },
            handleImageError(e) {
                e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(this.user.name)}&background=e5e7eb&color=374151`;
            },
            getIcon(name) {
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
                return icons[name] || '';
            }
        }
    }).mount('#app');
</script>
</body>
</html>




