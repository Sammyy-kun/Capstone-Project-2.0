<?php 
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
redirectIfLoggedIn();
require_once __DIR__ . '/../../Layouts/header.php'; 
?>
<body>
    <div id="app">
        <!--Navbar-->
        <header>
            <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200" >
                <div class="flex items-center gap-4">
                    <a href="<?= getDashboardUrl() ?>" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition">FixMart</a>
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

        <main>
            <!-- Hero Section -->
            <section id="hero-section" class="pt-20 pb-20 bg-white border-b border-gray-100">
                <div class="relative w-full">
                    <div class="grid grid-cols-1 lg:grid-cols-2 items-start h-auto lg:h-[80vh] px-8 lg:px-40 py-16 gap-8 text-center lg:text-left">
                        <div class="flex flex-col mt-12">
                            <h1 class="text-7xl font-bold text-black leading-tight">{{ (hero || {}).heading }} <span class="text-emerald-500">{{ (hero || {}).headingHighlight }}</span></h1>
                            <p class="mt-7 text-xl lg:text-xl text-gray-600">{{ (hero || {}).description }}</p>
                            <a :href="(hero || {}).ctaLink" class="mx-auto lg:mx-0 bg-emerald-500 text-white w-48 py-4 px-5 rounded-lg font-semibold hover:bg-emerald-700 transition duration-300 ease-in-out text-center mt-10">
                                {{ (hero || {}).ctaText }}
                            </a>
                        </div>
                        <div class="flex justify-center">
                            <img src="../../../Public/pictures/Group 2 copy.png" alt="Hero Image" class="max-w-full h-auto">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Why Us Section -->
            <section v-if="whyUs" class="py-20 px-8 lg:px-40" id="why-us">
                <div class="text-center">
                    <p class="text-emerald-500 font-semibold text-lg mb-4">{{ (whyUs || {}).title }}</p>
                    <h2 class="font-bold text-5xl">{{ (whyUs || {}).heading }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">
                    <div v-for="card in ((whyUs || {}).cards || [])" :key="card.id" class="p-10 border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition">
                        <img :src="card.icon" alt="" class="w-12 h-12 mb-4">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ card.title }}</h3>
                        <p class="text-gray-600 leading-relaxed">{{ card.description }}</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-gray-900 text-white py-12 px-8 lg:px-40 mt-20">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 text-center md:text-left">
                <div>
                    <h3 class="text-2xl font-bold text-emerald-500 mb-4">FixMart</h3>
                    <p class="text-gray-400">Your trusted online shopping destination for appliances and repair services.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Services</h4>
                    <ul class="text-gray-400 space-y-2">
                        <li>Appliances</li>
                        <li>Repair</li>
                        <li>Installation</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Company</h4>
                    <ul class="text-gray-400 space-y-2">
                        <li>About Us</li>
                        <li>Contact</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Legal</h4>
                    <ul class="text-gray-400 space-y-2">
                        <li>Terms</li>
                        <li>Privacy</li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts at the very end -->
    <script src="../../../Public/js/User/landing-page.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({ duration: 800, once: true });
            }
        });
    </script>
</body>
</html>

