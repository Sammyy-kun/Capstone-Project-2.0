<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - FixMart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-emerald-500/5 border border-gray-100 p-8 lg:p-12 transition-all duration-500 hover:shadow-emerald-500/10">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-emerald-50 mb-6 text-emerald-500 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A4.477 4.477 0 005.145 5.515a4.477 4.477 0 00-1.272 2.719 4.477 4.477 0 001.272 2.719l6.499 6.499c.404.404 1 .404 1.404 0l6.499-6.499a4.477 4.477 0 001.272-2.719 4.477 4.477 0 00-1.272-2.719 4.477 4.477 0 00-2.719-1.272 4.477 4.477 0 00-2.719 1.272L12 10.518l-1.272-1.272a4.477 4.477 0 00-2.864-1.272z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Verify Code</h2>
                <p class="text-gray-500 text-sm leading-relaxed max-w-[280px] mx-auto">We've sent a 6-digit confirmation code to your email inbox.</p>
            </div>
            
            <form id="verifyForm" class="space-y-8">
                <input type="hidden" name="email" id="emailField">
                
                <div class="space-y-4">
                    <label class="block text-center text-sm font-bold text-gray-700 uppercase tracking-widest">Enter Verification Code</label>
                    <input type="text" name="code" required maxlength="6"
                        class="w-full py-5 rounded-2xl border-2 border-gray-100 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-300 bg-gray-50/50 hover:bg-white focus:bg-white text-center text-3xl font-black tracking-[0.75em] text-emerald-600 placeholder:text-gray-200"
                        placeholder="000000">
                </div>
                
                <div class="space-y-4">
                    <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed">
                        Verify Account
                    </button>
                    
                    <div class="text-center">
                        <button type="button" id="resendBtn" class="text-emerald-500 hover:text-emerald-600 text-sm font-bold transition flex items-center gap-2 mx-auto justify-center group">
                            <span>Didn't receive code? Resend</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-10 pt-8 border-t border-gray-100 text-center">
                <a href="User/login.php" class="inline-flex items-center gap-2 text-gray-400 hover:text-emerald-500 font-semibold transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Login
                </a>
            </div>
        </div>
    </div>

    <!-- Multi-Account Selection Modal -->
    <div id="accountModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4 transition-all duration-300">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform">
            <div class="p-8 border-b border-gray-50 text-center">
                <h3 class="text-2xl font-bold text-gray-900">Select Account</h3>
                <p class="text-gray-500 text-sm mt-2">Multiple accounts found. Please Choose one to reset.</p>
            </div>
            <div id="accountList" class="p-6 space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar">
                <!-- Accounts will be injected here -->
            </div>
            <div class="p-6 bg-gray-50/50">
                <button onclick="document.getElementById('accountModal').classList.add('hidden')" class="w-full py-4 text-gray-400 font-bold hover:text-gray-600 transition">Cancel</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Retrieve email from previous step
        const email = localStorage.getItem('reset_email');
        if (!email) {
            Swal.fire({
                icon: 'warning',
                title: 'Session Expired',
                text: 'Please start over.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'forgot_password.php';
            });
        }
        document.getElementById('emailField').value = email;

        document.getElementById('verifyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            
            btn.disabled = true;
            btn.innerText = "Verifying...";
            
            Swal.fire({
                title: 'Verifying Code...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('../../Controller/forgot-controller.php?action=verify_code', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = "Verify";

                if (data.status === 'success') {
                    Swal.close(); // Close loading
                    localStorage.setItem('reset_token', data.token);
                    
                    if (data.accounts && data.accounts.length > 1) {
                         showAccountSelection(data.accounts);
                    } else if (data.accounts && data.accounts.length === 1) {
                         // Auto select the only account
                         localStorage.setItem('reset_user_id', data.accounts[0].id);
                         window.location.href = 'reset_password.php';
                    } else {
                        // Fallback
                        Swal.fire({ icon: 'error', title: 'Error', text: "No accounts found." });
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Verification Failed', text: data.message });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred.' });
                btn.disabled = false;
                btn.innerText = "Verify";
            });
        });

        const resendBtn = document.getElementById('resendBtn');
        resendBtn.addEventListener('click', function() {
             const email = document.getElementById('emailField').value;
             if(!email) return;
             
             resendBtn.disabled = true;
             resendBtn.innerText = "Sending...";
             
             Swal.fire({
                title: 'Resending Code...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
             
             const formData = new FormData();
             formData.append('email', email);
             
             fetch('../../Controller/forgot-controller.php?action=send_code', {
                 method: 'POST',
                 body: formData
             })
             .then(res => res.json())
             .then(data => {
                 resendBtn.disabled = false;
                 resendBtn.innerText = "Resend Code";
                 
                 Swal.fire({
                     icon: data.status === 'success' ? 'success' : 'error',
                     title: data.status === 'success' ? 'Sent' : 'Error',
                     text: data.message
                 });
             })
             .catch(err => {
                 Swal.fire({ icon: 'error', title: 'Error', text: "Failed to resend." });
                 resendBtn.disabled = false;
                 resendBtn.innerText = "Resend Code";
             });
        });

        function showAccountSelection(accounts) {
            Swal.close();
            const modal = document.getElementById('accountModal');
            const list = document.getElementById('accountList');
            list.innerHTML = ''; // Clear

            accounts.forEach(acc => {
                const item = document.createElement('div');
                item.className = 'flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-50 cursor-pointer hover:bg-emerald-50 hover:border-emerald-100 transition-all duration-300 group';
                item.onclick = () => {
                    localStorage.setItem('reset_user_id', acc.id);
                    window.location.href = 'reset_password.php';
                };

                const img = acc.profile_picture ? acc.profile_picture : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(acc.full_name) + '&background=e5e7eb&color=374151';
                
                item.innerHTML = `
                    <div class="h-12 w-12 rounded-xl bg-gray-100 overflow-hidden shadow-inner flex-shrink-0 group-hover:scale-110 transition-transform">
                        <img src="${img}" class="h-full w-full object-cover">
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">${acc.username || 'User'}</p>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">${acc.role.toUpperCase()}</p>
                    </div>
                    <div class="text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                `;
                list.appendChild(item);
            });

            modal.classList.remove('hidden');
        }
    </script>
</body>
</html>

