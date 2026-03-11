const userSidebarMenu = [
    {
        title: 'Main',
        isOpen: true,
        items: [
            { name: 'Dashboard', icon: 'dashboard', link: '../Dashboard/index.php', active: false }
        ]
    },
    {
        title: 'Shop',
        isOpen: true,
        items: [
            { name: 'Browse Products', icon: 'shop', link: '../Products/dashboard.php', active: false },
            { name: 'My Cart', icon: 'cart', link: '../Cart/index.php', active: false },
            { name: 'My Orders', icon: 'orders', link: '../Orders/index.php', active: false }
        ]
    },
    {
        title: 'Services',
        isOpen: true,
        items: [
            { name: 'Request Repair', icon: 'repair', link: '../Repair/create.php', active: false },
            { name: 'Repair History', icon: 'history', link: '../Repair/history.php', active: false },
            { name: 'My Invoices', icon: 'payments', link: '../Billing/index.php', active: false },
            { name: 'My Profile', icon: 'profile', link: '../Profile/edit.php', active: false }
        ]
    }
];

function calculateActiveUserMenu(menuGroups) {
    const currentPath = window.location.pathname.toLowerCase();

    if (currentPath.includes('dashboard')) {
        menuGroups[0].items[0].active = true;
    } else if (currentPath.includes('products/dashboard') || currentPath.includes('store/')) {
        menuGroups[1].items[0].active = true;
    } else if (currentPath.includes('cart')) {
        menuGroups[1].items[1].active = true;
    } else if (currentPath.includes('orders')) {
        menuGroups[1].items[2].active = true;
    } else if (currentPath.includes('repair/create')) {
        menuGroups[2].items[0].active = true;
    } else if (currentPath.includes('repair/history')) {
        menuGroups[2].items[1].active = true;
    } else if (currentPath.includes('billing')) {
        menuGroups[2].items[2].active = true;
    } else if (currentPath.includes('profile')) {
        menuGroups[2].items[3].active = true;
    }
    return menuGroups;
}

function getUserIcon(iconName) {
    const icons = {
        dashboard: 'M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 17 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17',
        shop: 'M4 6h16M4 10h16M4 14h16M4 18h16',
        cart: 'M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        repair: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
        history: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        payments: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        profile: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        orders: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        settings: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        logout: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
    };
    return icons[iconName] || '';
}
