const app = Vue.createApp({
    data() {
        return {
            sidebarOpen: window.innerWidth >= 1024,
            user: {
                name: 'Owner',
                image: null
            },
            menuGroups: [
                {
                    title: 'Main',
                    isOpen: true,
                    items: [
                        { name: 'Dashboard', icon: 'dashboard', link: '../Dashboard/dashboard.php', active: true },
                        { name: 'Application Review', icon: 'business', link: '../Business/apply.php', active: false }
                    ]
                },
                {
                    title: 'Inventory',
                    isOpen: true,
                    items: [
                        { name: 'Spare Parts', icon: 'products', link: '../Inventory/dashboard.php', active: false },
                        { name: 'Distribution', icon: 'products', link: '../Products/distribution.php', active: false }
                    ]
                },
                {
                    title: 'Repair Services',
                    isOpen: true,
                    items: [
                        { name: 'Repair Jobs', icon: 'repair', link: '../Repair/history.php', active: false },
                        { name: 'Technicians', icon: 'technicians', link: '../Technicians/index.php', active: false }
                    ]
                },
                {
                    title: 'System',
                    isOpen: true,
                    items: [
                        { name: 'Settings', icon: 'settings', link: '../Profile/edit.php', active: false }
                    ]
                }
            ],
            logoutLink: { name: 'Logout', icon: 'logout', link: '../../Auth/logout.php' },
            notifications: 0,
            showProfileMenu: false,
            metrics: {
                products: 0,
                repairs: 0,
                revenue: '₱0.00'
            },
            salesChart: null,
            appStatus: null,
            appLoading: true,
            rejectionReason: null
        }
    },
    mounted() {
        this.loadProfile();
        this.loadNotifications();
        this.loadMetrics();
        this.loadSalesAnalytics();
        this.loadAppStatus();
        window.addEventListener('resize', this.handleResize);
        // Determine active menu item based on current URL path
        const currentPath = window.location.pathname.toLowerCase();

        let foundActive = false;
        for (const group of this.menuGroups) {
            for (const item of group.items) {
                // Get the page name from the link (e.g., dashboard.php)
                const linkParts = item.link.toLowerCase().split('/');
                let linkPath = linkParts[linkParts.length - 2] + '/' + linkParts[linkParts.length - 1]; // e.g. Inventory/dashboard.php

                // For Dashboard vs Inventory dashboard
                if (currentPath.includes(linkPath)) {
                    item.active = true;
                    foundActive = true;
                } else {
                    item.active = false;
                }
            }
        }

        // Fallback for Dashboard if nothing matched but we are in Dashboard dir
        if (!foundActive && currentPath.includes('/dashboard/')) {
            this.menuGroups[0].items[0].active = true;
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#profileMenuBtn')) {
                this.showProfileMenu = false;
            }
        });
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize);
    },
    computed: {
        parsedRejectionReasons() {
            if (!this.rejectionReason) return [];
            const parts = this.rejectionReason.split(' | ');
            const reasonPart = parts[0] || '';
            return reasonPart.split('; ').map(r => r.trim()).filter(Boolean);
        },
        rejectionNote() {
            if (!this.rejectionReason) return '';
            const match = this.rejectionReason.match(/\| Note: (.+)$/);
            return match ? match[1].trim() : '';
        }
    },
    methods: {
        async loadAppStatus() {
            this.appLoading = true;
            try {
                const res = await fetch('../../../Controller/business-controller.php?action=my_status');
                const data = await res.json();
                if (data.status === 'success' && data.data) {
                    this.appStatus = data.data.status;
                    this.rejectionReason = data.data.rejection_reason || null;
                } else {
                    this.appStatus = null;
                }
            } catch (e) { console.error('Failed to load app status', e); }
            finally { this.appLoading = false; }
        },
        handleResize() {
            if (window.innerWidth < 1024) {
                this.sidebarOpen = false;
            } else {
                this.sidebarOpen = true;
            }
        },
        async loadNotifications() {
            try {
                const res = await fetch('../../../Controller/notification-controller.php?action=fetch');
                const data = await res.json();
                if (data.status === 'success') {
                    this.notifications = data.unread_count || 0;
                }
            } catch (e) { console.error('Failed to load notifications', e); }
        },
        async loadProfile() {
            try {
                const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                const data = await res.json();
                if (data.status === 'success') {
                    this.user.name = data.data.full_name;
                    // If the database has a path like "/GitHub/Capstone.../profiles/image.png" we can just use it directly.
                    // If it is entirely null, we display the UI-avatars placeholder instead.
                    this.user.image = data.data.profile_picture ? data.data.profile_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.data.full_name);
                }
            } catch (e) { console.error(e); }
        },
        async loadMetrics() {
            try {
                const res = await fetch('../../../Controller/user-controller.php?action=get_dashboard_metrics');
                const data = await res.json();
                if (data.status === 'success') {
                    this.metrics = data.data;
                }
            } catch (e) { console.error("Failed to load metrics", e); }
        },
        async loadSalesAnalytics() {
            try {
                const res = await fetch('../../../Controller/user-controller.php?action=get_sales_analytics');
                const result = await res.json();
                if (result.status === 'success') {
                    this.initChart(result.data);
                }
            } catch (e) { console.error("Failed to load analytics", e); }
        },
        initChart(data) {
            const ctx = document.getElementById('salesChart').getContext('2d');

            // Emerald themed gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            if (this.salesChart) {
                this.salesChart.destroy();
            }

            this.salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.month),
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: data.map(item => item.total),
                        borderColor: '#10B981',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return ' ₱' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F3F4F6' },
                            ticks: {
                                font: { size: 12 },
                                color: '#9CA3AF',
                                callback: function (value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 12 },
                                color: '#9CA3AF'
                            }
                        }
                    }
                }
            });
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        toggleProfileMenu(event) {
            event.stopPropagation();
            this.showProfileMenu = !this.showProfileMenu;
        },
        toggleMenu(index) {
            this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen;
        },
        getIcon(iconName) {
            const icons = {
                dashboard: 'M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 17 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17',
                business: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                products: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                repair: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
                technicians: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                settings: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                logout: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'
            };
            return icons[iconName] || (typeof getOwnerIcon !== 'undefined' ? getOwnerIcon(iconName) : '');
        },
        handleImageError(event) {
            event.target.src = 'https://via.placeholder.com/300x300/f3f4f6/6b7280?text=No+Image';
        }
    }
})
app.mount('#app')