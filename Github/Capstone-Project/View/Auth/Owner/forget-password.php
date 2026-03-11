<?php require '../../Layouts/header.php'; ?>
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
                <div class="lg:cols-span-1 bg-white rounded-l-xl border border-gray-200 lg:px-15 lg:py-13 lg:w-140 flex items-center justify-center">
                    <div class="w-full">
                    <div id="emailStep">
                        <h1 class="text-center font-bold text-2xl mb-3">Forgot Password?</h1>
                        <p class="text-center mb-8 text-sm sm:text-base text-gray-600">Enter your email address to receive a verification code</p>
                    
                        <form id="emailForm">
                            <div class="mb-6">
                                <label class="block mb-3 font-semibold">Email Address</label>
                                <input type="email" id="email" name="email" required 
                                class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3">
                            </div>

                            <div class="mb-6">
                                <button type="submit" 
                                class="w-full h-12 font-semibold bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition duration-300">
                                    Send Code
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-1">
                            <h1>Remember your password? <a href="login.php" class="text-emerald-500">Sign in</a></h1>
                        </div>
                    </div>

                    <div id="otpStep" class="hidden">
                        <h1 class="text-center font-bold text-2xl mb-3">Enter Verification Code</h1>
                        <p class="text-center mb-8 text-sm sm:text-base text-gray-600">We've sent a code to your email address</p>
                    
                        <form id="otpForm">
                            <div class="mb-6">
                                <label class="block mb-3 font-semibold text-center">Enter 6-digit code</label>
                                <div class="flex justify-center gap-2">
                                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl font-semibold rounded-md border-2 border-gray-200 focus:outline-emerald-500 focus:border-emerald-500" data-index="0">
                                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl font-semibold rounded-md border-2 border-gray-200 focus:outline-emerald-500 focus:border-emerald-500" data-index="1">
                                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl font-semibold rounded-md border-2 border-gray-200 focus:outline-emerald-500 focus:border-emerald-500" data-index="2">
                                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl font-semibold rounded-md border-2 border-gray-200 focus:outline-emerald-500 focus:border-emerald-500" data-index="3">
                                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl font-semibold rounded-md border-2 border-gray-200 focus:outline-emerald-500 focus:border-emerald-500" data-index="4">
                                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl font-semibold rounded-md border-2 border-gray-200 focus:outline-emerald-500 focus:border-emerald-500" data-index="5">
                                </div>
                            </div>

                            <div class="mb-6">
                                <button type="submit" 
                                     class="w-full h-12 font-semibold bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition duration-300">
                                    Verify Code
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="text-sm text-gray-600">Didn't receive the code? 
                                    <button type="button" id="resendBtn" class="text-emerald-500 hover:text-emerald-600 font-semibold">Resend</button>
                                </p>
                            </div>
                        </form>
                    </div>

                    <div id="resetStep" class="hidden">
                        <h1 class="text-center font-bold text-2xl mb-3">Reset Password</h1>
                        <p class="text-center mb-8 text-sm sm:text-base text-gray-600">Enter your new password</p>
                    
                        <form id="resetForm">
                            <div class="mb-6">
                                <label class="block mb-3 font-semibold">New Password</label>
                                <input type="password" id="newPassword" name="newPassword" required 
                                 class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3">
                            </div>

                            <div class="mb-6">
                                <label class="block mb-3 font-semibold">Confirm Password</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" required 
                                class="w-full h-12 rounded-md border-2 border-gray-200 focus:outline-emerald-500 px-4 py-3">
                            </div>

                            <div class="mb-6">
                                <button type="submit" 
                                     class="w-full h-12 font-semibold bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition duration-300">
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
                <div class="lg:cols-span-1">
                    <img class="lg:h-100 lg:w-125 rounded-r-xl border border-gray-200" src="../../../Public/pictures/pexels-pavel-danilyuk-6612794.jpg" alt="">
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