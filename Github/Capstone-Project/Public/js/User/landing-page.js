const app = Vue.createApp({
    data() {
        return {
            user: {
                name: 'User',
                image: 'https://ui-avatars.com/api/?name=User'
            },
            showProfileMenu: false,
            notifications: [],
            unreadCount: 0,
            showNotifications: false,
            hero: {
                heading: 'Upgrade Your Home with ',
                headingHighlight: 'Ease',
                description: 'An easy-to-use online platform where users can browse and purchase home appliances, track orders, and conveniently request repair services in one integrated system.',
                image: '../../../Public/pictures/Group 2 copy.png',
                ctaText: 'Shop Now!',
                ctaLink: '#products'
            },
            whyUs: {
                title: '- OUR SERVICES',
                heading: 'Why Choose Our Platform',
                subHeading: 'We provide a simple, reliable, and all-in-one solution for purchasing home appliances and requesting professional repair services.',
                cards: [
                    {
                        id: 1,
                        icon: '../../../Public/pictures/arrow_selector_tool_24dp_3B82F6_FILL0_wght400_GRAD0_opsz24.svg',
                        title: 'Easy & Convenient',
                        description: 'Browse appliances, place orders, and request repair services through a simple and user-friendly platform.'
                    },
                    {
                        id: 2,
                        icon: '../../../Public/pictures/shield_lock_24dp_22C55E_FILL0_wght400_GRAD0_opsz24.svg',
                        title: 'Trusted Services',
                        description: 'We provide quality appliances and reliable repair services to ensure customer satisfaction and peace of mind.'
                    },
                    {
                        id: 3,
                        icon: '../../../Public/pictures/stacks_24dp_A855F7_FILL0_wght400_GRAD0_opsz24.svg',
                        title: 'All-in-One Solution',
                        description: 'Manage purchases, track orders, and schedule appliance repairs in one integrated system.'
                    },
                    {
                        id: 4,
                        icon: '../../../Public/pictures/paper clip.svg',
                        title: 'Simple Application',
                        description: 'Apply your business in just a few easy steps. Submit your business information and required details for quick review and approval.'
                    },
                    {
                        id: 5,
                        icon: '../../../Public/pictures/lock.svg',
                        title: 'Verified Partnership',
                        description: 'Once approved, your business becomes a trusted partner on our platform, helping customers feel confident when choosing your products and services.'
                    },
                    {
                        id: 6,
                        icon: '../../../Public/pictures/chart.svg',
                        title: 'Manage & Grow',
                        description: 'Easily add products, manage orders, and handle repair requests using a dedicated dashboard built for business owners.'
                    }
                ]
            },
            about: {
                title: '- ABOUT US',
                heading: 'Your Trusted Platform for Appliances and Repair Service',
                description: 'FixMart is a modern e-commerce platform designed to make buying and maintaining home appliances simple, reliable, and convenient. Our system brings together appliance sales and repair services in one easy-to-use platform, helping customers find quality products and dependable technicians in just a few clicks.',
                image: '../../../Public/pictures/About us pic.png',
                features: [
                    'Online selling of various home appliances',
                    'Easy browsing and searching of products',
                    'Appliance repair and maintenance services'
                ],
                checkIcon: '../../../Public/pictures/check.svg',
                ctaText: 'Explore Now',
                arrowIcon: '../../../Public/pictures/arrow_right_alt_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg'
            },
            testimonials: {
                title: '- TESTIMONIALS',
                heading: 'What Our Customers Say',
                subHeading: 'Hear from our satisfied customers about their experience with FixMart',
                items: [
                    {
                        id: 1,
                        title: 'Excellent Service and Quality Products',
                        quote: 'I purchased a refrigerator from FixMart and the entire process was smooth. Delivery was on time and the technician set it up perfectly!',
                        name: 'Maria Santos',
                        position: 'Homeowner',
                        avatar: 'https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/karen-nelson.png'
                    },
                    {
                        id: 2,
                        title: 'Fast and Reliable Repair Service',
                        quote: 'My washing machine broke down and FixMart sent a technician the next day. It was fixed quickly and works perfectly now. Highly recommend!',
                        name: 'Roberto Cruz',
                        position: 'Business Owner',
                        avatar: 'https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/roberta-casas.png'
                    },
                    {
                        id: 3,
                        title: 'Great Platform for Appliances',
                        quote: 'FixMart has everything I need - from buying new appliances to getting them repaired. The all-in-one solution makes life so much easier.',
                        name: 'John Reyes',
                        position: 'Property Manager',
                        avatar: 'https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/jese-leos.png'
                    },
                    {
                        id: 4,
                        title: 'Trusted Brands and Affordable Prices',
                        quote: 'I love that FixMart offers all the major brands at competitive prices. I got a great deal on my air conditioner and the quality is excellent!',
                        name: 'Anna Lopez',
                        position: 'Teacher',
                        avatar: 'https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/joseph-mcfall.png'
                    }
                ]
            },
            products: {
                title: '- PRODUCTS',
                heading: 'Trusted Appliance Brands in One Platform',
                subHeading: 'We bring together well-known and reliable appliance brands to give you quality products and dependable repair services in one place.',
                brands: [
                    {
                        id: 1,
                        logo: '../../../Public/pictures/samsung logo.png'
                    },
                    {
                        id: 2,
                        logo: '../../../Public/pictures/lg logo.png'
                    },
                    {
                        id: 3,
                        logo: '../../../Public/pictures/whirlpool logo.png'
                    },
                    {
                        id: 4,
                        logo: '../../../Public/pictures/panasonic logo.png'
                    },
                    {
                        id: 5,
                        logo: '../../../Public/pictures/sonny logo.png'
                    },
                    {
                        id: 6,
                        logo: '../../../Public/pictures/sharp logo.png'
                    }
                ],
                items: [
                    {
                        id: 1,
                        name: 'Samsung Refrigerator',
                        image: '../../../Public/pictures/LG-Refrigerator-PNG-Transparent-Image.png',
                        price: '₱45,999',
                        category: 'Refrigerator',
                        description: 'Energy-efficient double door refrigerator with smart cooling technology'
                    },
                    {
                        id: 2,
                        name: 'LG Washing Machine',
                        image: '../../../Public/pictures/vecteezy_modern-silver-washing-machine-with-digital-display-and-sleek_55983209.png',
                        price: '₱28,500',
                        category: 'Washing Machine',
                        description: 'Front-load washer with digital display and multiple wash programs'
                    },
                    {
                        id: 3,
                        name: 'Panasonic Air Conditioner',
                        image: '../../../Public/pictures/Air-Conditioner-Transparent-Images-PNG.png',
                        price: '₱32,900',
                        category: 'Air Conditioner',
                        description: 'Inverter split-type AC with powerful cooling and energy-saving features'
                    },
                    {
                        id: 4,
                        name: 'Smart TV 55 inch',
                        image: '../../../Public/pictures/vecteezy_black-tv-screen-with-blank-screen_46013247.png',
                        price: '₱38,999',
                        category: 'Television',
                        description: '4K UHD Smart TV with streaming apps and voice control'
                    },
                    {
                        id: 5,
                        name: 'Electric Fan',
                        image: '../../../Public/pictures/—Pngtree—a modern electric fan_16046829.png',
                        price: '₱2,499',
                        category: 'Fan',
                        description: 'Standing fan with oscillation and adjustable speed settings'
                    },
                    {
                        id: 6,
                        name: 'Whirlpool Refrigerator',
                        image: '../../../Public/pictures/pngimg.com - refrigerator_PNG101548.png',
                        price: '₱42,500',
                        category: 'Refrigerator',
                        description: 'Spacious side-by-side refrigerator with water dispenser'
                    },
                    {
                        id: 7,
                        name: 'Microwave Oven',
                        image: '../../../Public/pictures/microwave.png',
                        price: '₱8,999',
                        category: 'Microwave',
                        description: 'Compact microwave with auto-cook menus and defrost function'
                    },
                    {
                        id: 8,
                        name: 'Water Dispenser',
                        image: '../../../Public/pictures/water dispenser.png',
                        price: '₱5,999',
                        category: 'Water Dispenser',
                        description: 'Hot and cold water dispenser with safety lock feature'
                    }
                ]
            },
            cta: {
                heading: 'Ready to Upgrade Your Home?',
                description: 'Explore our wide range of quality home appliances and reliable repair services. Get started today!',
                primaryBtnText: 'Shop Now',
                primaryBtnLink: '#products',
                secondaryBtnText: 'Request Repair',
                secondaryBtnLink: '../Repair/create.php'
            },
            contact: {
                title: '- CONTACT US',
                heading: 'Get in Touch',
                subHeading: 'Have questions or need assistance? Reach out to our team.',
                address: '123 FixMart St, Tech City, Philippines',
                email: 'support@fixmart.com',
                phone: '+63 900 000 0000',
                socials: [
                    { icon: 'facebook', link: '#' },
                    { icon: 'twitter', link: '#' },
                    { icon: 'instagram', link: '#' }
                ]
            }
        }
    },
    methods: {
        toggleProfileMenu() { this.showProfileMenu = !this.showProfileMenu; },
        toggleNotifications() { this.showNotifications = !this.showNotifications; },
        toggleSidebar() { window.location.href = '../Dashboard/index.php'; },
        handleImageError(event) {
            event.target.src = 'https://ui-avatars.com/api/?name=User';
        },
        async fetchNotifications() {
            try {
                const res = await fetch('../../../Controller/notification-controller.php?action=fetch');
                const data = await res.json();
                if (data.status === 'success') {
                    this.notifications = data.data || [];
                    this.unreadCount = data.unread_count || 0;
                }
            } catch (e) { console.error(e); }
        },
        async loadProfile() {
            try {
                const res = await fetch('../../../Controller/user-controller.php?action=get_profile');
                const data = await res.json();
                if (data.status === 'success' && data.data) {
                    this.user.name = data.data.full_name || data.data.username || 'User';
                    this.user.image = data.data.profile_picture || this.user.image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(this.user.name);
                }
            } catch (e) { console.error("Failed to load profile", e); }
        }
    },
    mounted() {
        this.loadProfile();
        this.fetchNotifications();
        // Initialize AOS after Vue renders the DOM
        setTimeout(() => {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true
                });
            }
        }, 100);
    }
});
app.config.errorHandler = (err, vm, info) => {
    console.error('VUE ERROR HANDLER:', err);
    const div = document.createElement('div');
    div.style.cssText = 'background:darkred;color:white;padding:20px;position:fixed;top:0;left:0;width:100%;z-index:999999;font-family:monospace;';
    div.innerHTML = '<strong>VUE RENDER CRASH:</strong><br>' + err.message + '<br>Info: ' + info;
    document.body.appendChild(div);
};
app.mount("#app");
