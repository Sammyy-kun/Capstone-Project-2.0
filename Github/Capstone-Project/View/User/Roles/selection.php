<?php 
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../Layouts/header.php'; 
?>
<body>
    <div id="app">
        <!--Navbar to-->
        <header>
            <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200" >
                <div class="flex items-center gap-4">
                    <a href="../Home/index.php" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition">FixMart</a>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Guest Links -->
                    <?php if (!isLoggedIn()): ?>
                    <div class="flex items-center gap-4">
                        <a href="../../Auth/User/login.php" class="text-sm font-medium text-gray-700 hover:text-emerald-500 transition">Log in</a>
                        <a href="../Roles/selection.php" class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-600 transition">Get Started</a>
                    </div>
                    <?php else: ?>
                    <!-- Authenticated Links -->
                    <span class="text-sm font-medium text-gray-700">Hello, {{ (user || {}).name || 'User' }}</span>
                    
                    <button @click="toggleNotifications" class="relative p-2 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span v-if="(notifications || []).length > 0 || unreadCount > 0" class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">{{ (notifications || []).length || unreadCount }}</span>
                    </button>
                    
                    <div class="relative">
                        <button id="profileMenuBtn" @click="toggleProfileMenu" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition relative">
                            <img :src="(user || {}).image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent((user || {}) + '&background=e5e7eb&color=374151'.name || 'User')" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-200 bg-white">
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
                    <?php endif; ?>
                </div>
            </nav>
        </header>
        <main class="min-h-screen flex flex-col items-center justify-center px-8 lg:px-40 pt-20">
            <div class="text-center mb-10">
                <h1 class="font-bold text-4xl lg:text-5xl mb-6 leading-tight">How are you planning to use <span class="text-emerald-500">FixMart</span></h1>
                <p class="text-base lg:text-lg text-gray-700 my-2 text-center max-w-3xl mx-auto leading-relaxed">
                    Help us understand how you plan to use FixMart so we<br>
                    can tailor the experience for you.
                </p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 w-full max-w-5xl mx-auto">
                <div class="text-center lg:cols-span-1 bg-white rounded-xl border border-gray-200 p-7 hover:border-emerald-500 hover:border-2 transition-all duration-300 ease-in-out hover:shadow-lg transform hover:scale-105">
                    <img class="w-20 h-20 justify-self-center m-5" src="../../../Public/pictures/shops.png" alt="">
                    <h1 class="font-semibold mb-4 text-3xl">Sell on FixMart</h1>
                    <p class="text-gray-700 text-md">
                        Apply to sell your appliances, manage orders, 
                        and grow your business on FixMart.
                    </p>
                    <a href="<?= BASE_URL ?>View/User/Business/apply.php" class="inline-block text-white bg-emerald-500 hover:bg-emerald-600 font-semibold text-md mt-5 rounded-lg p-4 transition duration-300 ease-in-out">Apply as a Seller</a>
                </div>
                <div class="text-center lg:cols-span-1 bg-white rounded-xl border border-gray-200 p-7 hover:border-emerald-500 hover:border-2 transition-all duration-300 ease-in-out hover:shadow-lg transform hover:scale-105">
                    <div class="flex justify-center items-center gap-4 m-5">
                        <img class="w-20 h-20" src="<?= BASE_URL ?>Public/pictures/repair.png" alt="Repair">
                        <img class="w-16 h-16" src="<?= BASE_URL ?>Public/pictures/shopping-cart.png" alt="Shopping Cart">
                    </div>
                    <h1 class="font-semibold mb-4 text-3xl">Shop & Repair</h1>
                    <p class="text-gray-700 text-md">
                       Buy top-quality appliances and book professional repair services all in one place.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-5">
                        <a href="<?= BASE_URL ?>View/Auth/User/register.php" class="inline-block text-white bg-emerald-500 hover:bg-emerald-600 font-semibold text-md rounded-lg p-4 transition duration-300 ease-in-out">Get Started</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../../../Public/js/User/landing-page.js"></script>
</body>
</html>
