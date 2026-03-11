<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
requireRole('user');
?>
<?php require '../../Layouts/header.php'; ?>
<body class="bg-gray-50">
    <!-- Navbar -->
    <!-- Navbar -->
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
    </header>

    <section class="pt-24 pb-10 px-4 lg:px-40 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4 sm:gap-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">My Appliances</h1>
                <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                        class="w-full sm:w-auto bg-emerald-500 text-white px-4 py-2 rounded-lg hover:bg-emerald-600 transition text-sm sm:text-base">
                    + Register Appliance
                </button>
            </div>

            <!-- Appliances List -->
            <div id="appliancesList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Loaded via JS -->
                <p class="text-gray-500 col-span-2 text-center py-10">Loading...</p>
            </div>
        </div>
    </section>

    <!-- Add Appliance Modal -->
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-white p-8 rounded-xl w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Register New Appliance</h2>
            <form id="addApplianceForm">
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Brand</label>
                    <input type="text" name="brand" required class="w-full border rounded p-2 focus:outline-emerald-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Model</label>
                    <input type="text" name="model" required class="w-full border rounded p-2 focus:outline-emerald-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Serial Number</label>
                    <input type="text" name="serial_number" class="w-full border rounded p-2 focus:outline-emerald-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-1">Purchase Date</label>
                    <input type="date" name="purchase_date" required class="w-full border rounded p-2 focus:outline-emerald-500">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" 
                            class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-500 text-white rounded hover:bg-emerald-600">Register</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', loadAppliances);

        function loadAppliances() {
            fetch('../../../Controller/user-controller.php?action=my_appliances')
                .then(res => res.json())
                .then(res => {
                    const container = document.getElementById('appliancesList');
                    if (res.status === 'success' && res.data.length > 0) {
                        container.innerHTML = res.data.map(item => `
                            <div class="bg-white p-6 rounded-lg shadow border border-gray-100">
                                <h3 class="font-bold text-xl text-gray-800">${item.brand} ${item.model}</h3>
                                <p class="text-sm text-gray-500">Serial: ${item.serial_number || 'N/A'}</p>
                                <p class="text-sm text-gray-500">Purchased: ${item.purchase_date}</p>
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <span class="text-xs font-semibold px-2 py-1 rounded ${new Date(item.warranty_expiry) > new Date() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${new Date(item.warranty_expiry) > new Date() ? 'Warranty Active' : 'Warranty Expired'}
                                    </span>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p class="col-span-2 text-center text-gray-500 py-10">No appliances registered yet.</p>';
                    }
                })
                .catch(err => console.error(err));
        }

        document.getElementById('addApplianceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Registering Appliance...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('../../../Controller/user-controller.php?action=add_appliance', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    document.getElementById('addModal').classList.add('hidden');
                    this.reset();
                    loadAppliances();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message
                    });
                }
            })
            .catch(err => Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error registering appliance'
            }));
        });
    </script>
</body>
</html>

