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
            <a href="../../User/Home/index.php" class="text-2xl font-bold text-emerald-500 hover:text-emerald-600 transition">FixMart</a>
            <a href="../../User/Home/index.php" class="nav-link text-black hover:text-emerald-500 transition duration-300 ease-in-out px-2" >Home</a>
        </nav>
    </header>
    <section class="min-h-screen flex justify-center items-center pt-20 pb-10 px-4">
         <div id="app">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <div class="lg:cols-span-1 bg-white rounded-l-xl border border-gray-200 lg:px-15 lg:py-13 lg:w-140">
                    <h1 class="text-center font-bold text-2xl mb-3">Sign in to your account</h1>
                    <p class="text-center mb-8 text-sm sm:text-base text-gray-600">Fill in your credentials to access your account</p>
                    <form id="loginForm">
                        <div class="mb-6">
                            <label class="block mb-3 font-semibold">Username</label>
                            <input type="text" id="username" name="username" required class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3">
                        </div>
                        <div class="mb-6">
                            <label class="block mb-3 font-semibold">Password</label>
                            <div class="relative">
                                <input type="password" id="loginPassword" name="password" required class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3 pr-10">
                                <button type="button" id="togglePassword" class="absolute right-3 top-3 text-gray-500 hover:text-emerald-500 focus:outline-none">
                                    <!-- Eye Icon (Show) -->
                                    <svg id="iconEye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <!-- Eye Slash Icon (Hide) -->
                                    <svg id="iconEyeSlash" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 hidden">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            <div class="text-right mt-2">
                                <a href="../User/forgot_password.php" class="text-sm text-emerald-500 hover:underline">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-5 mb-7">
                            <div>
                                <input type="checkbox" id="rememberMe" class="mr-2">
                                <label for="rememberMe">Remember Me</label>
                            </div>
                        <div>
                            <a href="forget-password.php" class="text-emerald-500 hover:text-emerald-600">Forgot Password?</a>
                        </div>
                        </div>
                            <div class="mb-6">
                                <button type="submit" class="w-full h-12 font-semibold bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition duration-300">Login</button>
                            </div>
                    </form>
                    <script>
                        document.getElementById('togglePassword').addEventListener('click', function () {
                            const passInput = document.getElementById('loginPassword');
                            const iconEye = document.getElementById('iconEye');
                            const iconEyeSlash = document.getElementById('iconEyeSlash');
                            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
                            
                            passInput.setAttribute('type', type);
                            
                            if (type === 'text') {
                                // Show "Eye Slash" (meaning 'hide me'), hide "Eye"
                                iconEye.classList.add('hidden');
                                iconEyeSlash.classList.remove('hidden');
                                this.classList.add('text-emerald-500');
                            } else {
                                // Show "Eye" (meaning 'show me'), hide "Eye Slash"
                                iconEye.classList.remove('hidden');
                                iconEyeSlash.classList.add('hidden');
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
                                    // Owners go to dashboard
                                    window.location.href = '../../../View/Owner/Dashboard/dashboard.php';
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
                    </script>
                    
                    <div class="relative flex items-center justify-center my-6">
                        <div class="border-t border-gray-300 flex-grow"></div>
                        <span class="px-4 text-sm text-gray-500">Or continue with</span>
                        <div class="border-t border-gray-300 flex-grow"></div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <button type="button" class="w-full h-12 flex items-center justify-center gap-3 border-2 border-gray-200 rounded-lg hover:bg-gray-50 transition duration-300">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            <span class="font-medium text-gray-700">Continue with Google</span>
                        </button>
                        <button type="button" class="w-full h-12 flex items-center justify-center gap-3 bg-[#1877F2] text-white rounded-lg hover:bg-[#166FE5] transition duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span class="font-medium">Continue with Facebook</span>
                        </button>
                    </div>
                    
                    <div class="text-center mt-1">
                        <h1>Don't have an Account? <a href="register.php" class="text-emerald-500">Sign up</a></h1>
                    </div>
                </div>
                <div class="lg:cols-span-1">
                    <img class="lg:h-100 lg:w-125 rounded-r-xl border border-gray-200" src="../../../Public/pictures/pexels-jose-andres-pacheco-cortes-3641213-5463580.jpg" alt="">
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
</body>
</html>