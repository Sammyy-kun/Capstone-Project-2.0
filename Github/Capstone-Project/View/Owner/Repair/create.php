<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireApprovedOwner();
?>
<?php require '../../Layouts/header.php'; ?>
<script src="../../../Public/js/owner/sidebar.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/owner/sidebar.js') ?>"></script>
<div id="app" v-cloak>
    <!-- Sidebar / Navbar -->
        <!--Navbar-->
        <!--Navbar-->
    <header>
       <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200" >
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <img src="../../../Public/pictures/menu.svg" alt="Menu" class="w-6 h-6">
                </button>
                <a href="../../User/Home/index.php" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition">FixMart</a>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700">Hello, {{ user.name }}</span>
                
                <button @click="toggleNotifications" class="relative p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span v-if="notifications > 0 || unreadCount > 0" class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">{{ notifications || unreadCount }}</span>
                </button>
                
                <div class="relative">
                    <button id="profileMenuBtn" @click="toggleProfileMenu" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition relative">
                        <img :src="user.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=e5e7eb&color=374151'" @error="handleImageError" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-200 bg-white">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
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
                </div>
            </div>
        </nav>

        <transition name="slide-in">
            <aside v-show="sidebarOpen" class="fixed left-0 top-[3.5rem] flex flex-col w-64 h-[calc(100vh-3.5rem)] px-4 py-6 overflow-y-auto bg-white border-r border-gray-200 z-50">
            <div class="flex flex-col justify-between flex-1">
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
                    <a :href="logoutLink.link" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-300 transform rounded-md" onclick="event.preventDefault(); const targetUrl = this.href || '../../Auth/logout.php'; Swal.fire({ title: 'Logout', text: 'Are you sure you want to logout?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10B981', cancelButtonColor: '#EF4444', confirmButtonText: 'Yes, logout' }).then((result) => { if (result.isConfirmed) { window.location.href = targetUrl; } });">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path :d="getIcon(logoutLink.icon)" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="mx-4 font-medium">{{ logoutLink.name }}</span>
                    </a>
                </div>
            </div>
            </aside>
        </transition>
    </header>

    <main class="transition-all duration-300 ease-in-out mt-14 px-4 sm:px-10 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Profile</h1>
            
            <form @submit.prevent="updateProfile">
                <div class="flex items-center gap-6 mb-6">
                    <img :src="previewImage || user.image" @error="e => e.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=e5e7eb&color=374151'" class="w-24 h-24 rounded-full object-cover border border-gray-200">
                    <div>
                        <label class="block font-semibold mb-2">Profile Picture</label>
                        <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="block w-full text-sm text-gray-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-full file:border-0
                          file:text-sm file:font-semibold
                          file:bg-emerald-50 file:text-emerald-700
                          hover:file:bg-emerald-100
                        "/>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block font-semibold mb-2">Full Name</label>
                        <input v-model="form.full_name" type="text" required class="w-full border rounded p-3">
                    </div>
                    <div>
                        <label class="block font-semibold mb-2">Email</label>
                        <input v-model="form.email" type="email" required class="w-full border rounded p-3">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-2">Username</label>
                    <input v-model="form.username" type="text" disabled class="w-full border rounded p-3 bg-gray-100 text-gray-500">
                </div>

                <div class="mb-4">
                     <label class="block font-semibold mb-2">Business Name</label>
                     <input v-model="form.business_name" type="text" class="w-full border rounded p-3">
                </div>
                
                <div class="mb-4">
                     <label class="block font-semibold mb-2">Address</label>
                     <input v-model="form.address" type="text" class="w-full border rounded p-3">
                </div>
                
                 <div class="mb-4">
                     <label class="block font-semibold mb-2">Phone</label>
                     <input v-model="form.phone" type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full border rounded p-3">
                </div>

                <hr class="my-6 border-gray-200">

                <div class="mb-6">
                    <label class="block font-semibold mb-2">New Password (Optional)</label>
                    <input v-model="form.password" type="password" placeholder="Leave blank to keep current" class="w-full border rounded p-3">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-emerald-500 text-white px-6 py-3 rounded-lg hover:bg-emerald-600 font-bold transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                sidebarOpen: true,
                showProfileMenu: false,
                user: { name: 'Owner', image: null },
                menuGroups: typeof calculateActiveOwnerMenu !== "undefined" ? calculateActiveOwnerMenu(JSON.parse(JSON.stringify(ownerSidebarMenu))) : [],
                logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },
                form: {
                    full_name: '',
                    email: '',
                    username: '',
                    business_name: '',
                    address: '',
                    phone: '',
                    password: ''
                },
                profileFile: null,
                previewImage: null
            }
        },
        mounted() {
            this.loadProfile();
        },
        methods: {
            toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
            toggleProfileMenu() { this.showProfileMenu = !this.showProfileMenu; },
            toggleMenu(index) { this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen; },
            getIcon(iconName) { return typeof getOwnerIcon !== "undefined" ? getOwnerIcon(iconName) : ""; },
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
                        this.user.image = data.data.profile_picture || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.data.full_name)}&background=e5e7eb&color=374151`;
                    }
                } catch(e) { console.error(e); }
            },
            async updateProfile() {
                const formData = new FormData();
                for (const key in this.form) {
                    formData.append(key, this.form[key]);
                }
                if (this.profileFile) {
                    formData.append('profile_picture', this.profileFile);
                }

                Swal.fire({
                    title: 'Saving Changes...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

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
                }
            }
        }
    }).mount('#app');
</script>
</body>
</html>

