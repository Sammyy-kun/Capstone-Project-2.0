<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('technician');
?>
<?php require '../../Layouts/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div id="app" v-cloak class="flex min-h-screen bg-white font-sans">
    <!-- Sidebar / Navbar Structure -->
    <header>
       <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200">
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <img src="../../../Public/pictures/menu.svg" alt="Menu" class="w-6 h-6">
                </button>
                <a href="../Dashboard/index.php" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition">FixMart Tech</a>
            </div>
            <div class="flex items-center gap-4">
                <!-- Status Pill (Desktop) -->
                <div class="hidden md:flex bg-gray-100 rounded-full p-1 border border-gray-200 items-center mr-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold cursor-pointer transition-colors" :class="{'bg-emerald-500 text-white': status==='active', 'text-gray-500 hover:bg-gray-200': status!=='active'}" @click="updateStatus('active')">Online</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold cursor-pointer transition-colors" :class="{'bg-gray-500 text-white': status==='offline', 'text-gray-500 hover:bg-gray-200': status!=='offline'}" @click="updateStatus('offline')">Offline</span>
                </div>
                <span class="text-sm text-gray-700 hidden sm:inline">Hello, <span class="font-medium">{{ user.full_name }}</span></span>
                <!-- Profile Dropdown -->
                <div class="relative">
                    <button class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition" @click="toggleProfileMenu">
                        <img :src="user.profile_picture || user.image || 'https://ui-avatars.com/api/?name=Tech&background=e5e7eb&color=374151'" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                        <svg class="w-4 h-4 text-gray-600 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div v-show="showProfileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl overflow-hidden z-50 border border-gray-100" style="display: none;">
                        <a href="../Profile/edit.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                        <a href="../../Auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50" onclick="event.preventDefault(); const targetUrl = this.href || '../../Auth/logout.php'; Swal.fire({ title: 'Logout', text: 'Are you sure you want to logout?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10B981', cancelButtonColor: '#EF4444', confirmButtonText: 'Yes, logout' }).then((result) => { if (result.isConfirmed) { window.location.href = targetUrl; } });">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <transition name="slide-in">
            <aside v-show="sidebarOpen" class="fixed left-0 top-[3.5rem] flex flex-col w-64 h-[calc(100vh-3.5rem)] bg-white border-r border-gray-200 z-50">
                <div class="flex-1 overflow-y-auto py-6 px-4">
                    <nav>
                        <div v-for="(group, index) in menuGroups" :key="index" class="mb-4">
                            <button @click="toggleMenu(index)" class="w-full flex items-center justify-between px-4 py-2 text-md font-medium text-gray-600 hover:text-gray-700 transition-colors">
                                <span class="whitespace-nowrap truncate text-xs uppercase font-bold text-gray-400 tracking-wider">{{ group.title }}</span>
                                <svg :class="{'rotate-180': group.isOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <transition name="slide-fade">
                                <div v-show="group.isOpen" class="mt-2 space-y-1">
                                    <a v-for="item in group.items" :key="item.name" :href="item.link"
                                       :class="item.active ? 'text-gray-700 bg-gray-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-700'"
                                       class="flex items-center px-4 py-2 text-sm transition-colors duration-300 transform rounded-md justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path :d="getIcon(item.icon)" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <span class="mx-4 font-medium">{{ item.name }}</span>
                                        </div>
                                        <span v-if="item.badge && pendingCount > 0" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ pendingCount }}</span>
                                    </a>
                                </div>
                            </transition>
                        </div>
                        <!-- Mobile Status Toggle -->
                        <div class="md:hidden mt-6 pt-4 border-t border-gray-100">
                            <p class="px-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Status</p>
                            <div class="flex gap-2">
                                <button @click="updateStatus('active')" :class="{'bg-emerald-500 text-white': status==='active', 'bg-gray-100 text-gray-600': status!=='active'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition">Online</button>
                                <button @click="updateStatus('offline')" :class="{'bg-gray-500 text-white': status==='offline', 'bg-gray-100 text-gray-600': status!=='offline'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition">Offline</button>
                            </div>
                        </div>
                    </nav>
                </div>
                <!-- Fixed Bottom -->
                <div class="p-4 border-t border-gray-200 bg-white">
                    <nav class="space-y-1">
                        <a href="../Profile/edit.php" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors duration-300 transform rounded-md">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path :d="getIcon('settings')" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="mx-4 font-medium">Settings</span>
                        </a>
                        <a href="../../Auth/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-300 transform rounded-md" onclick="event.preventDefault(); const targetUrl = this.href || '../../Auth/logout.php'; Swal.fire({ title: 'Logout', text: 'Are you sure you want to logout?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10B981', cancelButtonColor: '#EF4444', confirmButtonText: 'Yes, logout' }).then((result) => { if (result.isConfirmed) { window.location.href = targetUrl; } });">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path :d="getIcon('logout')" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="mx-4 font-medium">Logout</span>
                        </a>
                    </nav>
                </div>
            </aside>
        </transition>
    </header>

     <main class="flex-1 min-w-0 transition-all duration-300 ease-in-out mt-14 px-6 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div>
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Account Settings</h1>
                <p class="text-gray-500 mt-1">Update your public profile and account details.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Avatar Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col items-center text-center">
                        <div class="relative mb-5">
                            <img :src="previewImage || user.image || 'https://ui-avatars.com/api/?name=Tech&background=e5e7eb&color=374151'"
                                 @error="handleImageError"
                                 class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-md">
                            <label class="absolute bottom-0 right-0 w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center cursor-pointer hover:bg-emerald-600 transition shadow">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden">
                            </label>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">{{ form.full_name || 'Technician' }}</h2>
                        <p class="text-sm text-gray-400 mt-0.5">{{ form.email }}</p>
                        <span class="mt-3 text-xs font-semibold bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">Technician</span>
                        <p class="text-xs text-gray-400 mt-5">Click the camera icon to change your photo</p>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Info -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-7 py-5 border-b border-gray-100">
                            <h2 class="text-base font-bold text-gray-800">Personal Information</h2>
                            <p class="text-sm text-gray-400">Your basic account details</p>
                        </div>
                        <form @submit.prevent="updateProfile" class="p-7">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">Full Name</label>
                                    <input v-model="form.full_name" type="text" required placeholder="Your full name"
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">Email</label>
                                    <input v-model="form.email" type="email" required placeholder="email@example.com"
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">Phone</label>
                                    <input v-model="form.phone" type="text" placeholder="09xxxxxxxxx"
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">Address</label>
                                    <input v-model="form.address" type="text" placeholder="Your address"
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                                </div>
                            </div>

                            <!-- Professional -->
                            <div class="border-t border-gray-100 pt-6 mb-5">
                                <h3 class="text-sm font-bold text-gray-700 mb-4">Professional Details</h3>
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-gray-700 ml-1">Specialization</label>
                                        <input v-model="form.specialization" type="text" placeholder="e.g. Refrigerators, Air Conditioning, Electronics"
                                               class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                                        <p class="text-xs text-gray-400 ml-1">Comma-separated list of expertise areas.</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-gray-700 ml-1">Bio / Experience</label>
                                        <textarea v-model="form.bio" rows="3" placeholder="Brief description of your skills and experience..."
                                                  class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Security -->
                            <div class="border-t border-gray-100 pt-6 mb-6">
                                <h3 class="text-sm font-bold text-gray-700 mb-4">Change Password</h3>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 ml-1">New Password <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input v-model="form.password" type="password" placeholder="Leave blank to keep current password"
                                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
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

    <!-- Loading Spinner Overlay -->
    <div v-if="loading" class="fixed inset-0 bg-white bg-opacity-70 z-50 flex flex-col items-center justify-center backdrop-blur-sm">
        <svg class="animate-spin h-12 w-12 text-emerald-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-emerald-800 font-semibold animate-pulse">Saving Changes...</span>
    </div>

