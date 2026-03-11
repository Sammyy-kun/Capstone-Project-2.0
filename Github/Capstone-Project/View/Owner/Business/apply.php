<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('owner');
?>
<?php require '../../Layouts/auth_check.php'; ?>
<?php require '../../Layouts/header.php'; ?>
<body>
<script src="../../../Public/js/owner/sidebar.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/owner/sidebar.js') ?>"></script>
<div id="app" v-cloak class="flex min-h-screen bg-white font-sans">
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

    <main class="flex-1 min-w-0 transition-all duration-300 ease-in-out mt-14 px-6 py-10" :class="{'lg:ml-64': sidebarOpen}">
        <div>
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Application Review</h1>
                <p class="text-gray-500 mt-1">Track the status of your business application.</p>
            </div>

            <!-- Status section (hidden while form is open) -->
            <div v-if="!showReapplyForm">

            <!-- Loading State -->
            <div v-if="appLoading" class="flex items-center justify-center py-24">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-500"></div>
            </div>

            <!-- Pending -->
            <div v-else-if="appStatus === 'Pending'" class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-amber-50 border-b border-amber-100 px-6 py-5 flex items-center gap-4">
                    <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Application Pending</h2>
                        <p class="text-sm text-amber-600 mt-0.5">Awaiting admin review</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <p class="text-gray-600 text-sm leading-relaxed">Your business application is currently under review. Our team will evaluate your submission and notify you once a decision has been made. This usually takes 1�3 business days.</p>
                    <div class="mt-4 flex items-center gap-2 text-xs text-amber-600 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        You will receive a notification when your application status changes.
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div v-else-if="appStatus === 'Approved'" class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-emerald-50 border-b border-emerald-100 px-6 py-5 flex items-center gap-4">
                    <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Application Approved</h2>
                        <p class="text-sm text-emerald-600 mt-0.5">Your business is verified</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <p class="text-gray-600 text-sm leading-relaxed">Congratulations! Your business application has been approved. You now have full access to the merchant panel and can start managing your products and services.</p>
                    <a href="../Dashboard/dashboard.php" class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl text-sm transition shadow-sm shadow-emerald-500/25 hover:-translate-y-0.5">
                        Go to Dashboard
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>

            <!-- Rejected -->
            <div v-else-if="appStatus === 'Rejected'" class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-red-50 border-b border-red-100 px-6 py-5 flex items-center gap-4">
                    <div class="w-11 h-11 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Application Rejected</h2>
                        <p class="text-sm text-red-500 mt-0.5">Your application was not approved</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <div v-if="parsedRejectionReasons.length > 0 || rejectionNote">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Reason(s) for rejection:</p>
                        <ul v-if="parsedRejectionReasons.length > 0" class="space-y-2 mb-4">
                            <li v-for="reason in parsedRejectionReasons" :key="reason" class="flex items-start gap-3 bg-red-50 border border-red-100 rounded-xl px-4 py-3">
                                <svg class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                <span class="text-sm text-gray-700">{{ reason }}</span>
                            </li>
                        </ul>
                        <div v-if="rejectionNote" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 mb-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Additional Note</p>
                            <p class="text-sm text-gray-700">{{ rejectionNote }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500">Please address the issues above and resubmit your application.</p>
                    <button @click="showReapplyForm = true; currentStep = 1" class="mt-4 inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reapply Now
                    </button>
                </div>
            </div>

            <!-- No Application -->
            <div v-else class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-blue-50 border-b border-blue-100 px-6 py-5 flex items-center gap-4">
                    <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">No Application Found</h2>
                        <p class="text-sm text-blue-500 mt-0.5">No submission on record</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <p class="text-gray-600 text-sm">No business application record was found for your account.</p>
                </div>
            </div>

            </div><!-- end status section -->

            <!-- ========== Reapply Form ========== -->
            <div v-if="showReapplyForm">

                <!-- Step Progress -->
                <div class="bg-white rounded-[2rem] shadow-xl shadow-emerald-500/5 border border-gray-100 mb-10 overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-5">
                        <div v-for="(step, i) in reapplySteps" :key="i"
                             :class="['relative p-7 border-b md:border-b-0 md:border-r border-gray-50 last:border-0 transition-all duration-300', currentStep === i+1 ? 'bg-emerald-50/30' : '']"
                        >
                            <div class="flex items-center gap-5">
                                <div class="flex-shrink-0">
                                    <div v-if="currentStep > i+1" class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div v-else :class="['w-12 h-12 rounded-2xl border-2 flex items-center justify-center font-bold text-base transition-all duration-300 shadow-sm', currentStep === i+1 ? 'border-emerald-500 bg-emerald-500 text-white shadow-emerald-500/30' : 'border-gray-100 bg-gray-50 text-gray-400 font-medium']">
                                        {{ i + 1 }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 :class="['font-bold text-sm mb-1 transition-colors duration-300', currentStep >= i+1 ? 'text-gray-900' : 'text-gray-400']">{{ step }}</h3>
                                    <div class="flex items-center gap-1.5">
                                        <div :class="['w-1.5 h-1.5 rounded-full transition-colors duration-300', currentStep === i+1 ? 'bg-emerald-500 animate-pulse' : (currentStep > i+1 ? 'bg-emerald-400' : 'bg-gray-200')]"></div>
                                        <p :class="['text-[10px] uppercase tracking-widest font-bold transition-colors duration-300', currentStep >= i+1 ? 'text-emerald-600' : 'text-gray-400']">{{ currentStep === i+1 ? 'Active' : (currentStep > i+1 ? 'Done' : 'Wait') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-if="currentStep===i+1" class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500"></div>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-emerald-500/5 border border-gray-100 p-8 lg:p-14 mb-20 transition-all duration-500">

                    <!-- Step 1: Owner Info -->
                    <div v-if="currentStep === 1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-1">Owner Information</h2>
                        <p class="text-gray-500 text-sm mb-8">Personal details to verify your identity</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">First Name</label>
                                <input v-model="rform.first_name" type="text" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="First name">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Last Name</label>
                                <input v-model="rform.last_name" type="text" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="Last name">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Email Address</label>
                                <input v-model="rform.email" type="email" readonly class="w-full px-5 py-4 rounded-xl border border-gray-200 bg-gray-50/50 text-gray-500 outline-none cursor-not-allowed" placeholder="example@email.com">
                                <p class="text-xs text-gray-400 mt-1">This is your account email and cannot be changed here.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Mobile Number</label>
                                <input v-model="rform.phone" type="text" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="09XX XXX XXXX">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">ID Type</label>
                                <select v-model="rform.id_type" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white cursor-pointer appearance-none">
                                    <option value="" disabled>Select ID Type</option>
                                    <option value="National ID">National ID</option>
                                    <option value="Drivers License">Driver's License</option>
                                    <option value="Passport">Passport</option>
                                </select>
                                <div class="mt-3">
                                    <label class="text-sm font-bold text-gray-700 ml-1">ID Number</label>
                                    <input v-model="rform.gov_id" type="text" class="w-full mt-1 px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="Enter ID number">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Government ID Upload</label>
                                <input type="file" ref="govIdInput" class="hidden" @change="onFileChange($event, 'gov_id_file')" accept="image/*,.pdf">
                                <div @click="$refs.govIdInput.click()" class="border-2 border-dashed border-gray-200 rounded-xl p-6 flex flex-col items-center justify-center hover:bg-emerald-50 hover:border-emerald-500 transition cursor-pointer">
                                    <div v-if="rfiles.gov_id_file" class="flex flex-col items-center">
                                        <svg class="w-8 h-8 text-emerald-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-xs font-semibold text-emerald-600 truncate max-w-[180px]">{{ rfiles.gov_id_file.name }}</p>
                                    </div>
                                    <div v-else class="flex flex-col items-center">
                                        <svg class="w-8 h-8 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M16 8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <p class="text-xs text-gray-500">Click to upload</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Business Info -->
                    <div v-if="currentStep === 2">
                        <h2 class="text-2xl font-bold text-gray-800 mb-1">Business Information</h2>
                        <p class="text-gray-500 text-sm mb-8">Shop details and contact</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Name</label>
                                <input v-model="rform.business_name" type="text" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="Your business name">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Type</label>
                                <select v-model="rform.business_type" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white cursor-pointer appearance-none">
                                    <option value="" disabled>Select type</option>
                                    <option value="Appliance Store">Appliance Store</option>
                                    <option value="Appliance Repair & Sales">Appliance Repair &amp; Sales</option>
                                    <option value="Independent Technician">Independent Technician</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Email</label>
                                <input v-model="rform.business_email" type="email"
                                    :class="['w-full px-5 py-4 rounded-xl border focus:ring-4 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white', isValidRformBusinessEmail ? 'border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/10' : 'border-red-500 focus:border-red-500 focus:ring-red-500/10']"
                                    placeholder="business@email.com">
                                <p v-if="!isValidRformBusinessEmail" class="text-xs text-red-500 font-bold ml-1 mt-1">Please enter a valid business email with '@' and domain.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Phone</label>
                                <input v-model="rform.business_phone" type="text" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="09XX XXX XXXX">
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Address</label>
                                <input v-model="rform.business_address" type="text" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="Full business address">
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">TIN Number</label>
                                <input v-model="rform.tin_number" type="text" @input="formatTIN" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="000-000-000-000">
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Offerings -->
                    <div v-if="currentStep === 3">
                        <h2 class="text-2xl font-bold text-gray-800 mb-1">Offerings &amp; Pricing</h2>
                        <p class="text-gray-500 text-sm mb-8">Define what services or products you provide</p>
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Service / Product Description</label>
                                <textarea v-model="rform.offer_details" rows="5" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white resize-none" placeholder="Describe your expertise, services, and specializations..."></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Service Coverage Area</label>
                                    <input v-model="rform.service_area" type="text" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="e.g. Metro Manila, Cavite">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Est. Average Pricing (PHP)</label>
                                    <input v-model="rform.avg_pricing" type="text" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" placeholder="500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Documents -->
                    <div v-if="currentStep === 4">
                        <h2 class="text-2xl font-bold text-gray-800 mb-1">Business Legitimacy</h2>
                        <p class="text-gray-500 text-sm mb-8">Upload updated/corrected documents</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="text-sm font-bold text-gray-700 mb-2 block">Business Permit</label>
                                <input type="file" ref="permitInput" class="hidden" @change="onFileChange($event, 'business_permit')" accept="image/*,.pdf">
                                <div @click="$refs.permitInput.click()" class="border-2 border-dashed border-gray-200 rounded-xl p-10 flex flex-col items-center justify-center hover:bg-emerald-50 hover:border-emerald-500 transition cursor-pointer group">
                                    <div v-if="rfiles.business_permit" class="flex flex-col items-center">
                                        <svg class="w-10 h-10 text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-xs font-semibold text-emerald-600 truncate max-w-[180px]">{{ rfiles.business_permit.name }}</p>
                                    </div>
                                    <div v-else class="flex flex-col items-center">
                                        <svg class="w-10 h-10 text-gray-300 group-hover:text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <p class="text-xs text-gray-500">Click to upload (PDF, PNG, JPG)</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-bold text-gray-700 mb-2 block">DTI / SEC Registration</label>
                                <input type="file" ref="dtiInput" class="hidden" @change="onFileChange($event, 'dti_registration')" accept="image/*,.pdf">
                                <div @click="$refs.dtiInput.click()" class="border-2 border-dashed border-gray-200 rounded-xl p-10 flex flex-col items-center justify-center hover:bg-emerald-50 hover:border-emerald-500 transition cursor-pointer group">
                                    <div v-if="rfiles.dti_registration" class="flex flex-col items-center">
                                        <svg class="w-10 h-10 text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-xs font-semibold text-emerald-600 truncate max-w-[180px]">{{ rfiles.dti_registration.name }}</p>
                                    </div>
                                    <div v-else class="flex flex-col items-center">
                                        <svg class="w-10 h-10 text-gray-300 group-hover:text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <p class="text-xs text-gray-500">Click to upload (PDF, PNG, JPG)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Agreement -->
                    <div v-if="currentStep === 5">
                        <h2 class="text-2xl font-bold text-gray-800 mb-1">Platform Agreement</h2>
                        <p class="text-gray-500 text-sm mb-8">Final review and confirmation</p>
                        <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50 mb-6">
                            <div class="p-6 h-64 overflow-y-auto text-sm text-gray-700 leading-relaxed space-y-4">
                                <h3 class="font-bold text-base text-gray-900 uppercase tracking-wider border-b border-gray-200 pb-2">Terms &amp; Conditions</h3>
                                <p><strong>1. Service Standards:</strong> You agree to provide high-quality services and genuine products. Fraudulent listings are grounds for immediate termination.</p>
                                <p><strong>2. Commission &amp; Fees:</strong> FixMart charges a flat platform fee on successful bookings. Details available in the full Merchant Policy.</p>
                                <p><strong>3. Warranty:</strong> All shop repairs must honor a minimum 90-day platform-backed warranty.</p>
                                <p><strong>4. Dispute Resolution:</strong> FixMart serves as the final mediator for any customer complaints involving technical fulfillment.</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-emerald-50 transition border border-transparent hover:border-emerald-100">
                                <input type="checkbox" v-model="rform.agreed" class="w-5 h-5 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                <span class="text-sm font-semibold text-gray-700">I have read and agree to the Terms and Conditions</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-emerald-50 transition border border-transparent hover:border-emerald-100">
                                <input type="checkbox" v-model="rform.certified" class="w-5 h-5 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                <span class="text-sm font-semibold text-gray-700">I certify that all submitted information is true and accurate</span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex flex-wrap items-center justify-between gap-4 mt-12 pt-8 border-t border-gray-100">
                        <button @click="cancelReapply" class="px-10 py-4 rounded-lg font-bold text-gray-400 hover:text-gray-900 hover:bg-gray-100 transition">Cancel</button>
                        <div class="flex items-center gap-4 ml-auto">
                            <button v-if="currentStep > 1" @click="prevStep" class="px-10 py-4 rounded-lg border border-gray-200 font-bold text-gray-700 hover:shadow-md transition bg-white active:scale-95">Back</button>
                            <button v-if="currentStep < 5" @click="nextStep" class="px-14 py-4 rounded-lg bg-emerald-500 font-extrabold text-white hover:bg-emerald-600 shadow-xl shadow-emerald-100 transition transform hover:scale-105 active:scale-95 flex items-center gap-3">
                                <span>Continue</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7M5 12h16"></path></svg>
                            </button>
                            <button v-else @click="submitReapply" :disabled="!rform.agreed || !rform.certified || isSubmitting"
                                class="px-14 py-4 rounded-lg bg-emerald-500 font-extrabold text-white hover:bg-emerald-600 shadow-xl shadow-emerald-100 transition transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="isSubmitting">Submitting...</span>
                                <span v-else>Submit Reapplication</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div><!-- end reapply form -->

        </div>
    </main>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    sidebarOpen: window.innerWidth >= 1024,
                    showProfileMenu: false,
                    user: { name: '', image: null },
                    menuGroups: typeof calculateActiveOwnerMenu !== 'undefined'
                        ? calculateActiveOwnerMenu(JSON.parse(JSON.stringify(ownerSidebarMenu)))
                        : [],
                    logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },
                    notifications: [],
                    unreadCount: 0,
                    showNotifications: false,
                    appStatus: null,
                    appLoading: true,
                    rejectionReason: null,
                    // Reapply form state
                    showReapplyForm: false,
                    currentStep: 1,
                    isSubmitting: false,
                    reapplySteps: ['Owner Info', 'Business', 'Offerings', 'Documents', 'Agreement'],
                    rform: {
                        first_name: '', last_name: '', email: '', phone: '',
                        gov_id: '', id_type: '',
                        business_name: '', business_type: '', business_email: '',
                        business_phone: '', business_address: '', tin_number: '',
                        offer_details: '', service_area: '', avg_pricing: '',
                        agreed: false, certified: false
                    },
                    rfiles: { gov_id_file: null, business_permit: null, dti_registration: null }
                };
            },
            computed: {
                parsedRejectionReasons() {
                    if (!this.rejectionReason) return [];
                    const parts = this.rejectionReason.split(' | ');
                    return (parts[0] || '').split('; ').map(r => r.trim()).filter(Boolean);
                },
                rejectionNote() {
                    if (!this.rejectionReason) return '';
                    const match = this.rejectionReason.match(/\| Note: (.+)$/);
                    return match ? match[1].trim() : '';
                },
                isValidRformBusinessEmail() {
                    if (!this.rform.business_email) return true;
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.rform.business_email);
                }
            },
            async mounted() {
                this.loadProfile();
                this.fetchNotifications();
                await this.loadAppStatus();
            },
            watch: {
                'rform.phone'(v) { this.rform.phone = String(v).replace(/\D/g, ''); },
                'rform.gov_id'(v) { this.rform.gov_id = String(v).replace(/\D/g, ''); },
                'rform.business_phone'(v) { this.rform.business_phone = String(v).replace(/\D/g, ''); },
                'rform.avg_pricing'(v) { this.rform.avg_pricing = String(v).replace(/\D/g, ''); }
            },
            methods: {
                toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
                toggleMenu(index) { this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen; },
                getIcon(iconName) { return typeof getOwnerIcon !== 'undefined' ? getOwnerIcon(iconName) : ''; },
                toggleProfileMenu() { this.showProfileMenu = !this.showProfileMenu; },
                toggleNotifications() { this.showNotifications = !this.showNotifications; },
                handleImageError(e) {
                    e.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(this.user.name) + '&background=e5e7eb&color=374151';
                },
                async loadProfile() {
                    try {
                        const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                        const data = await res.json();
                        if (data.status === 'success') {
                            this.user.name = data.data.full_name;
                            this.user.image = data.data.profile_picture || null;
                            // Pre-fill owner email from account
                            if (data.data.email) this.rform.email = data.data.email;
                        }
                    } catch(e) { console.error(e); }
                },
                async loadAppStatus() {
                    try {
                        const res = await fetch('../../../Controller/business-controller.php?action=my_status');
                        const data = await res.json();
                        if (data.status === 'success' && data.data) {
                            this.appStatus = data.data.status;
                            this.rejectionReason = data.data.rejection_reason || null;
                        }
                    } catch(e) { console.error(e); } finally {
                        this.appLoading = false;
                    }
                },
                async fetchNotifications() {
                    try {
                        const res = await fetch('../../../Controller/notification-controller.php?action=fetch');
                        const data = await res.json();
                        if (data.status === 'success') {
                            this.notifications = data.data;
                            this.unreadCount = data.unread_count;
                        }
                    } catch(e) {}
                },
                // ---- Reapply form methods ----
                validateStep(step) {
                    const f = this.rform;
                    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    const phoneRe = /^[0-9]{11}$/;
                    if (step === 1) {
                        if (!f.first_name || !f.last_name || !f.email || !f.phone || !f.id_type || !f.gov_id || !this.rfiles.gov_id_file) {
                            Swal.fire('Required Fields', 'Please complete all owner information and upload your government ID.', 'warning'); return false;
                        }
                        if (!emailRe.test(f.email)) { Swal.fire('Invalid Email', 'Please enter a valid email address.', 'warning'); return false; }
                        if (!phoneRe.test(f.phone)) { Swal.fire('Invalid Phone', 'Mobile number must be exactly 11 digits.', 'warning'); return false; }
                        if (!/^[0-9]+$/.test(f.gov_id)) { Swal.fire('Invalid ID Number', 'ID Number must contain numbers only.', 'warning'); return false; }
                    }
                    if (step === 2) {
                        if (!f.business_name || !f.business_type || !f.business_email || !f.business_phone || !f.business_address || !f.tin_number) {
                            Swal.fire('Required Fields', 'Please complete all business details.', 'warning'); return false;
                        }
                        if (!emailRe.test(f.business_email)) { Swal.fire('Invalid Email', 'Please enter a valid business email.', 'warning'); return false; }
                        if (!phoneRe.test(f.business_phone)) { Swal.fire('Invalid Phone', 'Business phone must be exactly 11 digits.', 'warning'); return false; }
                    }
                    if (step === 3) {
                        if (!f.offer_details || !f.service_area || !f.avg_pricing) {
                            Swal.fire('Required Fields', 'Please describe your services and pricing.', 'warning'); return false;
                        }
                    }
                    if (step === 4) {
                        if (!this.rfiles.business_permit || !this.rfiles.dti_registration) {
                            Swal.fire('Required Documents', 'Please upload both your Business Permit and DTI/SEC Registration.', 'warning'); return false;
                        }
                    }
                    return true;
                },
                nextStep() {
                    if (this.validateStep(this.currentStep)) {
                        this.currentStep++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                prevStep() {
                    if (this.currentStep > 1) { this.currentStep--; window.scrollTo({ top: 0, behavior: 'smooth' }); }
                },
                cancelReapply() {
                    Swal.fire({ title: 'Cancel?', text: 'Your progress will be lost.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#10B981', cancelButtonColor: '#6B7280', confirmButtonText: 'Yes, cancel' })
                        .then(r => { if (r.isConfirmed) { this.showReapplyForm = false; this.currentStep = 1; } });
                },
                onFileChange(event, field) {
                    const file = event.target.files[0];
                    if (file) this.rfiles[field] = file;
                },
                formatTIN() {
                    let val = this.rform.tin_number.replace(/\D/g, '').substring(0, 12);
                    this.rform.tin_number = val.length > 0 ? val.match(/.{1,3}/g).join('-') : '';
                },
                async submitReapply() {
                    if (!this.rform.agreed || !this.rform.certified) {
                        Swal.fire('Incomplete', 'Please agree to the terms and certify your information.', 'warning'); return;
                    }
                    this.isSubmitting = true;
                    Swal.fire({ title: 'Submitting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    const fd = new FormData();
                    for (const key in this.rform) {
                        if (key !== 'agreed' && key !== 'certified') fd.append(key, this.rform[key]);
                    }
                    for (const key in this.rfiles) {
                        if (this.rfiles[key]) fd.append(key, this.rfiles[key]);
                    }

                    try {
                        const res = await fetch('../../../Controller/business-controller.php?action=reapply', { method: 'POST', body: fd });
                        const data = await res.json();
                        if (data.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'Reapplication Submitted!', text: 'Your application is now under review.', confirmButtonColor: '#10B981' })
                                .then(() => { this.showReapplyForm = false; this.appStatus = 'Pending'; this.rejectionReason = null; });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'A network error occurred. Please try again.', 'error');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }).mount('#app');
    </script>
</div>
</body>
</html>

