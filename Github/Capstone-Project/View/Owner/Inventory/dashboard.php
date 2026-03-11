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
    <!-- Sidebar / Navbar Structure -->
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

    <main class="transition-all duration-300 ease-in-out mt-14 px-6 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div>
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Inventory Management</h1>
                    <p class="text-gray-500 mt-1">Track spare parts, suppliers, and stock levels.</p>
                </div>
                <div class="flex gap-3">
                    <button @click="showAddPartModal = true"
                            class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-md shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Part
                    </button>
                    <button @click="showAddSupplierModal = true"
                            class="inline-flex items-center gap-2 border border-emerald-500 text-emerald-600 bg-white hover:bg-emerald-50 px-5 py-2.5 rounded-xl font-semibold transition-all hover:-translate-y-0.5 active:translate-y-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Add Supplier
                    </button>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Total Parts</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ parts.length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Unique spare parts</p>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Total Stock</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ parts.reduce((s,p)=>s+Number(p.stock),0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Total items in stock</p>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Low Stock</span>
                        <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ lowStock.length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Parts need restocking</p>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Suppliers</span>
                        <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ suppliers.length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Registered suppliers</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Spare Parts Table -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Spare Parts</h2>
                            <p class="text-sm text-gray-400">All registered inventory items</p>
                        </div>
                        <span class="text-xs font-semibold bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full">{{ parts.length }} items</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-4 font-semibold">Part Name</th>
                                    <th class="px-6 py-4 font-semibold">Part Number</th>
                                    <th class="px-6 py-4 font-semibold">Stock</th>
                                    <th class="px-6 py-4 font-semibold">Price</th>
                                    <th class="px-6 py-4 font-semibold">Supplier</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="part in parts" :key="part.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ part.part_name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ part.part_number || '—' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full"
                                              :class="part.stock <= part.reorder_level ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="part.stock <= part.reorder_level ? 'bg-red-500' : 'bg-green-500'"></span>
                                            {{ part.stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">₱{{ Number(part.price).toLocaleString('en-PH', {minimumFractionDigits:2}) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ part.supplier_name || 'N/A' }}</td>
                                </tr>
                                <tr v-if="parts.length === 0">
                                    <td colspan="5" class="px-6 py-14 text-center text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                                        No parts found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Side Panels -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Low Stock Alerts -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-red-50 bg-red-50 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <h2 class="text-base font-bold text-red-700">Low Stock Alerts</h2>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-3">
                                <li v-for="low in lowStock" :key="low.id" class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-800">{{ low.part_name }}</span>
                                    <span class="text-xs font-bold bg-red-100 text-red-700 px-2 py-0.5 rounded-full">{{ low.stock }} left</span>
                                </li>
                                <li v-if="lowStock.length === 0" class="flex items-center gap-2 text-sm text-green-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    All stock levels are good.
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Suppliers -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="text-base font-bold text-gray-800">Suppliers</h2>
                            <span class="text-xs font-semibold bg-blue-50 text-blue-600 px-3 py-1 rounded-full">{{ suppliers.length }}</span>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-3">
                                <li v-for="s in suppliers" :key="s.id" class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">{{ s.name.charAt(0).toUpperCase() }}</div>
                                        <span class="text-sm font-medium text-gray-800">{{ s.name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">ID: {{ s.id }}</span>
                                </li>
                                <li v-if="suppliers.length === 0" class="text-sm text-gray-400 text-center py-4">No suppliers found.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Part Modal -->
    <div v-if="showAddPartModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-[70] px-4">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-md p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Add Spare Part</h2>
                    <p class="text-gray-500 text-sm mt-1">Register a new inventory item</p>
                </div>
                <button type="button" @click="showAddPartModal = false" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="addPart" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Part Name</label>
                    <input v-model="partForm.name" type="text" required placeholder="e.g. Compressor Motor"
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Part Number Components</label>
                    <div class="grid grid-cols-4 gap-2">
                        <input v-model="partForm.category" type="text" placeholder="CAT" required title="Category (e.g. ELE)"
                               class="px-3 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-xs uppercase text-center">
                        <input v-model="partForm.type" type="text" placeholder="TYPE" required title="Type (e.g. MTR)"
                               class="px-3 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-xs uppercase text-center">
                        <input v-model="partForm.spec" type="text" placeholder="SPEC" required title="Spec (e.g. 12V)"
                               class="px-3 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-xs uppercase text-center">
                        <input v-model="partForm.version" type="text" placeholder="VER" title="Version (Default A)"
                               class="px-3 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-xs uppercase text-center">
                    </div>
                    <p class="text-[11px] text-emerald-600 font-medium ml-1">
                        Preview: <span class="font-mono">{{ (partForm.category || '???') + '-' + (partForm.type || '???') + '-' + (partForm.spec || '???') + '-000-' + (partForm.version || 'A') }}</span>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Stock</label>
                        <input v-model="partForm.stock" type="number" required placeholder="0"
                               class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Price</label>
                        <input v-model="partForm.price" type="number" step="0.01" required placeholder="0.00"
                               class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Supplier</label>
                    <select v-model="partForm.supplier_id" required
                            class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white appearance-none">
                        <option value="" disabled selected>Select Supplier</option>
                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showAddPartModal = false"
                            class="flex-1 py-3.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-300">
                        Add Part
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div v-if="showAddSupplierModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-[70] px-4">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-md p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Add Supplier</h2>
                    <p class="text-gray-500 text-sm mt-1">Register a new parts supplier</p>
                </div>
                <button type="button" @click="showAddSupplierModal = false" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="addSupplier" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Supplier Name</label>
                    <input v-model="supplierForm.name" type="text" required placeholder="e.g. Ydan Trading"
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Contact Info</label>
                    <input v-model="supplierForm.contact" type="text" placeholder="Phone or email"
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Address</label>
                    <textarea v-model="supplierForm.address" rows="3" placeholder="Full address"
                              class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showAddSupplierModal = false"
                            class="flex-1 py-3.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-300">
                        Add Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                // Sidebar Data
                sidebarOpen: window.innerWidth >= 1024,
                showProfileMenu: false,
                user: {
                    name: 'Owner', // Loaded dynamically
                    image: null
                },
                menuGroups: typeof calculateActiveOwnerMenu !== "undefined" ? calculateActiveOwnerMenu(JSON.parse(JSON.stringify(ownerSidebarMenu))) : [],
                logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },

                // Inventory Data
                parts: [],
                lowStock: [],
                suppliers: [],
                showAddPartModal: false,
                showAddSupplierModal: false,
                partForm: {
                    name: '',
                    category: '',
                    type: '',
                    spec: '',
                    version: 'A',
                    stock: '',
                    price: '',
                    supplier_id: ''
                },
                supplierForm: { name: '', contact: '', address: '' },
                logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },

                // Inventory Data
                parts: [],
                lowStock: [],
                suppliers: [],
                showAddPartModal: false,
                showAddSupplierModal: false,
                // partForm and supplierForm defined above
                
                // Notifications Data
                notifications: [],
                unreadCount: 0,
                showNotifications: false,
                pollInterval: null
            }
        },
        mounted() {
            this.loadProfile();
            this.loadParts();
            this.loadSuppliers();
            this.checkLowStock();
            this.fetchNotifications();
            // Poll for notifications every 30 seconds
            this.pollInterval = setInterval(this.fetchNotifications, 30000);
            window.addEventListener('resize', this.handleResize);
        },
        beforeUnmount() {
            if (this.pollInterval) clearInterval(this.pollInterval);
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
            async loadProfile() {
                 try {
                    const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user.name = data.data.full_name;
                        this.user.image = data.data.profile_picture || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.data.full_name) + '&background=e5e7eb&color=374151';
                    }
                } catch(e) { console.error(e); }
            },
            // Sidebar Methods
            toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
            toggleMenu(index) { this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen; },
            getIcon(iconName) { return typeof getOwnerIcon !== "undefined" ? getOwnerIcon(iconName) : ""; },
            toggleProfileMenu() {
                this.showProfileMenu = !this.showProfileMenu;
            },

            // Inventory Methods
            async loadParts() {
                try {
                    const res = await fetch('../../../Controller/inventory-controller.php?action=list_parts');
                    const data = await res.json();
                    if (data.status === 'success') this.parts = data.data;
                } catch(e) { console.error(e); }
            },
            async loadSuppliers() {
               try {
                    const res = await fetch('../../../Controller/inventory-controller.php?action=list_suppliers');
                    const data = await res.json();
                    if (data.status === 'success') this.suppliers = data.data;
                } catch(e) { console.error(e); }
            },
            async checkLowStock() {
                try {
                    const res = await fetch('../../../Controller/inventory-controller.php?action=check_low_stock');
                    const data = await res.json();
                    if (data.status === 'success') this.lowStock = data.data;
                } catch(e) { console.error(e); }
            },
            async addPart() {
                 const formData = new FormData();
                 for(let key in this.partForm) formData.append(key, this.partForm[key]);
                 
                 Swal.fire({
                    title: 'Adding Part...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                 });

                 try {
                     const res = await fetch('../../../Controller/inventory-controller.php?action=add_part', { method: 'POST', body: formData });
                     const data = await res.json();
                     
                     if(data.status === 'success') {
                         Swal.fire({
                            icon: 'success',
                            title: 'Part Added',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                         });
                         this.showAddPartModal = false;
                         this.partForm = { name: '', category: '', type: '', spec: '', version: 'A', stock: '', price: '', supplier_id: '' };
                         this.loadParts();
                     } else {
                         Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                         });
                     }
                 } catch(e) {
                      Swal.fire({ icon: 'error', title: 'Error', text: 'Error adding part' });
                 }
            },
            async addSupplier() {
                const formData = new FormData();
                for(let key in this.supplierForm) formData.append(key, this.supplierForm[key]);
                
                Swal.fire({
                    title: 'Adding Supplier...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                 });

                try {
                    const res = await fetch('../../../Controller/inventory-controller.php?action=add_supplier', { method: 'POST', body: formData });
                    const data = await res.json();
                    
                    if(data.status === 'success') {
                         Swal.fire({
                            icon: 'success',
                            title: 'Supplier Added',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                         });
                         this.showAddSupplierModal = false;
                         this.supplierForm = { name: '', contact: '', address: '' };
                         this.loadSuppliers();
                     } else {
                         Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                         });
                     }
                } catch(e) {
                     Swal.fire({ icon: 'error', title: 'Error', text: 'Error adding supplier' });
                }
            },

            // Notification Methods
            toggleNotifications() { this.showNotifications = !this.showNotifications; },
            
            async fetchNotifications() {
                try {
                    const res = await fetch('../../../Controller/notification-controller.php?action=fetch');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.notifications = data.data;
                        this.unreadCount = data.unread_count;
                    }
                } catch(e) { console.error('Error fetching notifications:', e); }
            },

            async markRead(notif) {
                if (Number(notif.is_read) === 1) return;
                try {
                    const formData = new FormData();
                    formData.append('id', notif.id);
                    await fetch('../../../Controller/notification-controller.php?action=mark_read', { method: 'POST', body: formData });
                    
                    // Update local state immediately
                    notif.is_read = 1;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch(e) { console.error(e); }
            },

            async markAllRead() {
                if (this.unreadCount === 0) return;
                try {
                    await fetch('../../../Controller/notification-controller.php?action=mark_all_read');
                    this.notifications.forEach(n => n.is_read = 1);
                    this.unreadCount = 0;
                } catch(e) { console.error(e); }
            },

            async removeNotification(id) {
                const result = await Swal.fire({
                    title: 'Are you sure?',
                    text: "Remove this notification?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                });

                if (!result.isConfirmed) return;

                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    await fetch('../../../Controller/notification-controller.php?action=delete', { method: 'POST', body: formData });
                    
                    const notif = this.notifications.find(n => n.id === id);
                    if (notif && Number(notif.is_read) === 0) {
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                    this.notifications = this.notifications.filter(n => n.id !== id);
                    Swal.fire({
                        title: 'Removed!',
                        text: 'Notification has been removed.',
                        icon: 'success',
                        timer: 1000,
                        showConfirmButton: false
                    });
                } catch(e) { console.error(e); }
            },

            formatDate(dateStr) {
                const date = new Date(dateStr);
                const now = new Date();
                const diffMs = now - date;
                const diffSec = Math.round(diffMs / 1000);
                const diffMin = Math.round(diffSec / 60);
                const diffHr = Math.round(diffMin / 60);
                const diffDay = Math.round(diffHr / 24);

                if (diffSec < 60) return 'Just now';
                if (diffMin < 60) return `${diffMin}m ago`;
                if (diffHr < 24) return `${diffHr}h ago`;
                if (diffDay < 7) return `${diffDay}d ago`;
                
                return date.toLocaleDateString();
            },

            getTypeClass(type) {
                const classes = {
                    info: 'border-blue-200 text-blue-600 bg-blue-50',
                    success: 'border-green-200 text-green-600 bg-green-50',
                    warning: 'border-yellow-200 text-yellow-600 bg-yellow-50',
                    error: 'border-red-200 text-red-600 bg-red-50'
                };
                return classes[type] || classes.info;
            }
        }
    }).mount('#app');
</script>
</body>
</html>

