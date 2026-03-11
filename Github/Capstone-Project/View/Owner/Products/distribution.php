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
    <!-- Sidebar / Navbar Structure (Copied from Dashboard) -->
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
                    <h1 class="text-3xl font-bold text-gray-800">Product Distribution</h1>
                    <p class="text-gray-500 mt-1">Manage your listed products and stock.</p>
                </div>
                <button @click="showAddProductModal = true"
                        class="inline-flex items-center gap-2 bg-emerald-500 text-white px-5 py-2.5 rounded-xl hover:bg-emerald-600 transition font-medium shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Product
                </button>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Products</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ products.length }}</p>
                    <p class="text-xs text-gray-400 mt-1">Total product types</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">Total Stock</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5l5 5v5m-5-5h5M7 3v18m0 0h10a2 2 0 002-2V8M7 21a2 2 0 01-2-2V5a2 2 0 012-2"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ products.reduce((s,p)=>s+Number(p.qty),0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Total units available</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Product List Panel -->
                <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-fit">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-base font-bold text-gray-800">Select Product</h2>
                        <span class="text-xs font-semibold bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full">{{ products.length }}</span>
                    </div>
                    <div class="p-4 space-y-2">
                        <div v-for="product in products" :key="product.id"
                             @click="selectProduct(product)"
                             class="p-4 rounded-xl border-2 cursor-pointer transition"
                             :class="selectedProduct && selectedProduct.id === product.id ? 'border-emerald-400 bg-emerald-50' : 'border-gray-100 hover:border-gray-200 hover:bg-gray-50'">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-sm text-gray-800">{{ product.product_name }}</p>
                                <span class="text-xs font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ product.qty }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-xs text-gray-400">Click to view serials</p>
                                <div class="flex space-x-1" @click.stop>
                                    <button @click="openEditModal(product)" class="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded transition" title="Edit Product">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="confirmDelete(product.id)" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition" title="Remove Product">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-if="products.length === 0" class="py-10 text-center text-gray-400 text-sm">
                            No products yet. Add one above.
                        </div>
                    </div>
                </div>

                <!-- Serial Numbers Panel -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div v-if="selectedProduct">
                        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-base font-bold text-gray-800">{{ selectedProduct.product_name }}</h2>
                                <p class="text-sm text-gray-400">{{ serials.length }} serial numbers</p>
                            </div>
                            <button @click="showAddSerialModal = true"
                                    class="inline-flex items-center gap-2 bg-emerald-500 text-white px-4 py-2 rounded-xl hover:bg-emerald-600 transition text-sm font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Serials
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                                        <th class="px-6 py-4 font-semibold">Serial Number</th>
                                        <th class="px-6 py-4 font-semibold">Status</th>
                                        <th class="px-6 py-4 font-semibold">Distributed To</th>
                                        <th class="px-6 py-4 font-semibold">Date Added</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr v-for="serial in serials" :key="serial.serial_number" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4"><span class="text-xs font-mono bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ serial.serial_number }}</span></td>
                                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="getStatusClass(serial.status)">{{ serial.status }}</span></td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ serial.distributed_to || '—' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-400">{{ serial.created_at }}</td>
                                    </tr>
                                    <tr v-if="serials.length === 0">
                                        <td colspan="4" class="px-6 py-14 text-center text-gray-400">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            No serials found for this product.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center py-24 text-gray-400">
                        <svg class="w-12 h-12 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                        <p class="text-sm font-medium">Select a product from the left</p>
                        <p class="text-xs mt-1">to view its serial numbers</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Serials Modal -->
    <div v-if="showAddSerialModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-[70]">
        <div class="bg-white p-8 rounded-xl w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Add Serial Numbers</h2>
            <p class="text-sm text-gray-600 mb-4">Enter serial numbers separated by commas or new lines.</p>
            <form @submit.prevent="addSerials">
                <textarea v-model="newSerials" rows="5" class="w-full border rounded p-2 focus:outline-emerald-500 mb-4" placeholder="SN001, SN002..."></textarea>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="showAddSerialModal = false" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-500 text-white rounded hover:bg-emerald-600">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div v-if="showAddProductModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-[70] px-4">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-md p-8 overflow-y-auto max-h-[90vh]">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Add New Product</h2>
                    <p class="text-gray-500 text-sm mt-1">Fill in the details to list a new product</p>
                </div>
                <button type="button" @click="showAddProductModal = false" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="addProduct" enctype="multipart/form-data" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Product Name</label>
                    <input v-model="productForm.product_name" type="text" required placeholder="e.g. Samsung Inverter AC 1.5HP"
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Description</label>
                    <textarea v-model="productForm.description" rows="2" placeholder="Briefly describe the product…"
                              class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white resize-none"></textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Category <span class="text-red-400">*</span></label>
                    <div class="grid grid-cols-4 gap-2">
                        <button v-for="cat in productCategories" :key="cat.value" type="button"
                                @click="productForm.category = cat.value"
                                :class="productForm.category === cat.value
                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                                    : 'border-gray-200 bg-gray-50/50 text-gray-500 hover:border-emerald-300 hover:bg-white'"
                                class="flex flex-col items-center gap-1.5 border rounded-xl py-3 px-1 transition-all text-center">
                            <span class="text-2xl leading-none">{{ cat.icon }}</span>
                            <span class="text-[10px] font-semibold leading-tight">{{ cat.label }}</span>
                        </button>
                    </div>
                    <p v-if="!productForm.category" class="text-[11px] text-emerald-600 font-medium ml-1">Select a category above.</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Price (₱)</label>
                        <input v-model="productForm.price" type="number" step="0.01" min="0" required placeholder="0.00"
                               class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Initial Qty</label>
                        <input v-model="productForm.stock" type="number" min="0" required placeholder="0"
                               class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Product Images</label>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50">
                            <span class="text-xs font-semibold text-gray-700 w-24 shrink-0">Main Image <span class="text-red-400">*</span></span>
                            <input type="file" ref="image_1" accept="image/*" required
                                   class="flex-1 text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-500 file:text-white hover:file:bg-emerald-600 cursor-pointer">
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50">
                            <span class="text-xs font-semibold text-gray-500 w-24 shrink-0">Image 2</span>
                            <input type="file" ref="image_2" accept="image/*"
                                   class="flex-1 text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-600 hover:file:bg-gray-300 cursor-pointer">
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50">
                            <span class="text-xs font-semibold text-gray-500 w-24 shrink-0">Image 3</span>
                            <input type="file" ref="image_3" accept="image/*"
                                   class="flex-1 text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-600 hover:file:bg-gray-300 cursor-pointer">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showAddProductModal = false"
                            class="flex-1 py-3.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" :disabled="!productForm.category"
                            class="flex-1 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-[70] px-4">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-md p-8 overflow-y-auto max-h-[90vh]">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Product</h2>
                    <p class="text-gray-500 text-sm mt-1">Update the product details below</p>
                </div>
                <button type="button" @click="showEditModal = false" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="updateProduct" enctype="multipart/form-data" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Product Name</label>
                    <input v-model="editForm.product_name" type="text" required
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Description</label>
                    <textarea v-model="editForm.description" rows="2"
                              class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white resize-none"></textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Category</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button v-for="cat in productCategories" :key="cat.value" type="button"
                                @click="editForm.category = cat.value"
                                :class="editForm.category === cat.value
                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                                    : 'border-gray-200 bg-gray-50/50 text-gray-500 hover:border-emerald-300 hover:bg-white'"
                                class="flex flex-col items-center gap-1.5 border rounded-xl py-3 px-1 transition-all text-center">
                            <span class="text-2xl leading-none">{{ cat.icon }}</span>
                            <span class="text-[10px] font-semibold leading-tight">{{ cat.label }}</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Price (₱)</label>
                        <input v-model="editForm.price" type="number" step="0.01" min="0" required
                               class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Stock / Qty</label>
                        <input v-model="editForm.qty" type="number" min="0" required
                               class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-gray-50/50 hover:bg-white focus:bg-white">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Replace Images <span class="text-gray-400 font-normal text-xs">(optional)</span></label>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50">
                            <span class="text-xs font-semibold text-gray-700 w-24 shrink-0">Main Image</span>
                            <input type="file" ref="edit_image_1" accept="image/*"
                                   class="flex-1 text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-500 file:text-white hover:file:bg-emerald-600 cursor-pointer">
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50">
                            <span class="text-xs font-semibold text-gray-500 w-24 shrink-0">Image 2</span>
                            <input type="file" ref="edit_image_2" accept="image/*"
                                   class="flex-1 text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-600 hover:file:bg-gray-300 cursor-pointer">
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50">
                            <span class="text-xs font-semibold text-gray-500 w-24 shrink-0">Image 3</span>
                            <input type="file" ref="edit_image_3" accept="image/*"
                                   class="flex-1 text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-600 hover:file:bg-gray-300 cursor-pointer">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showEditModal = false"
                            class="flex-1 py-3.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-300 cursor-pointer">
                        Save Changes
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
                sidebarOpen: window.innerWidth >= 1024,
                sidebarOpen: window.innerWidth >= 1024,
                showProfileMenu: false,
                user: { name: 'Owner', image: null },
                menuGroups: typeof calculateActiveOwnerMenu !== "undefined" ? calculateActiveOwnerMenu(JSON.parse(JSON.stringify(ownerSidebarMenu))) : [],
                logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },

                products: [],
                selectedProduct: null,
                serials: [],
                showAddSerialModal: false,
                newSerials: '',
                showAddProductModal: false,
                productForm: {
                    product_name: '',
                    description: '',
                    category: '',
                    price: '',
                    stock: 0
                },
                showEditModal: false,
                editForm: {
                    id: null,
                    product_name: '',
                    description: '',
                    category: '',
                    price: '',
                    qty: 0
                },
                productCategories: [
                    { value: 'Air Conditioner',  icon: '❄️',  label: 'Air Conditioner' },
                    { value: 'Refrigerator',     icon: '🧊',  label: 'Refrigerator' },
                    { value: 'Washing Machine',  icon: '🫧',  label: 'Washing Machine' },
                    { value: 'TV/Monitor',       icon: '📺',  label: 'TV / Monitor' },
                    { value: 'Microwave',        icon: '📡',  label: 'Microwave' },
                    { value: 'Water Dispenser',  icon: '💧',  label: 'Water Dispenser' },
                    { value: 'Fan',              icon: '🌀',  label: 'Fan' },
                    { value: 'Laptop',           icon: '💻',  label: 'Laptop' },
                ]
            }
        },
        mounted() {
            this.loadProfile();
            this.loadProducts();
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
            
            async loadProfile() {
                try {
                    const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.user.name = data.data.full_name;
                        this.user.image = data.data.profile_picture || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.data.full_name) + '&background=e5e7eb&color=374151';
                    }
                } catch(e) {}
            },
            
            async loadProducts() {
                try {
                    const res = await fetch('../../../Controller/product-controller.php?action=list_owner');
                    const data = await res.json();
                    if (data.status === 'success') this.products = data.data;
                } catch(e) {}
            },

            async selectProduct(product) {
                this.selectedProduct = product;
                try {
                    const res = await fetch(`../../../Controller/product-controller.php?action=list_serials&product_id=${product.id}`);
                    const data = await res.json();
                    if (data.status === 'success') this.serials = data.data;
                    else this.serials = [];
                } catch(e) {}
            },

            async addSerials() {
                if (!this.newSerials) return;
                
                const formData = new FormData();
                formData.append('product_id', this.selectedProduct.id);
                formData.append('serials', this.newSerials);

                Swal.fire({ title: 'Adding...', didOpen: () => Swal.showLoading() });

                try {
                    const res = await fetch('../../../Controller/product-controller.php?action=add_serials', { method: 'POST', body: formData });
                    const data = await res.json();
                    
                    if (data.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Added', timer: 1500, showConfirmButton: false });
                        this.showAddSerialModal = false;
                        this.newSerials = '';
                        this.selectProduct(this.selectedProduct); // Reload serials
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                } catch(e) {}
            },

            async addProduct() {
                const formData = new FormData();
                formData.append('product-name', this.productForm.product_name);
                formData.append('product-description', this.productForm.description);
                formData.append('category', this.productForm.category);
                formData.append('price', this.productForm.price);
                formData.append('stock', this.productForm.stock);

                if (this.$refs.image_1.files[0]) formData.append('image_1', this.$refs.image_1.files[0]);
                if (this.$refs.image_2.files[0]) formData.append('image_2', this.$refs.image_2.files[0]);
                if (this.$refs.image_3.files[0]) formData.append('image_3', this.$refs.image_3.files[0]);

                Swal.fire({ title: 'Adding Product...', didOpen: () => Swal.showLoading() });

                try {
                    const res = await fetch('../../../Controller/product-controller.php?action=add', { method: 'POST', body: formData });
                    const data = await res.json();
                    
                    if (data.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Product Added', timer: 1500, showConfirmButton: false });
                        this.showAddProductModal = false;
                        this.productForm = { product_name: '', description: '', category: '', price: '', stock: 0 };
                        if (this.$refs.image_1) this.$refs.image_1.value = '';
                        if (this.$refs.image_2) this.$refs.image_2.value = '';
                        if (this.$refs.image_3) this.$refs.image_3.value = '';
                        this.loadProducts();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                } catch(e) {
                     Swal.fire({ icon: 'error', title: 'Error', text: 'Connection failed' });
                }
            },
            openEditModal(product) {
                this.editForm = {
                    id: product.id,
                    product_name: product.product_name,
                    description: product.description,
                    category: product.category || '',
                    price: product.price,
                    qty: product.qty
                };
                this.showEditModal = true;
            },
            async updateProduct() {
                const formData = new FormData();
                formData.append('id', this.editForm.id);
                formData.append('product-name', this.editForm.product_name);
                formData.append('product-description', this.editForm.description);
                formData.append('category', this.editForm.category);
                formData.append('price', this.editForm.price);
                formData.append('stock', this.editForm.qty);

                if (this.$refs.edit_image_1 && this.$refs.edit_image_1.files[0]) formData.append('image_1', this.$refs.edit_image_1.files[0]);
                if (this.$refs.edit_image_2 && this.$refs.edit_image_2.files[0]) formData.append('image_2', this.$refs.edit_image_2.files[0]);
                if (this.$refs.edit_image_3 && this.$refs.edit_image_3.files[0]) formData.append('image_3', this.$refs.edit_image_3.files[0]);

                Swal.fire({ title: 'Updating...', didOpen: () => Swal.showLoading() });

                try {
                    const res = await fetch('../../../Controller/product-controller.php?action=edit', { method: 'POST', body: formData });
                    const data = await res.json();
                    
                    if (data.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Updated!', timer: 1500, showConfirmButton: false });
                        this.showEditModal = false;
                        this.loadProducts();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                } catch(e) {
                    Swal.fire({ icon: 'error', title: 'Connection Error', text: 'Could not update product.' });
                }
            },
            async confirmDelete(productId) {
                const result = await Swal.fire({
                    title: 'Remove Product?',
                    text: 'Are you sure you want to remove this product?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981', // Green button
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Yes, remove it'
                });

                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', productId);

                    try {
                        const res = await fetch('../../../Controller/product-controller.php?action=delete', { method: 'POST', body: formData });
                        const data = await res.json();
                        
                        if (data.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'Removed', timer: 1500, showConfirmButton: false });
                            this.loadProducts();
                            if (this.selectedProduct && this.selectedProduct.id === productId) {
                                this.selectedProduct = null;
                            }
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                        }
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: 'Connection Error', text: 'Could not remove product.' });
                    }
                }
            },

            getStatusClass(status) {
                 switch(status) {
                    case 'in_stock': return 'bg-green-100 text-green-800';
                    case 'sold': return 'bg-blue-100 text-blue-800';
                    case 'distributed': return 'bg-yellow-100 text-yellow-800';
                    default: return 'bg-gray-100 text-gray-800';
                }
            },
            getIcon(iconName) { return typeof getOwnerIcon !== "undefined" ? getOwnerIcon(iconName) : ""; }
        }
    }).mount('#app');
</script>
</body>
</html>

