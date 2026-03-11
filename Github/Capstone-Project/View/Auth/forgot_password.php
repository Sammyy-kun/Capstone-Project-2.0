<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - FixMart</title>
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Forgot Password</h2>
                <p class="text-gray-500 text-sm leading-relaxed max-w-[280px] mx-auto">Enter your registered email to receive a secure verification code.</p>
            </div>
            
            <form id="forgotForm" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 ml-1">Email Address</label>
                    <div class="relative group">
                        <input type="email" name="email" required 
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-300 bg-gray-50/50 hover:bg-white focus:bg-white" 
                            placeholder="example@email.com">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed">
                    <span class="inline-flex items-center gap-2">
                        Send Secure Code
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </span>
                </button>
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

    <script>
        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            const formData = new FormData(this);
            
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-flex items-center gap-2">Sending...<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>';

            fetch('../../Controller/forgot-controller.php?action=send_code', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    localStorage.setItem('reset_email', formData.get('email'));
                    Swal.fire({
                        icon: 'success',
                        title: 'Verification Sent',
                        text: data.message,
                        confirmButtonColor: '#10B981',
                        customClass: {
                            popup: 'rounded-[2rem] border-none shadow-2xl',
                            confirmButton: 'rounded-xl px-10 py-3 font-bold'
                        }
                    }).then(() => {
                        window.location.href = 'verify_code.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: data.message,
                        confirmButtonColor: '#EF4444',
                        customClass: {
                            popup: 'rounded-[2rem] border-none shadow-2xl',
                            confirmButton: 'rounded-xl px-10 py-3 font-bold'
                        }
                    });
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Unable to connect to security server.',
                    confirmButtonColor: '#EF4444'
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    </script>
</body>
</html>
