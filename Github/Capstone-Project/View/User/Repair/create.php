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
                <h1 class="text-3xl font-bold text-gray-800">Request Repair Service</h1>
                <p class="text-gray-500 mt-1">Fill out the form below to submit a repair request.</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-7 py-5 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-800">Service Details</h2>
                    <p class="text-sm text-gray-400">Provide information about the repair you need</p>
                </div>

                <form @submit.prevent="submitRequest" class="p-7 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Service Type</label>
                            <select v-model="form.service_type" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm">
                                <option value="walk_in">Walk-In</option>
                                <option value="home_service">Home Service</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Issue Category</label>
                            <select v-model="form.issue_category" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm">
                                <option value="mechanical">Mechanical</option>
                                <option value="electrical">Electrical</option>
                                <option value="software">Software</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Shop / Service Provider</label>
                            <div class="flex gap-2">
                                <input type="text" :value="selectedShopName" placeholder="No shop selected" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 outline-none bg-gray-50/50 text-sm text-gray-500" readonly>
                                <button type="button" @click="openShopModal" class="flex-shrink-0 inline-flex items-center gap-1.5 bg-emerald-500 text-white px-4 py-3.5 rounded-xl hover:bg-emerald-600 whitespace-nowrap text-sm font-semibold transition shadow-sm shadow-emerald-500/25">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Select Shop
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 ml-1">Click "Select Shop" to choose a service provider.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Preferred Schedule Date</label>
                            <input type="date" v-model="form.schedule_date" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Description of Issue</label>
                        <textarea v-model="form.description" rows="5" required class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm resize-none" placeholder="Describe the problem in detail..."></textarea>
                    </div>

                    <!-- Delivery Fee Section (Home Service Only) -->
                    <div v-if="form.service_type === 'home_service'" class="border-t border-gray-100 pt-6 space-y-5 transition-all duration-300">
                        <!-- Section Header -->
                        <div class="flex items-start gap-3 p-4 bg-blue-50/60 border border-blue-100 rounded-xl">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-blue-900">Delivery / Transportation Fee</h4>
                                <p class="text-xs text-blue-700 mt-1 leading-relaxed">Delivery or transportation costs (e.g., via Lalamove or courier services) are to be covered by the customer. Please enter the estimated fee and select your preferred payment method.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700 ml-1">Estimated Delivery Fee (₱)</label>
                                <input type="number" v-model="form.delivery_fee" min="0" step="1"
                                       class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white text-sm"
                                       placeholder="e.g. 150">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700 ml-1">Payment Method</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label :class="['flex items-center gap-2 px-4 py-3.5 rounded-xl border-2 cursor-pointer transition-all text-sm font-medium', form.delivery_payment_method === 'online' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50/50 text-gray-600 hover:border-emerald-300 hover:bg-white']">
                                        <input type="radio" v-model="form.delivery_payment_method" value="online" class="hidden">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        Online
                                    </label>
                                    <label :class="['flex items-center gap-2 px-4 py-3.5 rounded-xl border-2 cursor-pointer transition-all text-sm font-medium', form.delivery_payment_method === 'cash' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50/50 text-gray-600 hover:border-emerald-300 hover:bg-white']">
                                        <input type="radio" v-model="form.delivery_payment_method" value="cash" class="hidden">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Cash
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Service Location Map -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Service Location</label>
                            <p class="text-xs text-gray-400 ml-1">Click on the map or drag the marker to your exact address. Your location will be auto-detected on load.</p>
                            <div id="repairMap" style="height:280px;border-radius:12px;border:1.5px solid #e5e7eb;overflow:hidden;"></div>
                            <p v-if="form.home_lat" class="text-xs text-gray-500 ml-1">Lat: {{ form.home_lat }}, Lng: {{ form.home_lng }}</p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="submitting"
                                class="w-full inline-flex items-center justify-center gap-2 bg-emerald-500 text-white font-bold py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-md shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60">
                            <span v-if="submitting" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ submitting ? 'Submitting...' : 'Submit Request' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Shop Selection Modal -->
    <div v-if="showShopModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-lg max-h-[85vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100 flex-shrink-0">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Select a Repair Shop</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Choose your preferred service provider</p>
                </div>
                <button type="button" @click="showShopModal = false" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="overflow-y-auto flex-1">
                <div v-if="loadingShops" class="px-7 py-14 text-center text-gray-400">Loading shops...</div>
                <div v-else-if="shops.length === 0" class="px-7 py-14 text-center text-gray-400">No shops available.</div>
                <div v-else class="divide-y divide-gray-50">
                    <div v-for="shop in shops" :key="shop.id"
                         class="flex items-center justify-between px-7 py-4 hover:bg-gray-50 transition-colors gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-sm font-bold text-emerald-700 flex-shrink-0">
                                {{ (shop.business_name || 'S').charAt(0) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ shop.business_name || 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ shop.full_name }}</p>
                            </div>
                        </div>
                        <button type="button" @click="selectShop(shop)"
                                class="flex-shrink-0 text-xs font-semibold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 px-4 py-2 rounded-xl transition">
                            Select
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                showProfileMenu: false,
                user: {
                    name: 'User',
                    image: null
                },
                form: {
                    service_type: 'walk_in',
                    issue_category: 'other',
                    shop_code: '',
                    description: '',
                    schedule_date: '',
                    delivery_fee: '',
                    delivery_payment_method: '',
                    home_lat: '',
                    home_lng: ''
                },
                selectedShopName: '',
                showShopModal: false,
                loadingShops: false,
                shops: [],
                submitting: false,
                notifications: [],
                unreadCount: 0,
                showNotifications: false,
                pollInterval: null,
                menuGroups: typeof calculateActiveUserMenu !== "undefined" ? calculateActiveUserMenu(JSON.parse(JSON.stringify(userSidebarMenu))) : []
            }
        },
        watch: {
            'form.service_type'(val) {
                if (val === 'home_service') {
                    setTimeout(() => this.initRepairMap(), 350);
                } else if (window._repairMap) {
                    window._repairMap.remove();
                    window._repairMap = null;
                }
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
            async loadProfile() {
                try {
                    const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user.name = data.data.full_name || data.data.username;
                        this.user.image = data.data.profile_picture || this.user.image;
                    }
                } catch (e) { console.error("Failed to load profile", e); }
            },
            
            // Repair Form Logic
             async openShopModal() {
                this.showShopModal = true;
                this.loadingShops = true;
                this.shops = [];
                
                try {
                    const res = await fetch('../../../Controller/user-controller.php?action=list_shops');
                    const data = await res.json();
                     if (data.status === 'success') {
                        this.shops = data.data;
                    }
                } catch(e) { console.error(e); } 
                finally {
                    this.loadingShops = false;
                }
            },
            selectShop(shop) {
                this.form.shop_code = shop.shop_code;
                this.selectedShopName = shop.business_name;
                this.showShopModal = false;
            },
             async submitRequest() {
                this.submitting = true;
                const formData = new FormData();
                for (const key in this.form) {
                    formData.append(key, this.form[key]);
                }
                
                try {
                    const res = await fetch('../../../Controller/repair-controller.php?action=request', {
                        method: 'POST',
                        body: formData
                    });
                     const data = await res.json();
                    
                    if (data.status === 'success') {
                         Swal.fire({
                            icon: 'success',
                            title: 'Request Submitted',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '../Dashboard/index.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Failed',
                            text: data.message
                        });
                    }
                } catch (e) {
                     Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error submitting request.'
                    });
                } finally {
                    this.submitting = false;
                }
            },

            initRepairMap() {
                if (window._repairMap) {
                    window._repairMap.remove();
                    window._repairMap = null;
                }
                const defaultLat = 14.5995;
                const defaultLng = 120.9842;
                const map = L.map('repairMap').setView([defaultLat, defaultLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);
                const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
                const updateCoords = (lat, lng) => {
                    this.form.home_lat = lat.toFixed(7);
                    this.form.home_lng = lng.toFixed(7);
                };
                updateCoords(defaultLat, defaultLng);
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        pos => {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;
                            map.setView([lat, lng], 16);
                            marker.setLatLng([lat, lng]);
                            updateCoords(lat, lng);
                        },
                        () => {} // silently ignore denied
                    );
                }
                marker.on('dragend', e => {
                    const { lat, lng } = e.target.getLatLng();
                    updateCoords(lat, lng);
                });
                map.on('click', e => {
                    marker.setLatLng(e.latlng);
                    updateCoords(e.latlng.lat, e.latlng.lng);
                });
                window._repairMap = map;
                setTimeout(() => map.invalidateSize(), 200);
            }
        }
    }).mount('#app');
</script>
</body>
</html>

