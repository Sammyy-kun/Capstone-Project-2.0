<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireApprovedOwner();
?>
<?php 
require_once '../../Layouts/auth_check.php';
require '../../Layouts/header.php'; 
?>
<script src="../../../Public/js/owner/sidebar.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/owner/sidebar.js') ?>"></script>
<div id="app" v-cloak>
    <header>
       <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200">
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

    <main class="transition-all duration-300 ease-in-out mt-14 px-6 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div>
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Repair Requests</h1>
                <p class="text-gray-500 mt-1">View and manage all incoming repair requests.</p>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Total</span>
                        <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ repairs.length }}</p>
                    <p class="text-xs text-gray-400 mt-1">All requests</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Pending</span>
                        <div class="w-9 h-9 rounded-xl bg-yellow-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ repairs.filter(r=>r.status==='pending').length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Awaiting action</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">In Progress</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ repairs.filter(r=>r.status==='in_progress').length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Being worked on</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Completed</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ repairs.filter(r=>r.status==='completed').length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Finished jobs</p>
                </div>
            </div>

            <!-- Repair Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">All Requests</h2>
                        <p class="text-sm text-gray-400">Manage and assign repair jobs</p>
                    </div>
                    <span class="text-xs font-semibold bg-gray-100 text-gray-600 px-3 py-1 rounded-full">{{ repairs.length }} total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">ID</th>
                                <th class="px-6 py-4 font-semibold">Customer</th>
                                <th class="px-6 py-4 font-semibold">Issue</th>
                                <th class="px-6 py-4 font-semibold">Type</th>
                                <th class="px-6 py-4 font-semibold">Category</th>
                                <th class="px-6 py-4 font-semibold">Delivery</th>
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-if="repairs.length === 0">
                                <td colspan="9" class="px-6 py-14 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    No repair requests found.
                                </td>
                            </tr>
                            <tr v-for="r in repairs" :key="r.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-xs font-mono font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded">#{{ r.id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-xs font-bold text-emerald-700 flex-shrink-0">
                                            {{ (r.customer_name || 'C').charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800 text-sm">{{ r.customer_name || 'Customer #' + r.user_id }}</div>
                                            <div class="text-xs text-gray-400">{{ r.customer_email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 max-w-[180px] truncate">{{ r.description }}</td>
                                <td class="px-6 py-4">
                                    <span class="capitalize text-xs font-medium bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">{{ r.service_type || 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="capitalize text-xs font-medium bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full">{{ r.issue_category || 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="r.service_type === 'home_service' && parseFloat(r.delivery_fee) > 0">
                                        <span class="text-xs font-semibold text-gray-800">₱{{ parseFloat(r.delivery_fee).toLocaleString() }}</span>
                                        <span class="block text-[10px] uppercase font-bold mt-0.5" :class="r.delivery_payment_method === 'online' ? 'text-blue-500' : 'text-amber-500'">{{ r.delivery_payment_method || 'N/A' }}</span>
                                    </div>
                                    <span v-else class="text-xs text-gray-300">—</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ r.schedule_date || '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="getStatusClass(r.status)">{{ r.status.replace('_',' ').toUpperCase() }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="r.status === 'pending'" class="flex gap-2">
                                        <button @click="acceptRepair(r)" class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Accept
                                        </button>
                                        <button @click="rejectRepair(r)" class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    </div>
                                    <div v-else-if="r.status === 'accepted'" class="flex flex-col gap-2">
                                        <!-- Consultation Status -->
                                        <div v-if="r.consultation_status !== 'consulted'">
                                            <button @click="consultClient(r)" class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition w-full justify-center">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                                Consult Client
                                            </button>
                                            <p class="text-[10px] text-gray-400 mt-1 text-center">Must consult before dispatch</p>
                                        </div>
                                        <div v-else>
                                            <span class="flex items-center gap-1 text-xs font-semibold text-emerald-600 mb-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Consulted
                                            </span>
                                            <button @click="openDispatchModal(r)" class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition w-full justify-center">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                Dispatch
                                            </button>
                                        </div>
                                    </div>
                                    <span v-else class="text-xs text-gray-300">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- =========================================================
             Dispatch Technician Modal
             ========================================================= -->
        <transition name="fade">
            <div v-if="showDispatchModal" class="fixed inset-0 z-[200] flex items-center justify-center">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeDispatchModal"></div>

                <!-- Modal -->
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Dispatch Technician</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Repair #{{ selectedRepair?.id }} — {{ selectedRepair?.description }}</p>
                        </div>
                        <button @click="closeDispatchModal" class="p-2 hover:bg-gray-200 rounded-full transition">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Technician List -->
                    <div class="px-6 py-4 max-h-80 overflow-y-auto">
                        <p v-if="loadingTechs" class="text-center text-gray-400 py-6">Loading technicians…</p>
                        <p v-else-if="technicians.length === 0" class="text-center text-gray-400 py-6">No technicians available.</p>
                        <div v-else class="space-y-2">
                            <label v-for="tech in technicians" :key="tech.id"
                                   class="flex items-center gap-4 p-3 rounded-xl border-2 cursor-pointer transition"
                                   :class="selectedTechId === tech.id ? 'border-blue-500 bg-blue-50' : 'border-gray-100 hover:border-gray-300 hover:bg-gray-50'">
                                <input type="radio" v-model="selectedTechId" :value="tech.id" class="hidden">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-sm flex-shrink-0">
                                    {{ tech.full_name ? tech.full_name.charAt(0) : '?' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate">{{ tech.full_name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ tech.specialization || 'General' }}</p>
                                </div>
                                <span :class="getTechStatusClass(tech.status)" class="capitalize text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0">
                                    {{ tech.status || 'offline' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50">
                        <button @click="closeDispatchModal" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-200 rounded-lg transition">Cancel</button>
                        <button @click="dispatchTechnician" :disabled="!selectedTechId || dispatching"
                                class="px-5 py-2 text-sm font-semibold text-white bg-blue-500 hover:bg-blue-600 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <svg v-if="dispatching" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                            {{ dispatching ? 'Assigning…' : 'Assign Technician' }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </main>
</div>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                showProfileMenu: false,
                user: { name: 'Owner', image: null },
                repairs: [],
                menuGroups: typeof calculateActiveOwnerMenu !== "undefined" ? calculateActiveOwnerMenu(JSON.parse(JSON.stringify(ownerSidebarMenu))) : [],
                logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },

                // Dispatch modal state
                showDispatchModal: false,
                selectedRepair: null,
                technicians: [],
                selectedTechId: null,
                loadingTechs: false,
                dispatching: false
            }
        },
        mounted() {
            this.loadProfile();
            this.loadRepairs();
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
            getIcon(iconName) { return typeof getOwnerIcon !== "undefined" ? getOwnerIcon(iconName) : ""; },

            async loadProfile() {
                try {
                    const res  = await fetch('../../../Controller/user-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user.name  = data.data.full_name;
                        this.user.image = data.data.profile_picture || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.data.full_name) + '&background=e5e7eb&color=374151';
                    }
                } catch(e) {}
            },

            async loadRepairs() {
                try {
                    const res  = await fetch('../../../Controller/repair-controller.php?action=list_owner_repairs');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.repairs = data.data;
                    }
                } catch(e) {}
            },

            // ── Accept ────────────────────────────────────────────────────────
            async acceptRepair(repair) {
                const confirm = await Swal.fire({
                    title: 'Accept this repair request?',
                    text: `Request #${repair.id} from ${repair.customer_name || 'customer'} will be marked as Accepted.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Accept',
                    cancelButtonText: 'Cancel'
                });
                if (!confirm.isConfirmed) return;

                try {
                    const res  = await fetch('../../../Controller/repair-controller.php?action=update_status', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ repair_id: repair.id, status: 'accepted' })
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        repair.status = 'accepted';
                        Swal.fire({ icon: 'success', title: 'Accepted!', text: 'Repair request has been accepted.', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                } catch(e) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.' });
                }
            },

            // ── Reject ────────────────────────────────────────────────────────
            async rejectRepair(repair) {
                const confirm = await Swal.fire({
                    title: 'Reject this repair request?',
                    text: `Request #${repair.id} will be marked as Rejected.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Reject',
                    cancelButtonText: 'Cancel'
                });
                if (!confirm.isConfirmed) return;

                try {
                    const res  = await fetch('../../../Controller/repair-controller.php?action=update_status', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ repair_id: repair.id, status: 'rejected' })
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        repair.status = 'rejected';
                        Swal.fire({ icon: 'success', title: 'Rejected', text: 'Repair request has been rejected.', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                } catch(e) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.' });
                }
            },

            // ── Dispatch Modal ────────────────────────────────────────────────
            async openDispatchModal(repair) {
                this.selectedRepair  = repair;
                this.selectedTechId  = null;
                this.showDispatchModal = true;
                this.loadingTechs    = true;
                this.technicians     = [];

                try {
                    const res  = await fetch('../../../Controller/repair-controller.php?action=list_technicians');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.technicians = data.data;
                    }
                } catch(e) {}
                this.loadingTechs = false;
            },

            closeDispatchModal() {
                this.showDispatchModal = false;
                this.selectedRepair   = null;
                this.selectedTechId   = null;
            },

            async dispatchTechnician() {
                if (!this.selectedTechId || !this.selectedRepair) return;
                this.dispatching = true;

                try {
                    const res  = await fetch('../../../Controller/repair-controller.php?action=dispatch_technician', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ repair_id: this.selectedRepair.id, technician_id: this.selectedTechId })
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        // Update local state
                        const idx = this.repairs.findIndex(r => r.id === this.selectedRepair.id);
                        if (idx !== -1) this.repairs[idx].status = 'in_progress';

                        this.closeDispatchModal();
                        Swal.fire({ icon: 'success', title: 'Technician Dispatched!', text: 'A technician has been assigned to this repair.', timer: 2000, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: data.message });
                    }
                } catch(e) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.' });
                }
                this.dispatching = false;
            },

            // ── Consult Client ─────────────────────────────────────────────
            async consultClient(repair) {
                const { value: notes } = await Swal.fire({
                    title: 'Client Consultation',
                    html: `<p class="text-sm text-gray-600 mb-3">Before dispatching a technician, you must communicate with <strong>${repair.customer_name || 'the customer'}</strong> to discuss the repair details, pricing, and schedule.</p>`,
                    input: 'textarea',
                    inputLabel: 'Consultation Notes (optional)',
                    inputPlaceholder: 'e.g. Discussed pricing, customer confirmed home service schedule...',
                    inputAttributes: { 'aria-label': 'Consultation notes' },
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '✓ Mark as Consulted',
                    cancelButtonText: 'Cancel'
                });

                if (notes === undefined) return; // cancelled

                try {
                    const res = await fetch('../../../Controller/repair-controller.php?action=mark_consulted', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ repair_id: repair.id, notes: notes || '' })
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        repair.consultation_status = 'consulted';
                        repair.consultation_notes = notes || '';
                        Swal.fire({ icon: 'success', title: 'Consulted!', text: 'Client consultation marked. You can now dispatch a technician.', timer: 2000, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                } catch(e) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.' });
                }
            },

            // ── Helpers ───────────────────────────────────────────────────────
            getStatusClass(status) {
                switch(status) {
                    case 'pending':     return 'bg-yellow-100 text-yellow-800';
                    case 'accepted':    return 'bg-emerald-100 text-emerald-800';
                    case 'in_progress': return 'bg-blue-100 text-blue-800';
                    case 'completed':   return 'bg-green-100 text-green-800';
                    case 'rejected':    return 'bg-red-100 text-red-800';
                    default:            return 'bg-gray-100 text-gray-800';
                }
            },

            getTechStatusClass(status) {
                switch(status) {
                    case 'active':  return 'bg-emerald-100 text-emerald-700';
                    case 'busy':    return 'bg-yellow-100 text-yellow-700';
                    case 'offline': return 'bg-gray-100 text-gray-500';
                    default:        return 'bg-gray-100 text-gray-500';
                }
            }
        }
    }).mount('#app');
</script>
</body>
</html>
