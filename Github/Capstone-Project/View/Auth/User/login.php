<?php 
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
redirectIfLoggedIn();
require '../../Layouts/header.php'; 
?>
<body>
    <!--Navbar to-->
    <header>
        <nav class="fixed top-0 z-50 w-full flex items-center justify-between px-8 lg:px-40 py-4 bg-white" >
            <a href="<?= getDashboardUrl() ?>" class="text-2xl font-bold text-emerald-500 hover:text-emerald-600 transition">FixMart</a>
            <a href="<?= getDashboardUrl() ?>" class="nav-link text-black hover:text-emerald-500 transition duration-300 ease-in-out px-2" >Home</a>
        </nav>
    </header>
    <section class="min-h-screen flex justify-center items-center pt-28 pb-20 px-4 bg-gray-50">
        <div id="app" class="w-full max-w-md">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 lg:p-10 transition-all duration-500 hover:shadow-2xl">
                <div class="text-center mb-10">
                    <h1 class="font-bold text-3xl text-gray-900 mb-3">Welcome Back</h1>
                    <p class="text-gray-500">Please enter your details to sign in</p>
                </div>

                <form id="loginForm" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Username</label>
                        <input type="text" id="username" name="username" required 
                            class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white"
                            placeholder="Enter your username">
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label class="text-sm font-semibold text-gray-700">Password</label>
                            <a href="../forgot_password.php" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">Forgot?</a>
                        </div>
                        <div class="relative group">
                            <input type="password" id="loginPassword" name="password" required 
                                class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white pr-12"
                                placeholder="••••••••">
                            <button type="button" id="togglePassword" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-emerald-500 transition-colors focus:outline-none">
                                <svg id="iconEye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 ml-1">
                        <input type="checkbox" id="rememberMe" 
                            class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                        <label for="rememberMe" class="text-sm text-gray-600 cursor-pointer select-none">Remember for 30 days</label>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                        Sign In
                    </button>
                </form>

                <div class="relative my-10">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-400 font-medium">Or continue with</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button" id="googleLogin" 
                        class="flex items-center justify-center py-3.5 px-4 rounded-xl border border-gray-200 hover:bg-gray-50 transition-all duration-200 group">
                        <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                    </button>
                    <button type="button" id="facebookLogin" 
                        class="flex items-center justify-center py-3.5 px-4 rounded-xl border border-gray-200 hover:bg-gray-50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-[#1877F2] transition-transform duration-200 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </button>
                </div>

                <div class="text-center mt-10">
                    <p class="text-gray-500 text-sm font-medium">
                        Don't have an account? 
                        <a href="register.php" class="text-emerald-500 hover:text-emerald-600 font-bold ml-1 transition">Create one now</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
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

    <script>
        // Password Toggle Logic
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passInput = document.getElementById('loginPassword');
            const icon = this.querySelector('svg');
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            
            passInput.setAttribute('type', type);
            
            if (type === 'text') {
                // Show "Eye Slash" (Hidden) icon to indicate clicking will hide it
                // OR technically, currently visible = Eye Open? No, usually:
                // Eye = Show Me. Eye Slash = Hide Me.
                // If text is visible, we show Eye Slash.
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />`;
                this.classList.add('text-emerald-500');
            } else {
                // Show "Eye" (Show) icon
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
                this.classList.remove('text-emerald-500');
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Logging in...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('../../../Controller/auth-controller.php?action=login', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.close();
                    handleRedirect(data.role);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: data.message
                    });
                }
            })
            .catch(err => {
                console.error('Error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred during login.'
                });
            });
        });

        // Social Login Handlers (Mock)
        function socialLogin(provider) {
            // In a real app, this would use OAuth. For prototype, we mock it.
            // We'll ask for an email to simulate "receiving" one from Google/Facebook.
            const email = prompt(`[DEMO] Enter email to continue with ${provider}:`, "demo_user@gmail.com");
            
            if (email) {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('provider', provider);
                formData.append('full_name', 'Demo User'); // Mock name
                
                Swal.fire({
                    title: 'Logging in...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch('../../../Controller/auth-controller.php?action=social_login', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `Successfully logged in with ${provider}!`,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            handleRedirect(data.role);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: data.message
                        });
                    }
                })
                .catch(err => Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "Social login failed."
                }));
            }
        }

        document.getElementById('googleLogin').onclick = () => socialLogin('Google');
        document.getElementById('facebookLogin').onclick = () => socialLogin('Facebook');

        function handleRedirect(role) {
            const urlParams = new URLSearchParams(window.location.search);
            const redirect = urlParams.get('redirect');
            
            if (redirect === 'apply') {
                window.location.href = '../../../View/User/Business/apply.php';
                return;
            }

            if (role === 'owner') {
                window.location.href = '../../../View/Owner/Dashboard/dashboard.php';
            } else if (role === 'admin') {
                window.location.href = '../../../View/Admin/Dashboard/dashboard.php';
            } else if (role === 'technician') {
                window.location.href = '../../../View/Technician/Dashboard/index.php';
            } else {
                window.location.href = '../../../View/User/Dashboard/index.php';
            }
        }
    </script>
</body>
</html>