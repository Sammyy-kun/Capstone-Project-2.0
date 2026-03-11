<?php 
require_once __DIR__ . '/../../../Config/constants.php';
require_once __DIR__ . '/../../../Config/session.php';
require '../../Layouts/header.php'; 
$role = $_GET['role'] ?? 'user';
$title = ($role === 'owner') ? 'Seller Registration' : 'Create an account';
$subtitle = ($role === 'owner') ? 'Sign up to start your business with FixMart' : 'Create an account to continue shopping';
?>
<div class="w-full">

<body>
    <!--Navbar to-->
    <header>
        <nav class="fixed top-0 z-50 w-full flex items-center justify-between px-8 lg:px-40 py-4 bg-white" >
            <a href="<?= getDashboardUrl() ?>" class="text-2xl font-bold text-emerald-500 hover:text-emerald-600 transition">FixMart</a>
            <a href="<?= getDashboardUrl() ?>" class="nav-link text-black hover:text-emerald-500 transition duration-300 ease-in-out px-2" >Home</a>
        </nav>
    </header>
    <section class="min-h-screen flex justify-center items-center pt-28 pb-20 px-4 bg-gray-50">
        <div id="app" class="w-full max-w-xl">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 lg:p-12 transition-all duration-500 hover:shadow-2xl">
                <div class="text-center mb-10">
                    <h1 class="font-bold text-3xl text-gray-900 mb-3"><?= $title ?></h1>
                    <p class="text-gray-500"><?= $subtitle ?></p>
                </div>

                <form id="signupForm" class="space-y-6">
                    <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required 
                                class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white"
                                placeholder="Your full name">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Username</label>
                            <input type="text" id="username" name="username" required 
                                class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white"
                                placeholder="3-20 characters">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Email Address</label>
                        <div class="relative group">
                            <input type="email" id="email" name="email" required 
                                class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white pr-12"
                                placeholder="example@email.com">
                            <span id="emailIndicator" class="absolute right-4 top-1/2 -translate-y-1/2 text-red-500 transition-colors duration-300" title="Email format">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Create Password</label>
                        <div class="relative group">
                            <input type="password" id="password" name="password" required 
                                class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white pr-12"
                                placeholder="••••••••">
                            <button type="button" id="togglePassword" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-emerald-500 transition-colors focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Password Health Check -->
                        <div class="mt-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Strength Requirements</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div id="req-len" class="flex items-center gap-2 text-xs font-semibold text-red-500 transition-colors duration-300">
                                    <div class="w-1.5 h-1.5 rounded-full bg-current"></div> 8+ Characters
                                </div>
                                <div id="req-upper" class="flex items-center gap-2 text-xs font-semibold text-red-500 transition-colors duration-300">
                                    <div class="w-1.5 h-1.5 rounded-full bg-current"></div> Uppercase
                                </div>
                                <div id="req-num" class="flex items-center gap-2 text-xs font-semibold text-red-500 transition-colors duration-300">
                                    <div class="w-1.5 h-1.5 rounded-full bg-current"></div> Number
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-start gap-3 p-4 rounded-2xl bg-gray-50/50 hover:bg-emerald-50 transition border border-gray-100 hover:border-emerald-200 cursor-pointer group">
                            <input type="checkbox" id="terms" 
                                class="mt-1 w-5 h-5 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-sm text-gray-600 leading-relaxed">
                                I agree to the <span class="text-emerald-600 font-bold">Terms of Service</span> and have read the <span class="text-emerald-600 font-bold">Privacy Policy</span>.
                            </span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                        Create Account
                    </button>
                </form>

                <div class="text-center mt-10">
                    <p class="text-gray-500 text-sm font-medium">
                        Already have an account? 
                        <a href="login.php" class="text-emerald-500 hover:text-emerald-600 font-bold ml-1 transition">Sign in here</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
    <script>
        // Real-time Email Validation
        const emailInput = document.getElementById('email');
        const emailInd = document.getElementById('emailIndicator');
        
        emailInput.addEventListener('input', function() {
            if (this.value.includes('@')) {
                emailInd.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
                emailInd.className = 'absolute right-4 top-1/2 -translate-y-1/2 text-emerald-500';
            } else {
                emailInd.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>';
                emailInd.className = 'absolute right-4 top-1/2 -translate-y-1/2 text-red-500';
            }
        });

        // Real-time Password Validation
        const passInput = document.getElementById('password');
        const reqLen = document.getElementById('req-len');
        const reqUpper = document.getElementById('req-upper');
        const reqNum = document.getElementById('req-num');

        passInput.addEventListener('input', function() {
            const val = this.value;
            
            // Length
            if (val.length >= 8) {
                reqLen.className = 'flex items-center gap-2 text-xs font-bold text-emerald-500 transition-colors duration-300';
            } else {
                reqLen.className = 'flex items-center gap-2 text-xs font-semibold text-red-500 transition-colors duration-300';
            }

            // Uppercase
            if (/[A-Z]/.test(val)) {
                reqUpper.className = 'flex items-center gap-2 text-xs font-bold text-emerald-500 transition-colors duration-300';
            } else {
                reqUpper.className = 'flex items-center gap-2 text-xs font-semibold text-red-500 transition-colors duration-300';
            }

            // Number
            if (/\d/.test(val)) {
                reqNum.className = 'flex items-center gap-2 text-xs font-bold text-emerald-500 transition-colors duration-300';
            } else {
                reqNum.className = 'flex items-center gap-2 text-xs font-semibold text-red-500 transition-colors duration-300';
            }
        });

        document.getElementById('togglePassword').addEventListener('click', function () {
            const icon = this.querySelector('svg');
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            
            if (type === 'text') {
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />`;
                this.classList.add('text-emerald-500');
            } else {
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
                this.classList.remove('text-emerald-500');
            }
        });

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const terms = document.getElementById('terms');
            if (!terms.checked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Terms Required',
                    text: 'You must agree to the Terms of Service and Privacy Policy to register.',
                    confirmButtonColor: '#10B981',
                    customClass: {
                        popup: 'rounded-3xl border-none shadow-2xl',
                        confirmButton: 'rounded-xl px-10 py-3 font-bold'
                    }
                });
                return;
            }

            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Registering...';

            const formData = new FormData(this);
            
            fetch('../../../Controller/auth-controller.php?action=register', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text()) 
            .then(text => {
                try {
                    const data = JSON.parse(text); 
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: data.message,
                            confirmButtonColor: '#10B981',
                            customClass: {
                                popup: 'rounded-3xl border-none shadow-2xl',
                                confirmButton: 'rounded-xl px-10 py-3 font-bold'
                            }
                        }).then(() => {
                            const role = document.querySelector('input[name="role"]').value;
                            if (role === 'owner') {
                                window.location.href = 'login.php?redirect=apply';
                            } else {
                                window.location.href = 'login.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: data.message,
                            confirmButtonColor: '#EF4444',
                            customClass: {
                                popup: 'rounded-3xl border-none shadow-2xl',
                                confirmButton: 'rounded-xl px-10 py-3 font-bold'
                            }
                        });
                        btn.disabled = false;
                        btn.innerText = originalText;
                    }
                } catch (e) {
                    console.error('Server returned invalid JSON:', text);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Something went wrong on the server.',
                        footer: '<pre class="text-[10px] text-left overflow-x-auto p-2 bg-gray-50 rounded">' + text.substring(0, 100) + '...</pre>'
                    });
                    btn.disabled = false;
                    btn.innerText = originalText;
                }
            })
            .catch(err => {
                console.error('Network Error:', err);
                btn.disabled = false;
                btn.innerText = originalText;
            });
        });
    </script>
    <footer class="footer sm:footer-horizontal bg-gray-50 text-base-content p-10">
            <aside>
                <a href="../../User/Home/index.php" class="text-2xl font-bold text-gray-700 hover:text-emerald-500 transition">FixMart</a>
                <p>Your trusted online shopping destination.<br/>Providing quality products since 2026.</p>
            </aside>
            <nav>
                <h6 class="footer-title">Services</h6>
                <a class="link link-hover">Branding</a>
                <a class="link link-hover">Design</a>
                <a class="link link-hover ">Marketing</a>
                <a class="link link-hover ">Advertisement</a>
            </nav>
            <nav>
                <h6 class="footer-title">Company</h6>
                <a class="link link-hover">About us</a>
                <a class="link link-hover">Contact</a>
                <a class="link link-hover">Jobs</a>
                <a class="link link-hover">Press kit</a>
            </nav>
            <nav>
                <h6 class="footer-title">Legal</h6>
                <a class="link link-hover">Terms of use</a>
                <a class="link link-hover">Privacy policy</a>
                <a class="link link-hover">Cookie policy</a>
            </nav>
    </footer>
</body>
</html>