<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('owner');
?>
<?php 
require_once '../../Layouts/auth_check.php';
require '../../Layouts/header.php'; 
?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="../../../Public/js/owner/sidebar.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/owner/sidebar.js') ?>"></script>
<div id="app" v-cloak>
    <!--Navbar-->
        <!--Navbar-->
        <!--Navbar-->
    <header>
       <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200" >
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <img src="../../../Public/pictures/menu.svg" alt="Menu" class="w-6 h-6">
                </button>
                <a href="<?= getDashboardUrl() ?>" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition">FixMart</a>
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

    <main class="transition-all duration-300 ease-in-out mt-14 px-6 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div>
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Merchant Dashboard</h1>
                <p class="text-gray-500 mt-1">Welcome back, {{ user.name }}. Here's your business at a glance.</p>
            </div>

            <!-- ===== Application Status Card ===== -->
            <!-- Pending -->
            <div v-if="appStatus === 'Pending'" class="mb-8 bg-white border border-amber-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-amber-50 px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 uppercase tracking-wider">Pending Review</span>
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Your application is under review</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Our team is reviewing your business application. You'll be notified once a decision has been made. Other dashboard features will be unlocked upon approval.</p>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div v-if="appStatus === 'Approved'" class="mb-8 bg-white border border-emerald-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-emerald-50 px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wider">Approved</span>
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Your business is active on FixMart!</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Congratulations! Your application has been approved. All dashboard features are now available to you.</p>
                    </div>
                </div>
            </div>

            <!-- Rejected -->
            <div v-if="appStatus === 'Rejected'" class="mb-8 bg-white border border-red-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-red-50 px-6 py-5 border-b border-red-100 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 uppercase tracking-wider">Rejected</span>
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Your application was not approved</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Please review the reasons below, make the necessary corrections, and resubmit your application.</p>
                    </div>
                    <a href="../Business/apply.php" class="flex-shrink-0 px-5 py-2.5 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition">Reapply Now</a>
                </div>
                <div class="p-6">
                    <p class="text-sm font-semibold text-gray-600 mb-3">Reason(s) for rejection:</p>
                    <ul class="space-y-2 mb-4">
                        <li v-for="reason in parsedRejectionReasons" :key="reason" class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ reason }}
                        </li>
                    </ul>
                    <div v-if="rejectionNote" class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Additional Notes from Admin</p>
                        <p class="text-sm text-gray-700">{{ rejectionNote }}</p>
                    </div>
                </div>
            </div>

            <!-- No application yet -->
            <div v-if="appStatus === null && !appLoading" class="mb-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-6 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-gray-800">No business application found</h3>
                        <p class="text-sm text-gray-500 mt-0.5">You haven't submitted a business application yet. Apply now to unlock all merchant features.</p>
                    </div>
                    <a href="../Business/apply.php" class="flex-shrink-0 px-5 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition">Apply Now</a>
                </div>
            </div>
            <!-- ===== End Status Card ===== -->

            <!-- Dashboard content — blurred/locked when not approved -->
            <div :class="appStatus !== 'Approved' ? 'relative' : ''">
                <!-- Lock overlay for non-approved -->
                <div v-if="appStatus !== 'Approved'" class="absolute inset-0 z-10 bg-white/70 backdrop-blur-sm rounded-2xl flex flex-col items-center justify-center gap-3 py-16">
                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">Dashboard locked until your application is approved</p>
                </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Products</p>
                        <p class="text-2xl font-bold text-gray-800">{{ metrics.products }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Listed in store</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Active Repairs</p>
                        <p class="text-2xl font-bold text-gray-800">{{ metrics.repairs }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Currently in progress</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Monthly Revenue</p>
                        <p class="text-2xl font-bold text-gray-800">{{ metrics.revenue }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">This month's earnings</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <a href="../Repair/history.php" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition group text-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mx-auto mb-3 group-hover:bg-emerald-100 transition">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Repair Jobs</p>
                </a>
                <a href="../Products/distribution.php" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition group text-center">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-100 transition">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Products</p>
                </a>
                <a href="../Inventory/dashboard.php" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-purple-200 hover:shadow-md transition group text-center">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mx-auto mb-3 group-hover:bg-purple-100 transition">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Inventory</p>
                </a>
                <a href="../Technicians/index.php" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-amber-200 hover:shadow-md transition group text-center">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-3 group-hover:bg-amber-100 transition">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Technicians</p>
                </a>
            </div>

            <!-- Sales Chart -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex justify-between items-center px-7 py-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Sales Analytics</h3>
                        <p class="text-sm text-gray-400">Monthly revenue trends</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live
                    </span>
                </div>
                <div class="p-7">
                    <div class="relative h-[320px]">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
            </div><!-- end lock wrapper -->
        </div>
    </main>
    <script src="../../../Public/js/owner/dashboard.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/owner/dashboard.js') ?>"></script>
</div>
</body>
</html>