</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                showProfileMenu: false,
                user: { full_name: 'Technician', image: '' },
                status: 'offline',
                menuGroups: [
                    { title: 'Main', isOpen: true, items: [
                        { name: 'Dashboard', icon: 'dashboard', link: '../Dashboard/index.php', active: false }
                    ]},
                    { title: 'Services', isOpen: true, items: [
                        { name: 'Job Requests', icon: 'jobs', link: '../Jobs/index.php', active: false, badge: true },
                        { name: 'My Reviews', icon: 'reviews', link: '../Reviews/index.php', active: false },
                        { name: 'My Schedule', icon: 'schedule', link: '../Schedule/index.php', active: false },
                        { name: 'My Profile', icon: 'settings', link: '#', active: true }
                    ]}
                ],
                pendingCount: 0,
                loading: false,
                profileFile: null,
                previewImage: null,
                form: {
                    full_name: '',
                    email: '',
                    phone: '',
                    address: '',
                    password: '',
                    specialization: '',
                    bio: '',
                    status: 'offline'
                }
            }
        },
        mounted() {
            window.addEventListener('resize', this.handleResize);
            if (window.innerWidth >= 1024) {
                this.sidebarOpen = true;
            } else {
                this.sidebarOpen = false;
            }
            this.loadProfile();
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
            getIcon(iconName) {
                const icons = {
                    dashboard: 'M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17',
                    jobs: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                    reviews: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                    schedule: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    settings: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                    logout: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'
                };
                return icons[iconName] || '';
            },
            handleImageError(event) {
                 event.target.src = 'https://ui-avatars.com/api/?name=Tech&background=e5e7eb&color=374151';
            },
            async loadProfile() {
                try {
                    const res = await fetch('../../../Controller/tech-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user = data.data;
                        if(data.data.username && !this.user.full_name) this.user.full_name = data.data.username;
                        if(data.data.profile_picture) this.user.image = data.data.profile_picture;

                        this.status = data.data.status || 'offline';
                        this.form = { ...this.form, ...data.data };
                         // Ensure empty strings if null
                        this.form.specialization = data.data.specialization || '';
                        this.form.bio = data.data.bio || '';
                    }
                } catch (e) { console.error(e); }
                
                // Get Job Count for sidebar
                 try {
                    const res = await fetch('../../../Controller/tech-controller.php?action=my_jobs');
                    const data = await res.json();
                    if (data.status === 'success') {
                         this.pendingCount = (data.data || []).filter(j => j.status === 'Pending').length;
                    }
                } catch (e) {}
            },
            updateStatus(newStatus) {
                 this.status = newStatus;
                 fetch('../../../Controller/tech-controller.php?action=update_profile', {
                    method: 'POST',
                    body: JSON.stringify({ status: newStatus })
                });
            },
            handleFileUpload(event) {
                this.profileFile = event.target.files[0];
                if (this.profileFile) {
                    this.previewImage = URL.createObjectURL(this.profileFile);
                }
            },
            async updateProfile() {
                this.loading = true;
                
                try {
                    // Update User Basic Info (and Image)
                    const userFormData = new FormData();
                    userFormData.append('full_name', this.form.full_name);
                    userFormData.append('email', this.form.email);
                    userFormData.append('phone', this.form.phone);
                    userFormData.append('address', this.form.address);
                    if (this.form.password) userFormData.append('password', this.form.password);
                    if (this.profileFile) userFormData.append('profile_picture', this.profileFile);
                    
                    const userRes = await fetch('../../../Controller/user-controller.php?action=update_profile', {
                        method: 'POST',
                        body: userFormData
                    });
                     const userData = await userRes.json();
                    
                     if (userData.status !== 'success') {
                         throw new Error(userData.message);
                     }
                     
                     // Update Tech Specific Info
                     const techRes = await fetch('../../../Controller/tech-controller.php?action=update_profile', {
                        method: 'POST',
                        body: JSON.stringify({ 
                            specialization: this.form.specialization,
                            bio: this.form.bio,
                            status: this.status
                        }),
                        headers: { 'Content-Type': 'application/json' }
                    });
                    const techData = await techRes.json();
                    
                    if (techData.status !== 'success') {
                        throw new Error(techData.message);
                    }
                    
                    Swal.fire('Success', 'Profile updated successfully!', 'success');
                     if (userData.image) {
                        this.user.image = userData.image; 
                    }
                    this.user.full_name = this.form.full_name;

                } catch (e) {
                     Swal.fire('Error', e.message || 'Failed to update profile', 'error');
                } finally {
                    this.loading = false;
                }
            }
        }
    }).mount('#app');
</script>
</body>
</html>

