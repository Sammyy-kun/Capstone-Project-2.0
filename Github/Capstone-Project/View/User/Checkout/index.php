<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('user');
?>
<?php require '../../Layouts/header.php'; ?>
<script src="../../../Public/js/User/sidebar.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/User/sidebar.js') ?>"></script>

<div id="app" v-cloak>
    <!-- Navbar -->
    <header>
        <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200">
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
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span v-if="unreadCount > 0" class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">{{ unreadCount }}</span>
                    </button>
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
                                     class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer"
                                     :class="{'bg-emerald-50/30': !notif.is_read}">
                                    <div class="flex gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0" v-if="!notif.is_read"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-800 mb-0.5">{{ notif.title }}</p>
                                            <p class="text-xs text-gray-500 leading-relaxed">{{ notif.message }}</p>
                                            <p class="text-[10px] text-gray-400 mt-2">{{ notif.time_ago }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- Profile Menu -->
                <div class="relative">
                    <button @click="toggleProfileMenu" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition">
                        <img :src="user.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=e5e7eb&color=374151'" @error="handleImageError" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-200 bg-white">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div v-show="showProfileMenu" class="absolute right-0 top-12 w-44 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-[100]">
                        <a href="../Profile/edit.php" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>
                        <a href="../../Auth/logout.php" class="flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition border-t border-gray-100" onclick="event.preventDefault(); Swal.fire({ title:'Logout', text:'Are you sure?', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, logout' }).then(r => { if(r.isConfirmed) window.location.href=this.href; });">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <transition name="slide-in">
            <aside v-show="sidebarOpen" class="fixed left-0 top-[3.5rem] flex flex-col w-64 h-[calc(100vh-3.5rem)] px-4 py-6 overflow-y-auto bg-white border-r border-gray-200 z-50">
                <div class="flex flex-col justify-between flex-1">
                    <nav>
                        <div v-for="(group, index) in menuGroups" :key="index" class="mb-4">
                            <button @click="toggleMenu(index)" class="w-full flex items-center justify-between px-4 py-2 text-xs uppercase font-bold text-gray-400 tracking-wider">
                                <span>{{ group.title }}</span>
                                <svg :class="{'rotate-180': group.isOpen}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <transition name="slide-fade">
                                <div v-show="group.isOpen" class="mt-2 space-y-1">
                                    <a v-for="item in group.items" :key="item.name" :href="item.link"
                                       :class="item.active ? 'text-gray-700 bg-gray-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-700'"
                                       class="flex items-center px-4 py-2 text-sm transition rounded-md">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path :d="getIcon(item.icon)" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span class="mx-4 font-medium">{{ item.name }}</span>
                                    </a>
                                </div>
                            </transition>
                        </div>
                    </nav>
                    <div class="mt-auto pt-6 border-t border-gray-200">
                        <a href="../../Auth/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition rounded-md" onclick="event.preventDefault(); Swal.fire({title:'Logout',text:'Are you sure?',icon:'warning',showCancelButton:true,confirmButtonText:'Yes, logout'}).then(r=>{if(r.isConfirmed)window.location.href=this.href;});">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="mx-4 font-medium">Logout</span>
                        </a>
                    </div>
                </div>
            </aside>
        </transition>
    </header>

    <!-- Main Content -->
    <main class="transition-all duration-300 ease-in-out mt-14 px-4 sm:px-10 py-10" :class="{'lg:ml-64': sidebarOpen}">

        <!-- Loading -->
        <div v-if="loading" class="flex flex-col lg:flex-row gap-6 items-start">
            <div class="flex-1 space-y-4">
                <div v-for="i in 3" :key="i" class="bg-gray-100 animate-pulse rounded-2xl h-40"></div>
            </div>
            <div class="w-full lg:w-80 bg-gray-100 animate-pulse rounded-2xl h-72 shrink-0"></div>
        </div>

        <!-- No Items -->
        <div v-else-if="!loading && checkoutItems.length === 0" class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-gray-100 shadow-sm text-gray-400">
            <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <p class="text-lg font-semibold text-gray-500">No items selected</p>
            <p class="text-sm mt-1">Please go back to your cart and select items to checkout.</p>
            <a href="../Cart/index.php" class="mt-6 px-6 py-2.5 bg-emerald-500 text-white rounded-xl text-sm font-medium hover:bg-emerald-600 transition shadow-sm">← Back to Cart</a>
        </div>

        <!-- Checkout Layout -->
        <div v-else class="space-y-5">

            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <a href="../Cart/index.php" class="hover:text-emerald-500 transition">Cart</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-700 font-medium">Checkout</span>
            </div>

            <!-- Main layout: Forms left, Cart summary right -->
            <div class="flex flex-col lg:flex-row gap-6 items-start">

                <!-- LEFT COLUMN: Selected Items + Forms -->
                <div class="flex-1 min-w-0 space-y-5">
                <!-- Selected Items -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 shrink-0 flex items-center justify-between">
                        <h2 class="font-bold text-gray-800">Selected Items</h2>
                        <span class="text-xs text-gray-400 font-medium">{{ totalQty }} item{{ totalQty !== 1 ? 's' : '' }}</span>
                    </div>
                    <!-- flex-1 + min-h-0 lets this area grow to fill the row height and scroll -->
                    <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-gray-50">
                        <div v-for="merchant in groupedMerchants" :key="merchant.owner_id">
                            <div class="flex items-center gap-3 px-5 py-2.5 bg-gray-50/50">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs shrink-0">
                                    {{ merchant.company_name ? merchant.company_name.charAt(0).toUpperCase() : '?' }}
                                </div>
                                <span class="text-xs font-semibold text-gray-600">{{ merchant.company_name || 'Store' }}</span>
                                <span class="text-xs text-gray-400 ml-1">· {{ merchant.items.length }} item{{ merchant.items.length !== 1 ? 's' : '' }}</span>
                            </div>
                            <div class="divide-y divide-gray-50">
                                <div v-for="item in merchant.items" :key="item.cart_item_id" class="flex items-center gap-4 px-5 py-3.5">
                                    <img :src="item.image_url || 'https://placehold.co/56x56/f3f4f6/9ca3af?text=?'" @error="handleProductImgError"
                                         class="w-14 h-14 rounded-xl object-cover border border-gray-100 shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-800 text-sm truncate">{{ item.product_name }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ item.company_name }}</p>
                                        <p class="text-emerald-600 font-bold text-sm mt-1">₱{{ fmtMoney(item.price) }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs text-gray-400">Qty: {{ item.quantity }}</p>
                                        <p class="font-bold text-gray-800 mt-0.5">₱{{ fmtMoney(item.price * item.quantity) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Delivery Address -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-gray-800">Delivery Address</h2>
                        <button @click="showAddressForm = !showAddressForm"
                                class="flex items-center gap-1.5 text-sm text-emerald-500 hover:text-emerald-600 font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ showAddressForm ? 'Cancel' : 'Add New' }}
                        </button>
                    </div>
                    <!-- Address List -->
                    <div v-if="addresses.length === 0 && !showAddressForm" class="text-center py-8 text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-sm">No saved addresses. Add one below.</p>
                    </div>

                    <div class="space-y-3 mb-4">
                        <label v-for="addr in addresses" :key="addr.id"
                               class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition"
                               :class="selectedAddressId == addr.id ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-100 hover:border-gray-200'">
                            <input type="radio" :value="addr.id" v-model="selectedAddressId" @change="onAddressChange"
                                   class="mt-1 text-emerald-500 focus:ring-emerald-500 shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                          :class="addr.label === 'Home' ? 'bg-blue-100 text-blue-700' : addr.label === 'Office' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700'">
                                        {{ addr.label }}
                                    </span>
                                    <span v-if="addr.is_default == 1" class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Default</span>
                                </div>
                                <p class="font-semibold text-gray-800 text-sm">{{ addr.recipient_name }}</p>
                                <p class="text-xs text-gray-500">{{ addr.phone }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ addr.street }}{{ addr.barangay ? ', ' + addr.barangay : '' }}, {{ addr.city }}{{ addr.province ? ', ' + addr.province : '' }} {{ addr.zip_code }}</p>
                            </div>
                        </label>
                    </div>

                    <!-- Add Address Form -->
                    <transition name="slide-fade">
                        <div v-if="showAddressForm" class="border-t border-gray-100 pt-5 mt-2">
                            <p class="text-sm font-bold text-gray-700 mb-4">New Address</p>

                            <!-- Delivery Location Map -->
                            <div class="mb-5">
                                <label class="text-xs font-semibold text-gray-600 block mb-1">📍 Pin Delivery Location</label>
                                <p class="text-xs text-gray-400 mb-2">Click the map or drag the marker — address fields will be filled automatically.</p>
                                <div id="checkoutMap" style="height:260px;border-radius:12px;border:1.5px solid #d1fae5;overflow:hidden;"></div>
                            </div>

                            <div class="flex gap-2 mb-4">
                                <button v-for="lbl in ['Home','Office','Other']" :key="lbl"
                                        @click="newAddress.label = lbl" type="button"
                                        class="px-4 py-1.5 rounded-full text-sm font-semibold border transition"
                                        :class="newAddress.label === lbl ? 'bg-emerald-500 text-white border-emerald-500' : 'border-gray-200 text-gray-600 hover:border-emerald-300'">
                                    {{ lbl }}
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-600">Recipient Name <span class="text-red-400">*</span></label>
                                    <input v-model="newAddress.recipient_name" placeholder="e.g. Juan Dela Cruz"
                                           class="px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none bg-gray-50/50 hover:bg-white focus:bg-white text-sm transition">
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-600">Phone Number <span class="text-red-400">*</span></label>
                                    <input v-model="newAddress.phone" placeholder="e.g. 09171234567"
                                           class="px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none bg-gray-50/50 hover:bg-white focus:bg-white text-sm transition">
                                </div>
                                <div class="flex flex-col gap-1 sm:col-span-2">
                                    <label class="text-xs font-semibold text-gray-600">Street / House No. <span class="text-red-400">*</span></label>
                                    <input v-model="newAddress.street" placeholder="e.g. 123 Rizal St."
                                           class="px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none bg-gray-50/50 hover:bg-white focus:bg-white text-sm transition">
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-600">Barangay</label>
                                    <input v-model="newAddress.barangay" placeholder="e.g. Barangay 1"
                                           class="px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none bg-gray-50/50 hover:bg-white focus:bg-white text-sm transition">
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-600">City / Municipality <span class="text-red-400">*</span></label>
                                    <input v-model="newAddress.city" placeholder="e.g. Quezon City"
                                           class="px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none bg-gray-50/50 hover:bg-white focus:bg-white text-sm transition">
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-600">Province</label>
                                    <input v-model="newAddress.province" placeholder="e.g. Metro Manila"
                                           class="px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none bg-gray-50/50 hover:bg-white focus:bg-white text-sm transition">
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-600">ZIP Code</label>
                                    <input v-model="newAddress.zip_code" placeholder="e.g. 1100"
                                           class="px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none bg-gray-50/50 hover:bg-white focus:bg-white text-sm transition">
                                </div>
                            </div>
                            <label class="flex items-center gap-2 mt-3 cursor-pointer select-none">
                                <input type="checkbox" v-model="newAddress.is_default" class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-500">
                                <span class="text-sm text-gray-600">Set as default address</span>
                            </label>
                            <button @click="saveAddress" :disabled="savingAddress"
                                    class="mt-4 w-full py-3 rounded-xl bg-emerald-500 text-white font-bold text-sm hover:bg-emerald-600 transition shadow-sm disabled:opacity-50">
                                {{ savingAddress ? 'Saving...' : 'Save Address' }}
                            </button>
                        </div>
                    </transition>
                </div>

                <!-- ───────── 3. Delivery Method ───────── -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-800 mb-4">Delivery Method</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-stretch">
                        <button @click="setDeliveryMethod('lalamove')"
                                class="relative flex items-start gap-4 p-4 rounded-xl border-2 text-left transition w-full min-h-[110px]"
                                :class="deliveryMethod === 'lalamove' ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-100 hover:border-gray-200'">
                            <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center text-xl shrink-0">🚚</div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800 text-sm">Third-Party Delivery</p>
                                <p class="text-xs text-gray-400 mt-0.5">via Lalamove</p>
                                <p class="text-xs font-semibold text-emerald-600 mt-1">
                                    <span v-if="fetchingFee" class="text-gray-400">Calculating...</span>
                                    <span v-else>₱{{ fmtMoney(deliveryFee) }}{{ distanceKm ? ' · ' + distanceKm + ' km' : '' }}</span>
                                </p>
                            </div>
                            <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0"
                                 :class="deliveryMethod === 'lalamove' ? 'bg-emerald-500' : 'border-2 border-gray-200'">
                                <svg v-if="deliveryMethod === 'lalamove'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>
                        <button @click="setDeliveryMethod('pickup')"
                                class="relative flex items-start gap-4 p-4 rounded-xl border-2 text-left transition w-full min-h-[110px]"
                                :class="deliveryMethod === 'pickup' ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-100 hover:border-gray-200'">
                            <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center text-xl shrink-0">🏪</div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800 text-sm">Pickup at Store</p>
                                <p class="text-xs text-gray-400 mt-0.5">Self-collect from merchant</p>
                                <p class="text-xs font-semibold text-emerald-600 mt-1">Free</p>
                            </div>
                            <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0"
                                 :class="deliveryMethod === 'pickup' ? 'bg-emerald-500' : 'border-2 border-gray-200'">
                                <svg v-if="deliveryMethod === 'pickup'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- ───────── 4. Payment Method ───────── -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-800 mb-4">Payment Method</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-stretch">
                        <button @click="paymentMethod = 'cod'"
                                class="flex items-start gap-4 p-4 rounded-xl border-2 text-left transition w-full min-h-[90px]"
                                :class="paymentMethod === 'cod' ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-100 hover:border-gray-200'">
                            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-xl shrink-0">💵</div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800 text-sm">Cash on Delivery</p>
                                <p class="text-xs text-gray-400 mt-0.5">Pay when you receive</p>
                            </div>
                            <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0"
                                 :class="paymentMethod === 'cod' ? 'bg-emerald-500' : 'border-2 border-gray-200'">
                                <svg v-if="paymentMethod === 'cod'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>
                        <button @click="paymentMethod = 'online'"
                                class="flex items-start gap-4 p-4 rounded-xl border-2 text-left transition w-full min-h-[90px]"
                                :class="paymentMethod === 'online' ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-100 hover:border-gray-200'">
                            <div class="w-11 h-11 rounded-xl bg-purple-100 flex items-center justify-center text-xl shrink-0">💳</div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800 text-sm">Online Payment</p>
                                <p class="text-xs text-gray-400 mt-0.5">GCash / Card</p>
                            </div>
                            <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0"
                                 :class="paymentMethod === 'online' ? 'bg-emerald-500' : 'border-2 border-gray-200'">
                                <svg v-if="paymentMethod === 'online'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>
                    </div>
                    <div v-if="paymentMethod" class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Selected: <span class="font-semibold text-gray-800">{{ paymentMethod === 'cod' ? 'Cash on Delivery' : 'Online Payment' }}</span>
                    </div>
                </div>

                <!-- ───────── 5. Order Notes ───────── -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-800 mb-1">Order Notes <span class="text-gray-400 font-normal text-sm">(Optional)</span></h2>
                    <p class="text-xs text-gray-400 mb-3">Add instructions for the merchant or delivery rider.</p>
                    <textarea v-model="orderNotes" maxlength="300" rows="3" placeholder="e.g. Please call before delivery, fragile items..."
                              class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none bg-gray-50/50 hover:bg-white focus:bg-white text-sm resize-none transition"></textarea>
                    <p class="text-xs text-right mt-1" :class="orderNotes.length >= 280 ? 'text-orange-500 font-semibold' : 'text-gray-400'">
                        {{ orderNotes.length }} / 300
                    </p>
                </div>

                </div><!-- end left column -->

                <!-- RIGHT COLUMN: Your Cart (sticky) -->
                <div class="w-full lg:w-[45%] shrink-0 sticky top-20">
                <!-- Your Cart -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm flex flex-col">
                    <div class="px-6 pt-6 pb-0 shrink-0">
                        <h2 class="font-bold text-gray-900 text-xl mb-4">Your Cart</h2>
                    </div>
                    <!-- Scrollable item thumbnails — grows to fill remaining height -->
                    <div class="flex-1 min-h-0 overflow-y-auto px-6 space-y-3 pb-3">
                        <div v-for="item in checkoutItems" :key="item.cart_item_id" class="flex items-center gap-3">
                            <div class="relative shrink-0">
                                <img :src="item.image_url || '/Capstone-Project/Public/pictures/no-image.png'"
                                     :alt="item.product_name"
                                     class="w-14 h-14 rounded-lg object-cover border border-gray-100 bg-gray-50">
                                <span class="absolute -top-1.5 -left-1.5 w-5 h-5 rounded-full bg-gray-500 text-white text-[10px] font-bold flex items-center justify-center leading-none">
                                    {{ item.quantity }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ item.product_name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ item.company_name }}</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-800 shrink-0">₱{{ fmtMoney(item.price * item.quantity) }}</span>
                        </div>
                    </div>
                    <!-- Pricing section — always anchored to bottom -->
                    <div class="px-6 pb-6 shrink-0">
                        <div class="border-t border-gray-100 mt-3 mb-4"></div>
                        <!-- Discount code -->
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden mb-4">
                            <div class="pl-3 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 7h.01M3 5a2 2 0 012-2h3.28a2 2 0 011.414.586l7.72 7.72a2 2 0 010 2.828l-3.28 3.28a2 2 0 01-2.828 0L3.586 10.7A2 2 0 013 9.286V5z"/>
                                </svg>
                            </div>
                            <input v-model="discountCode" type="text" placeholder="Discount code"
                                   class="flex-1 px-2 py-2.5 text-sm text-gray-700 placeholder-gray-400 outline-none bg-transparent"/>
                            <button @click="applyDiscount"
                                    class="px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-gray-900 border-l border-gray-200 bg-gray-50 hover:bg-gray-100 transition">
                                Apply
                            </button>
                        </div>
                        <!-- Price breakdown -->
                        <div class="space-y-2.5 text-sm mb-4">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium text-gray-800">₱{{ fmtMoney(itemsTotal) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span class="flex items-center gap-1">
                                    Shipping
                                    <span v-if="distanceKm && deliveryMethod === 'lalamove'" class="text-xs text-gray-400">({{ distanceKm }} km)</span>
                                </span>
                                <span class="font-medium" :class="deliveryMethod === 'pickup' ? 'text-emerald-600' : 'text-gray-800'">
                                    {{ deliveryMethod === 'pickup' ? 'Free' : '₱' + fmtMoney(effectiveDeliveryFee) }}
                                </span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span class="flex items-center gap-1">
                                    Estimated taxes
                                    <span class="relative group cursor-help">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="absolute bottom-5 left-1/2 -translate-x-1/2 w-44 bg-gray-800 text-white text-xs rounded-lg px-2 py-1.5 hidden group-hover:block z-10 text-center leading-snug">
                                            12% VAT is already included in listed prices.
                                        </span>
                                    </span>
                                </span>
                                <span class="font-medium text-gray-800">₱0.00</span>
                            </div>
                            <div v-if="discountAmount > 0" class="flex justify-between text-emerald-600">
                                <span>Discount</span>
                                <span class="font-medium">− ₱{{ fmtMoney(discountAmount) }}</span>
                            </div>
                        </div>
                        <!-- Total -->
                        <div class="flex justify-between items-center border-t border-gray-100 pt-4 mb-4">
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="font-bold text-gray-900 text-xl">₱{{ fmtMoney(totalPayment) }}</span>
                        </div>
                        <!-- Button -->
                        <button @click="placeOrder" :disabled="placingOrder"
                                class="w-full py-3.5 rounded-xl font-bold text-sm transition"
                                :class="placingOrder ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-emerald-500 hover:bg-emerald-600 text-white'">
                            <span v-if="placingOrder" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                Placing Order...
                            </span>
                            <span v-else>Continue to Payment</span>
                        </button>
                        <p class="text-xs text-gray-400 text-center mt-3">By placing your order you agree to our <a href="#" class="underline hover:text-gray-600">Terms of Service</a>.</p>
                    </div>
                </div><!-- end Your Cart -->
                </div><!-- end right column -->

            </div><!-- end main layout -->

        </div><!-- end v-else -->


    </main>

    <!-- Order Confirmation Modal -->
    <transition name="fade">
        <div v-if="showConfirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[200] p-4">
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8 w-full max-w-md text-center">
                <div class="w-20 h-20 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Order Placed!</h2>
                <p class="text-gray-500 text-sm mb-6">Your order #{{ placedOrderId }} has been submitted successfully.</p>
                <div class="flex flex-col gap-3">
                    <a :href="'../Orders/tracking.php?order_id=' + placedOrderId"
                       class="w-full py-3 rounded-xl bg-emerald-500 text-white font-bold text-sm hover:bg-emerald-600 transition shadow-sm">
                        Track Order
                    </a>
                    <a href="../Orders/index.php"
                       class="w-full py-3 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">
                        View All Orders
                    </a>
                    <a href="../Dashboard/index.php"
                       class="w-full py-3 rounded-xl text-gray-400 font-medium text-sm hover:text-gray-600 transition">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </transition>

</div><!-- end #app -->

<style>
[v-cloak] { display: none !important; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-fade-enter-active { transition: all 0.25s ease; }
.slide-fade-leave-active { transition: all 0.2s ease; }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateY(-8px); opacity: 0; }
.slide-in-enter-active, .slide-in-leave-active { transition: transform 0.25s ease; }
.slide-in-enter-from, .slide-in-leave-to { transform: translateX(-100%); }
.overflow-y-auto::-webkit-scrollbar { width: 4px; }
.overflow-y-auto::-webkit-scrollbar-track { background: transparent; }
.overflow-y-auto::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
</style>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            sidebarOpen: window.innerWidth >= 1024,
            showProfileMenu: false,
            user: { name: 'User', image: null },
            notifications: [],
            unreadCount: 0,
            showNotifications: false,
            menuGroups: typeof calculateActiveUserMenu !== 'undefined'
                ? calculateActiveUserMenu(JSON.parse(JSON.stringify(userSidebarMenu)))
                : [],
            loading: true,
            checkoutItems: [],
            addresses: [],
            selectedAddressId: null,
            showAddressForm: false,
            savingAddress: false,
            newAddress: { label: 'Home', recipient_name: '', phone: '', street: '', barangay: '', city: '', province: '', zip_code: '', is_default: false },
            deliveryMethod: 'lalamove',
            deliveryFee: 80,
            distanceKm: null,
            fetchingFee: false,
            paymentMethod: 'cod',
            orderNotes: '',
            discountCode: '',
            discountAmount: 0,
            placingOrder: false,
            showConfirmModal: false,
            placedOrderId: null,
        };
    },
    computed: {
        groupedMerchants() {
            const map = {};
            for (const item of this.checkoutItems) {
                const key = item.owner_id;
                if (!map[key]) map[key] = { owner_id: key, company_name: item.company_name, biz_lat: item.biz_lat, biz_lng: item.biz_lng, items: [] };
                map[key].items.push(item);
            }
            return Object.values(map);
        },
        itemsTotal() { return this.checkoutItems.reduce((s, i) => s + parseFloat(i.price) * parseInt(i.quantity), 0); },
        totalQty()   { return this.checkoutItems.reduce((s, i) => s + parseInt(i.quantity), 0); },
        effectiveDeliveryFee() { return this.deliveryMethod === 'pickup' ? 0 : this.deliveryFee; },
        totalPayment() { return Math.max(0, this.itemsTotal + this.effectiveDeliveryFee - this.discountAmount); },
        selectedAddress() { return this.addresses.find(a => a.id == this.selectedAddressId) || null; },
    },
    watch: {
        showAddressForm(val) {
            if (val) {
                setTimeout(() => this.initAddressMap(), 350);
            } else if (window._checkoutMap) {
                window._checkoutMap.remove();
                window._checkoutMap = null;
            }
        }
    },
    async mounted() {
        this.loadProfile();
        this.fetchNotifications();
        this.pollInterval = setInterval(this.fetchNotifications, 30000);
        window.addEventListener('resize', this.handleResize);
        await this.fetchAddresses();
        await this.fetchItems();
    },
    beforeUnmount() {
        if (this.pollInterval) clearInterval(this.pollInterval);
        window.removeEventListener('resize', this.handleResize);
    },
    methods: {
        handleResize() { this.sidebarOpen = window.innerWidth >= 1024; },
        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
        toggleProfileMenu() { this.showProfileMenu = !this.showProfileMenu; },
        toggleMenu(index) { this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen; },
        getIcon(iconName) { return typeof getUserIcon !== 'undefined' ? getUserIcon(iconName) : ''; },
        async loadProfile() {
            try {
                const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                const data = await res.json();
                if (data.status === 'success') { this.user.name = data.data.full_name || data.data.username; this.user.image = data.data.profile_picture || null; }
            } catch (e) { console.error(e); }
        },
        handleImageError(e) { e.target.src = 'https://ui-avatars.com/api/?name=User&background=e5e7eb&color=374151'; },
        handleProductImgError(e) { e.target.src = '/Capstone-Project/Public/pictures/no-image.png'; },
        toggleNotifications() { this.showNotifications = !this.showNotifications; },
        async fetchNotifications() {
            try {
                const res = await fetch('../../../Controller/notification-controller.php?action=fetch');
                const data = await res.json();
                if (data.status === 'success') { this.notifications = data.data; this.unreadCount = data.unread_count; }
            } catch (e) { console.error(e); }
        },
        async markAllRead() {
            try {
                const res = await fetch('../../../Controller/notification-controller.php?action=mark_all_read');
                const data = await res.json();
                if (data.status === 'success') { this.notifications.forEach(n => n.is_read = 1); this.unreadCount = 0; }
            } catch (e) { console.error(e); }
        },
        async handleNotifClick(notif) {
            if (Number(notif.is_read) === 0) {
                const fd = new FormData(); fd.append('id', notif.id);
                await fetch('../../../Controller/notification-controller.php?action=mark_read', { method: 'POST', body: fd });
                notif.is_read = 1; this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
            if (notif.target_url) window.location.href = notif.target_url;
        },
        async fetchItems() {
            this.loading = true;
            try {
                const raw = sessionStorage.getItem('checkout_cart_ids') || '[]';
                const idsArray = JSON.parse(raw);
                if (!idsArray.length) { this.loading = false; return; }
                const ids = idsArray.join(',');
                const res = await fetch('../../../Controller/checkout-controller.php?action=get_items&ids=' + encodeURIComponent(ids));
                const data = await res.json();
                if (data.status === 'success') { this.checkoutItems = data.data; if (this.selectedAddressId) await this.fetchDeliveryFee(); }
                else Swal.fire('Error', data.message, 'error');
            } catch (e) { Swal.fire('Error', 'Could not load checkout items.', 'error'); }
            finally { this.loading = false; }
        },
        async fetchAddresses() {
            try {
                const res = await fetch('../../../Controller/checkout-controller.php?action=get_addresses');
                const data = await res.json();
                if (data.status === 'success') {
                    this.addresses = data.data;
                    const def = this.addresses.find(a => a.is_default == 1) || this.addresses[0];
                    if (def) this.selectedAddressId = def.id;
                }
            } catch (e) { console.error(e); }
        },
        async onAddressChange() { if (this.deliveryMethod === 'lalamove') await this.fetchDeliveryFee(); },
        async saveAddress() {
            if (!this.newAddress.recipient_name || !this.newAddress.phone || !this.newAddress.street || !this.newAddress.city) {
                Swal.fire('Missing Fields', 'Name, phone, street and city are required.', 'warning'); return;
            }
            this.savingAddress = true;
            try {
                const fd = new FormData();
                Object.entries(this.newAddress).forEach(([k, v]) => fd.append(k, v === true ? '1' : v === false ? '' : v));
                const res = await fetch('../../../Controller/checkout-controller.php?action=save_address', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.status === 'success') {
                    this.addresses.unshift(data.data); this.selectedAddressId = data.data.id; this.showAddressForm = false;
                    this.newAddress = { label: 'Home', recipient_name: '', phone: '', street: '', barangay: '', city: '', province: '', zip_code: '', is_default: false };
                    if (this.deliveryMethod === 'lalamove') await this.fetchDeliveryFee();
                } else Swal.fire('Error', data.message, 'error');
            } catch (e) { Swal.fire('Error', 'Could not save address.', 'error'); }
            finally { this.savingAddress = false; }
        },
        async setDeliveryMethod(method) {
            this.deliveryMethod = method;
            if (method === 'lalamove') await this.fetchDeliveryFee();
            else { this.deliveryFee = 0; this.distanceKm = null; }
        },
        async fetchDeliveryFee() {
            if (!this.selectedAddressId || !this.checkoutItems.length) return;
            this.fetchingFee = true;
            try {
                const ownerId = this.groupedMerchants[0]?.owner_id || 0;
                const res = await fetch(`../../../Controller/checkout-controller.php?action=get_delivery_fee&address_id=${this.selectedAddressId}&owner_id=${ownerId}`);
                const data = await res.json();
                if (data.status === 'success') { this.deliveryFee = data.delivery_fee; this.distanceKm = data.distance_km; }
            } catch (e) { console.error(e); }
            finally { this.fetchingFee = false; }
        },
        applyDiscount() {
            if (!this.discountCode.trim()) return;
            Swal.fire('Info', 'No valid discount codes are active right now.', 'info');
        },
        async placeOrder() {
            if (this.deliveryMethod !== 'pickup' && !this.selectedAddressId) { Swal.fire('Address Required', 'Please select or add a delivery address.', 'warning'); return; }
            if (!this.paymentMethod) { Swal.fire('Payment Required', 'Please choose a payment method.', 'warning'); return; }
            if (!this.checkoutItems.length) { Swal.fire('No Items', 'Your checkout has no items.', 'error'); return; }
            this.placingOrder = true;
            try {
                const payload = {
                    address_id: this.selectedAddressId, delivery_method: this.deliveryMethod,
                    payment_method: this.paymentMethod, delivery_fee: this.effectiveDeliveryFee,
                    distance_km: this.distanceKm, notes: this.orderNotes,
                    cart_item_ids: this.checkoutItems.map(i => i.cart_item_id),
                };
                const res = await fetch('../../../Controller/checkout-controller.php?action=place_order', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (data.status === 'success') {
                    sessionStorage.removeItem('checkout_cart_ids');
                    this.placedOrderId = data.order_id; this.showConfirmModal = true;
                } else Swal.fire('Order Failed', data.message, 'error');
            } catch (e) { Swal.fire('Error', 'Something went wrong. Please try again.', 'error'); }
            finally { this.placingOrder = false; }
        },
        fmtMoney(v) { return Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },

        initAddressMap() {
            if (window._checkoutMap) {
                window._checkoutMap.remove();
                window._checkoutMap = null;
            }
            const defaultLat = 14.5995;
            const defaultLng = 120.9842;
            const map = L.map('checkoutMap').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            const fillFromLatLng = async (lat, lng) => {
                marker.setLatLng([lat, lng]);
                try {
                    const res = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,
                        { headers: { 'Accept-Language': 'en' } }
                    );
                    const geo = await res.json();
                    const addr = geo.address || {};
                    const houseNum = addr.house_number || '';
                    const road = addr.road || addr.pedestrian || addr.path || '';
                    this.newAddress.street = [houseNum, road].filter(Boolean).join(' ');
                    this.newAddress.barangay = addr.suburb || addr.neighbourhood || addr.village || '';
                    this.newAddress.city = addr.city || addr.town || addr.municipality || addr.county || '';
                    this.newAddress.province = addr.state || addr.province || '';
                    this.newAddress.zip_code = addr.postcode || '';
                } catch (e) { console.error('Reverse geocode error', e); }
            };

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        map.setView([lat, lng], 16);
                        fillFromLatLng(lat, lng);
                    },
                    () => {} // permission denied — stay on default view
                );
            }

            marker.on('dragend', e => {
                const { lat, lng } = e.target.getLatLng();
                fillFromLatLng(lat, lng);
            });
            map.on('click', e => fillFromLatLng(e.latlng.lat, e.latlng.lng));

            window._checkoutMap = map;
            setTimeout(() => map.invalidateSize(), 200);
        },
    },
}).mount('#app');
</script>
</body>
</html>
