<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FixMart</title>
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h2>
                <p class="text-gray-500 text-sm leading-relaxed max-w-[250px] mx-auto">Create a new secure password for your account.</p>
            </div>
            
            <form id="resetForm" class="space-y-6">
                <input type="hidden" name="email" id="emailField">
                <input type="hidden" name="token" id="tokenField">
                <input type="hidden" name="user_id" id="userIdField">
                
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">New Password</label>
                        <input type="password" name="password" required 
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-300 bg-gray-50/50 hover:bg-white focus:bg-white" 
                            placeholder="••••••••">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" required 
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-300 bg-gray-50/50 hover:bg-white focus:bg-white" 
                            placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed">
                    Secure Update
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-gray-100 text-center">
                <button type="button" onclick="cancelReset()" class="inline-flex items-center gap-2 text-gray-400 hover:text-emerald-500 font-semibold transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Cancel & Return
                </button>
            </div>
        </div>
    </div>

    <script>
        const email = localStorage.getItem('reset_email');
        const token = localStorage.getItem('reset_token');
        const userId = localStorage.getItem('reset_user_id');
        
        if (!email || !token) {
            Swal.fire({
                icon: 'warning',
                title: 'Session Expired',
                text: 'Invalid session. Please start over.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'forgot_password.php';
            });
        }
        
        document.getElementById('emailField').value = email;
        document.getElementById('tokenField').value = token;
        if(userId) document.getElementById('userIdField').value = userId;

        document.getElementById('resetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');

             if (formData.get('password') !== formData.get('confirm_password')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Passwords do not match.'
                });
                return;
            }
            
            btn.disabled = true;
            btn.innerText = "Processing...";
            
             Swal.fire({
                title: 'Updating Password...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('../../Controller/forgot-controller.php?action=reset_password', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = "Change Password";

                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Password changed successfully! You can now login.',
                        confirmButtonText: 'Go to Login'
                    }).then(() => {
                        // Clear storage
                        localStorage.removeItem('reset_email');
                        localStorage.removeItem('reset_token');
                        window.location.href = 'User/login.php'; // Redirect to user login
                    });
                } else {
                    Swal.fire({
                         icon: 'error',
                         title: 'Error',
                         text: data.message
                    });
                }
            })
            .catch(err => {
                 Swal.fire({
                     icon: 'error',
                     title: 'Error',
                     text: 'An error occurred.'
                 });
                 btn.disabled = false;
                 btn.innerText = "Change Password";
            });
        });

        function cancelReset() {
             localStorage.removeItem('reset_email');
             localStorage.removeItem('reset_token');
             localStorage.removeItem('reset_user_id');
             window.location.href = 'User/login.php';
        }
    </script>
</body>
</html>
