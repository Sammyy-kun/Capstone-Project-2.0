<?php require '../../Layouts/header.php'; ?>
<div class="w-full">

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
                <div class="lg:cols-span-1 bg-white rounded-l-xl border border-gray-200 lg:p-16 lg:w-150">
                    <h1 class="text-center font-bold text-2xl mb-3">Create an account</h1>
                    <form id="signupForm">
                        <input type="hidden" name="role" value="owner">
                        <div class="mb-6">
                            <label class="block mb-3 font-semibold">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required 
                            class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3">
                        </div>
                        
                        <div class="mb-6">
                            <label class="block mb-3 font-semibold">Username</label>
                            <input type="text" id="username" name="username" required 
                            placeholder="Alphanumeric, 3-20 chars"
                            class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3">
                        </div>
                    
                        <div class="mb-6">
                            <label class="block mb-3 font-semibold">Email</label>
                            <div class="relative">
                                <input type="email" id="email" name="email" required 
                                class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3 pr-10">
                                <span id="emailIndicator" class="absolute right-3 top-3 text-red-500" title="Missing @">
                                    &#9888; <!-- Warning Icon -->
                                </span>
                            </div>
                        </div>
                    
                        <div class="mb-6">
                            <label class="block mb-3 font-semibold">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required 
                                class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3 pr-10">
                                <button type="button" id="togglePassword" class="absolute right-3 top-3 text-gray-500 hover:text-emerald-500 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                             <!-- Password Guide -->
                            <div class="mt-2 text-sm text-gray-600 bg-gray-50 p-3 rounded-md border border-gray-200">
                                <p class="font-bold mb-1">Password Requirements:</p>
                                <ul class="list-disc list-inside">
                                    <li id="req-len" class="text-red-500">Minimum 8 characters</li>
                                    <li id="req-upper" class="text-red-500">At least 1 uppercase letter</li>
                                    <li id="req-num" class="text-red-500">At least 1 number</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block mb-3 font-semibold">Business Name</label>
                            <input type="text" id="business_name" name="business_name" required 
                            class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3">
                        </div>

                        <div class="mb-5">
                            <input type="checkbox" class="mr-2">
                            <label for="rememberMe">I agree to the <span class="text-emerald-400">Terms of Service</span> and have read the <span class="text-emerald-400">Privacy Policy</span>.</label>
                        </div>

                        <div class="mb-6">
                            <button type="submit" class="w-full h-12 font-semibold bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition duration-300">
                                Sign up
                            </button>
                        </div>
                    </form>
                     <script>
                        // Real-time Email Validation
                        const emailInput = document.getElementById('email');
                        const emailInd = document.getElementById('emailIndicator');
                        
                        emailInput.addEventListener('input', function() {
                            if (this.value.includes('@')) {
                                emailInd.innerHTML = '&#10004;'; // Checkmark
                                emailInd.className = 'absolute right-3 top-3 text-emerald-500 font-bold';
                            } else {
                                emailInd.innerHTML = '&#9888;'; // Warning
                                emailInd.className = 'absolute right-3 top-3 text-red-500 font-bold';
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
                                reqLen.className = 'text-emerald-500';
                            } else {
                                reqLen.className = 'text-red-500';
                            }

                            // Uppercase
                            if (/[A-Z]/.test(val)) {
                                reqUpper.className = 'text-emerald-500';
                            } else {
                                reqUpper.className = 'text-red-500';
                            }

                            // Number
                            if (/\d/.test(val)) {
                                reqNum.className = 'text-emerald-500';
                            } else {
                                reqNum.className = 'text-red-500';
                            }
                        });

                        document.getElementById('togglePassword').addEventListener('click', function () {
                            const icon = this.querySelector('svg');
                            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
                            passInput.setAttribute('type', type);
                            
                            if (type === 'text') {
                                // Show "Eye Slash" (Hidden) icon
                                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />`;
                                this.classList.add('text-emerald-500');
                            } else {
                                // Show "Eye" (Show) icon
                                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
                                this.classList.remove('text-emerald-500');
                            }
                        });

                        document.getElementById('signupForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            
                            const btn = this.querySelector('button[type="submit"]');
                            const originalText = btn.innerText;
                            btn.disabled = true;
                            btn.innerText = 'Registering...';

                            const formData = new FormData(this);
                            
                            Swal.fire({
                                title: 'Creating Account...',
                                text: 'Please wait',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            fetch('../../../Controller/auth-controller.php?action=register', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                btn.disabled = false;
                                btn.innerText = originalText;
                                
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Registration Successful',
                                        text: data.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.href = 'login.php';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Registration Failed',
                                        text: data.message
                                    });
                                    // btn already reset above
                                }
                            })
                            .catch(err => {
                                console.error('Error:', err);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred during registration.'
                                });
                                btn.disabled = false;
                                btn.innerText = originalText;
                            });
                        });
                    </script>
                    <div class="text-center mt-1">
                        <h1>Already have an Account? <a href="login.php" class="text-emerald-500">Sign in</a></h1>
                    </div>
                </div>
                <div class="lg:cols-span-1">
                    <img class="lg:h-100 lg:w-120 rounded-r-xl border border-gray-200" src="../../../Public/pictures/pexels-jose-andres-pacheco-cortes-3641213-5463576.jpg" alt="">
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