const app = Vue.createApp({
    data() {
        return {
            sidebarOpen: false,
            showProfileMenu: false,
            notifications: [],
            unreadCount: 0,
            user: window.INITIAL_USER || { name: 'Guest', image: '' },
            products: window.INITIAL_PRODUCTS || []
        }
    },
    methods: {
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        toggleProfileMenu() {
            this.showProfileMenu = !this.showProfileMenu;
        },
        toggleNotifications() {
            // handle notifications
        },
        handleImageError(event) {
            event.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(this.user.name) + '&background=e5e7eb&color=374151';
        }
    },
    mounted() {
        // Close profile menu when clicking outside
        document.addEventListener('click', (event) => {
            const btn = document.getElementById('profileMenuBtn');
            if (btn && !btn.contains(event.target) && !event.target.closest('.absolute.right-0')) {
                this.showProfileMenu = false;
            }
        });
    }
});

app.mount('#app');
