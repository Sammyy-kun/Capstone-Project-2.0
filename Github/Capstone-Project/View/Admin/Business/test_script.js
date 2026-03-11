
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                showProfileMenu: false,
                user: {
                    name: 'Admin',
                    image: null
                },
                menuGroups: [
                    {
                        title: 'Main',
                        isOpen: true,
                        items: [
                            { name: 'Dashboard', icon: 'dashboard', link: '../Dashboard/dashboard.php', active: false }
                        ]
                    },
                    {
                        title: 'Admin & System Config',
                        isOpen: true,
                        items: [
                            { name: 'Business Apps', icon: 'business', link: '../Business/application.php', active: true },
                            { name: 'Accounts', icon: 'users', link: '../Accounts/accounts.php', active: false },
                            { name: 'Schedule', icon: 'schedule', link: '../Schedule/index.php', active: false }
                        ]
                    },
                    {
                        title: 'Reporting & Analytics',
                        isOpen: true,
                        items: [
                            { name: 'Technicians Tracking', icon: 'technicians', link: '../Technicians/index.php', active: false },
                            { name: 'Business Partners', icon: 'partners', link: '../Business/partners.php', active: false }
                        ]
                    }
                ],
                logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },
                // Page Specific Data
                shops: [],
                selectedShop: {},
                showModal: false,
                // Notifications
                showNotifications: false,
                notifications: [],
                unreadCount: 0,
                pollInterval: null
            }
        },
        mounted() {
            this.loadProfile();
            this.loadShops();
            this.fetchNotifications();
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
            toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
            toggleProfileMenu() { this.showProfileMenu = !this.showProfileMenu; },
            toggleMenu(index) { this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen; },
            getIcon(iconName) {
                const icons = {
                    dashboard: 'M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17',
                    business: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    technicians: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    users: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                    schedule: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    partners: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    settings: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                    logout: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'
                };
                return icons[iconName] || '';
            },
            
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
                    await fetch('../../../Controller/notification-controller.php?action=mark_all_read');
                    this.notifications.forEach(n => n.is_read = 1);
                    this.unreadCount = 0;
                } catch(e) { console.error(e); }
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
            handleImageError(event) {
                event.target.src = 'https://ui-avatars.com/api/?name=Admin';
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

            // Page Specific Checks
             async loadShops() {
                try {
                    const res = await fetch('../../../Controller/business-controller.php?action=list');
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.shops = data.data;
                    }
                } catch (e) { console.error(e); }
            },
            openModal(shop) {
                this.selectedShop = shop;
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
                this.selectedShop = {};
            },
            confirmAction(action) {
                Swal.fire({
                    title: `Are you sure?`,
                    text: `Do you want to ${action.toLowerCase()} this application?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: action === 'Approve' ? '#10B981' : '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: `Yes, ${action} it!`
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Second Confirmation
                        Swal.fire({
                            title: `Final Confirmation`,
                            text: `This action cannot be undone. Are you absolutely sure?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: action === 'Approve' ? '#10B981' : '#EF4444',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: `Yes, Proceed!`
                        }).then((finalResult) => {
                            if (finalResult.isConfirmed) {
                                this.processAction(action);
                            }
                        });
                    }
                });
            },
            async processAction(action) {
                const endpoint = action === 'Approve' ? 'approve_shop' : 'reject_shop';
                
                try {
                    const res = await fetch(`../../../Controller/user-controller.php?action=${endpoint}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ user_id: this.selectedShop.id })
                    });
                    const data = await res.json();
                    
                    if (data.status === 'success') {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        );
                        this.selectedShop.status = action === 'Approve' ? 'Approved' : 'Rejected';
                        this.loadShops(); // Refresh list
                        this.closeModal();
                    } else {
                         Swal.fire(
                            'Error!',
                            data.message || 'Something went wrong.',
                            'error'
                        );
                    }
                } catch (e) {
                    console.error(e);
                    Swal.fire('Error!', 'Server request failed.', 'error');
                }
            }
        }
    }).mount('#app');

