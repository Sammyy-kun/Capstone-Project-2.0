const app = Vue.createApp({
    data() {
        return {
            sidebarOpen: true,
            user: {
                name: '',
                image: ''
            },
            searchQuery: '',
            sortBy: 'name-asc',
            products: [
                { id: 1, name: 'Whirlpool Refrigerator', description: 'Spacious side-by-side refrigerator with water dispenser', price: 59999, stock: 15, image: '../../../Public/pictures/LG-Refrigerator-PNG-Transparent-Image.png', category: 'Refrigerator', date: '2024-01-15' },
                { id: 2, name: 'Samsung Refrigerator', description: 'Premium double door refrigerator with smart cooling technology', price: 45999, stock: 8, image: '../../../Public/pictures/pngimg.com - refrigerator_PNG101548.png', category: 'Refrigerator', date: '2024-02-10' },
                { id: 3, name: 'LG Washing Machine', description: 'Front-load washer with digital display and multiple wash programs', price: 28500, stock: 5, image: '../../../Public/pictures/vecteezy_modern-silver-washing-machine-with-digital-display-and-sleek_55983209.png', category: 'Washing Machine', date: '2024-01-20' },
                { id: 4, name: 'Smart TV 55 inch', description: 'Versatile tablet for work and play', price: 38999, stock: 20, image: '../../../Public/pictures/vecteezy_black-tv-screen-with-blank-screen_46013247.png', category: 'Television', date: '2024-03-01' },
                { id: 5, name: 'Panasonic Air Conditioner', description: 'Inverter split-type AC with powerful cooling and energy-saving features', price: 32900, stock: 0, image: '../../../Public/pictures/Air-Conditioner-Transparent-Images-PNG.png', category: 'Air Conditioner', date: '2024-02-15' },
                { id: 6, name: 'Electric Fan', description: 'Standing fan with oscillation and adjustable speed settings', price: 2499, stock: 12, image: '../../../Public/pictures/—Pngtree—a modern electric fan_16046829.png', category: 'Fan', date: '2024-01-25' },
                { id: 7, name: 'Microwave Oven', description: 'Compact microwave with auto-cook menus and defrost function', price: 6999, stock: 3, image: '../../../Public/pictures/microwave.png', category: 'Microwave', date: '2024-02-20' },
                { id: 8, name: 'Water Dispenser', description: 'Hot and cold water dispenser with safety lock feature', price: 5999, stock: 18, image: '../../../Public/pictures/water dispenser.png', category: 'Water Dispenser', date: '2024-03-05' }
            ],
            menuGroups: [
                {
                    title: 'Main',
                    isOpen: true,
                    items: [
                        { name: 'Dashboard', icon: 'dashboard', link: 'dashboard.php', active: true }
                    ]
                }
            ],
            // Notifications Data
            notifications: [],
            unreadCount: 0,
            showNotifications: false,
            pollInterval: null,
            showProfileMenu: false
        }
    },
    mounted() {
        this.loadProfile();
        this.fetchNotifications();
        this.pollInterval = setInterval(this.fetchNotifications, 30000);
    },
    beforeUnmount() {
        if (this.pollInterval) clearInterval(this.pollInterval);
    },
    computed: {
        filteredProducts() {
            if (!this.searchQuery) {
                return this.products;
            }
            const query = this.searchQuery.toLowerCase();
            return this.products.filter(product =>
                product.name.toLowerCase().includes(query) ||
                product.description.toLowerCase().includes(query)
            );
        },
        sortedProducts() {
            const products = [...this.filteredProducts];

            switch (this.sortBy) {
                case 'name-asc':
                    return products.sort((a, b) => a.name.localeCompare(b.name));
                case 'name-desc':
                    return products.sort((a, b) => b.name.localeCompare(a.name));
                case 'price-asc':
                    return products.sort((a, b) => a.price - b.price);
                case 'price-desc':
                    return products.sort((a, b) => b.price - a.price);
                case 'newest':
                    return products.sort((a, b) => new Date(b.date) - new Date(a.date));
                case 'oldest':
                    return products.sort((a, b) => new Date(a.date) - new Date(b.date));
                default:
                    return products;
            }
        }
    },
    methods: {
        async loadProfile() {
            try {
                const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                const data = await res.json();
                if (data.status === 'success') {
                    this.user.name = data.data.full_name;
                    this.user.image = data.data.profile_picture ? data.data.profile_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.data.full_name);
                }
            } catch (e) {
                console.error(e);
            }
        },
        async fetchNotifications() {
            try {
                const res = await fetch('../../../Controller/notification-controller.php?action=fetch');
                const data = await res.json();
                if (data.status === 'success') {
                    this.notifications = data.data || [];
                    this.unreadCount = data.unread_count || 0;
                }
            } catch (e) { console.error('Failed to load notifications', e); }
        },
        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
            if (this.showNotifications && this.unreadCount > 0) {
                this.markAllRead();
            }
        },
        async markRead(notif) {
            if (Number(notif.is_read) === 1) return;
            try {
                const res = await fetch('../../../Controller/notification-controller.php?action=mark_read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: notif.id })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    notif.is_read = 1;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (e) { console.error('Error marking as read', e); }
        },
        async markAllRead() {
            if (this.unreadCount === 0) return;
            try {
                const res = await fetch('../../../Controller/notification-controller.php?action=mark_all_read', { method: 'POST' });
                const data = await res.json();
                if (data.status === 'success') {
                    this.notifications.forEach(n => n.is_read = 1);
                    this.unreadCount = 0;
                }
            } catch (e) { console.error('Error marking all as read', e); }
        },
        async removeNotification(id) {
            try {
                const res = await fetch('../../../Controller/notification-controller.php?action=remove', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    const idx = this.notifications.findIndex(n => n.id === id);
                    if (idx > -1) {
                        if (Number(this.notifications[idx].is_read) === 0) this.unreadCount = Math.max(0, this.unreadCount - 1);
                        this.notifications.splice(idx, 1);
                    }
                }
            } catch (e) { console.error('Error removing notification', e); }
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
        getTypeClass(type) {
            const types = { 'System': 'text-blue-600 border-blue-200 bg-blue-50', 'Alert': 'text-red-600 border-red-200 bg-red-50', 'Message': 'text-emerald-600 border-emerald-200 bg-emerald-50' };
            return types[type] || 'text-gray-600 border-gray-200 bg-gray-50';
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        toggleProfileMenu() {
            this.showProfileMenu = !this.showProfileMenu;
        },
        toggleMenu(index) {
            this.menuGroups[index].isOpen = !this.menuGroups[index].isOpen;
        },
        handleImageError(event) {
            event.target.src = 'https://via.placeholder.com/300x300/f3f4f6/6b7280?text=No+Image';
        }
    }
})
app.mount('#app')