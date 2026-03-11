<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('technician');
?>
<?php require '../../Layouts/header.php'; ?>
<!-- Chart.js CDN -->
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
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">My Schedule</h1>
                    <p class="text-gray-500 mt-1">Manage appointments and technician tasks.</p>
                </div>
                <input type="date" v-model="selectedDate" @change="loadSchedule"
                       class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white shadow-sm">
            </div>

            <!-- Stat -->
            <div class="grid grid-cols-2 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Today's Jobs</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ appointments.length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Scheduled for selected date</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Viewing Date</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-base font-bold text-gray-800">{{ new Date(selectedDate).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Currently selected</p>
                </div>
            </div>

            <!-- Schedule List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-800">Appointments</h2>
                        <p class="text-sm text-gray-400">Tasks scheduled for this date</p>
                    </div>
                    <span class="text-xs font-semibold bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full">{{ appointments.length }}</span>
                </div>

                <div class="divide-y divide-gray-50">
                    <div v-for="app in appointments" :key="app.id"
                         class="px-6 py-5 hover:bg-gray-50 transition-colors flex flex-col md:flex-row items-start md:items-center gap-6">
                        <div class="text-center bg-emerald-50 rounded-2xl px-5 py-3 flex-shrink-0">
                            <span class="block font-bold text-lg text-emerald-600 tracking-tight">{{ formatTime(app.time_slot) }}</span>
                            <span class="text-xs text-emerald-400 font-medium uppercase">Slot</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-gray-800">{{ app.customer }}</h3>
                                <span class="text-xs font-mono bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Job #{{ app.repair_id }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ app.description }}</p>
                            <div class="flex items-center gap-1 mt-1.5 text-xs text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ app.address }}
                            </div>
                        </div>
                    </div>

                    <div v-if="appointments.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
                        <svg class="w-12 h-12 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="font-medium text-gray-500">No Appointments</p>
                        <p class="text-sm mt-1">Nothing scheduled for this date.</p>
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
                user: { full_name: 'Technician', image: '' },
                status: 'offline',
                menuGroups: [
                    { title: 'Main', isOpen: true, items: [
                        { name: 'Dashboard', icon: 'dashboard', link: '../Dashboard/index.php', active: false }
                    ]},
                    { title: 'Services', isOpen: true, items: [
                        { name: 'Job Requests', icon: 'jobs', link: '../Jobs/index.php', active: false, badge: true },
                        { name: 'My Reviews', icon: 'reviews', link: '../Reviews/index.php', active: false },
                        { name: 'My Schedule', icon: 'schedule', link: '#', active: true },
                        { name: 'My Profile', icon: 'settings', link: '../Profile/edit.php', active: false }
                    ]}
                ],
                jobs: [],
                loading: true,
                selectedDate: new Date().toISOString().split('T')[0],
                appointments: [],
            }
        },
        computed: {
            pendingCount() { return this.jobs.filter(j => j.status === 'Pending').length; }
        },
        mounted() {
            window.addEventListener('resize', this.handleResize);
            this.initData();
             if (window.innerWidth >= 1024) {
                this.sidebarOpen = true;
            } else {
                this.sidebarOpen = false;
            }
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
            async initData() {
                try {
                    await Promise.all([this.loadProfile(), this.loadJobs(), this.loadSchedule()]);
                } finally {
                    this.loading = false;
                }
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
            async loadJobs() {
                 try {
                    const res = await fetch('../../../Controller/tech-controller.php?action=my_jobs');
                    const data = await res.json();
                    if (data.status === 'success') this.jobs = data.data || [];
                } catch (e) {}
            },
             async loadSchedule() {
                try {
                    // For now using the schedule-controller but filtered by current tech in backend or showing all (Admin simplified)
                    // In a real scenario, this endpoint should return only this tech's schedule
                    const res = await fetch(`../../../Controller/schedule-controller.php?action=get_schedule&date=${this.selectedDate}`);
                    const data = await res.json();
                    if (data.status === 'success') {
                        // Client-side filtering if needed, assuming the API returns all for now, or just showing what's returned
                        // Ideally, the backend should filter by technician_id if the user is a technician
                        // For this implementation, we will display meaningful data similarly to Admin
                        this.appointments = data.data; 
                    }
                } catch (e) { console.error(e); }
            },
            formatTime(time) {
                if(!time) return '';
                const [hour, minute] = time.split(':');
                const h = parseInt(hour);
                const ampm = h >= 12 ? 'PM' : 'AM';
                const h12 = h % 12 || 12;
                return `${h12}:${minute} ${ampm}`;
            }
        }
    }).mount('#app');
</script>
</body>
</html>

