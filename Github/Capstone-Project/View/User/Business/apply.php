<?php
require_once __DIR__ . '/../../../Config/session.php';
require_once __DIR__ . '/../../../Config/constants.php';
require_once __DIR__ . '/../../Layouts/header.php';
?>
<style>
    [v-cloak] { display: none; }
    .step-fade-enter-active, .step-fade-leave-active { transition: opacity 0.3s ease; }
    .step-fade-enter-from, .step-fade-leave-to { opacity: 0; }
</style>
<body class="bg-gray-50">
<script src="../../../Public/js/User/sidebar.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/User/sidebar.js') ?>"></script>
<div id="app" v-cloak>
    <header>
       <nav class="fixed top-0 left-0 right-0 z-[60] w-full flex items-center justify-between px-6 lg:px-8 py-3 shadow-sm bg-white border-b border-gray-200" >
            <div class="flex items-center gap-4">
                <a href="<?= getDashboardUrl() ?>" class="text-xl font-semibold text-emerald-500 hover:text-emerald-600 transition">FixMart</a>
            </div>
            
            <!-- Right side elements removed per user request -->
        </nav>
    </header>

    <script>
        const IS_LOGGED_IN = <?= isLoggedIn() ? 'true' : 'false' ?>;
    </script>

    <main class="pt-24 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="mb-8">
                <h1 class="text-gray-900 text-3xl font-bold">Business Registration</h1>
                <p class="text-gray-500 mt-2">Complete the form below to start selling on FixMart</p>
            </div>

            <div v-if="appStatus === 'Rejected'" class="mb-8 bg-white border border-red-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-red-700">Previous Application Rejected</h3>
                        <p class="text-xs text-red-500 mt-0.5">Please review the reasons below and resubmit with corrections.</p>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm font-semibold text-gray-600 mb-3">Reason(s) for rejection:</p>
                    <ul class="space-y-2 mb-4">
                        <li v-for="reason in parsedRejectionReasons" :key="reason" class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ reason }}
                        </li>
                    </ul>
                    <div v-if="rejectionNote" class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Additional Notes</p>
                        <p class="text-sm text-gray-700">{{ rejectionNote }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Progress Steps Grid -->
            <div class="bg-white rounded-[2rem] shadow-xl shadow-emerald-500/5 border border-gray-100 mb-10 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-5">
                    <div v-for="(step, index) in steps" :key="index" :class="['relative p-7 border-b md:border-b-0 md:border-r border-gray-50 last:border-0 transition-all duration-300', currentStep === index + 1 ? 'bg-emerald-50/30' : '']">
                        <div class="flex items-center gap-5">
                            <div class="flex-shrink-0">
                                <div v-if="currentStep > index + 1" class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div v-else :class="['w-12 h-12 rounded-2xl border-2 flex items-center justify-center font-bold text-base transition-all duration-300 shadow-sm', currentStep === index + 1 ? 'border-emerald-500 bg-emerald-500 text-white shadow-emerald-500/30' : 'border-gray-100 bg-gray-50 text-gray-400 font-medium']">
                                    {{ index + 1 }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 :class="['font-bold text-sm mb-1 transition-colors duration-300', currentStep >= index + 1 ? 'text-gray-900' : 'text-gray-400']">{{ step.title }}</h3>
                                <div class="flex items-center gap-1.5">
                                    <div :class="['w-1.5 h-1.5 rounded-full transition-colors duration-300', currentStep === index + 1 ? 'bg-emerald-500 animate-pulse' : (currentStep > index + 1 ? 'bg-emerald-400' : 'bg-gray-200')]"></div>
                                    <p :class="['text-[10px] uppercase tracking-widest font-bold transition-colors duration-300', currentStep >= index + 1 ? 'text-emerald-600' : 'text-gray-400']">{{ currentStep === index + 1 ? 'Active' : (currentStep > index + 1 ? 'Done' : 'Wait') }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="currentStep === index + 1" class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500"></div>
                    </div>
                </div>
            </div>

            <!-- Form Content Container -->
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-emerald-500/5 border border-gray-100 p-8 lg:p-14 mb-20 transition-all duration-500 hover:shadow-emerald-500/10">
                
                <transition name="step-fade" mode="out-in">
                    <!-- Step 1: Owner Information -->
                    <div v-if="currentStep === 1" :key="1">
                        <div class="mb-10">
                            <h2 class="text-2xl font-bold text-gray-800">Owner Information</h2>
                            <p class="text-gray-500 mt-1">Verify your identity as the business owner</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">First Name</label>
                                <input v-model="form.first_name" type="text" 
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                    placeholder="Enter your first name">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Last Name</label>
                                <input v-model="form.last_name" type="text" 
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                    placeholder="Enter your last name">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Email Address</label>
                                <input v-model="form.email" type="email" 
                                    :class="['w-full px-5 py-4 rounded-xl border focus:ring-4 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white', isValidEmail ? 'border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/10' : 'border-red-500 focus:border-red-500 focus:ring-red-500/10']" 
                                    placeholder="example@email.com">
                                <p v-if="!isValidEmail" class="text-xs text-red-500 font-bold ml-1 mt-1 transition-all">Please enter a valid email address with '@' and domain.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Mobile Number</label>
                                <input v-model="form.phone" type="text"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                    placeholder="09XX XXX XXXX" maxlength="11">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Government ID Type</label>
                                <select v-model="form.id_type" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white cursor-pointer appearance-none">
                                    <option value="" disabled>Select ID Type</option>
                                    <option value="National ID">National ID</option>
                                    <option value="Drivers License">Driver's License</option>
                                    <option value="Passport">Passport</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">ID Number</label>
                                <input v-model="form.gov_id" type="text"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                    placeholder="Enter ID number">
                            </div>

                            <?php if (!isLoggedIn()): ?>
                                <!-- Account Creation Section -->
                                <div class="md:col-span-2 mt-6 pt-6 border-t border-gray-100">
                                    <h3 class="text-lg font-bold text-emerald-600 mb-4">Create Your FixMart Account</h3>
                                    <p class="text-sm text-gray-500 mb-6">These credentials will be used for your shop account.</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="space-y-2">
                                            <label class="text-sm font-bold text-gray-700 ml-1">Choose Username</label>
                                            <input v-model="form.username" type="text" 
                                                class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                                placeholder="Alphanumeric, 3-20 chars">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-sm font-bold text-gray-700 ml-1">Set Password</label>
                                            <div class="relative group">
                                                <input v-model="form.password" :type="showPassword ? 'text' : 'password'" 
                                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white pr-12" 
                                                    placeholder="Create a strong password">
                                                <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-emerald-500 transition">
                                                    <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </button>
                                            </div>
                                            <!-- Password Requirements Indicator -->
                                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                                                <div :class="['flex items-center gap-1.5 text-[10px] font-bold transition-colors', form.password.length >= 8 ? 'text-emerald-500' : 'text-gray-400']">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    8+ CHARS
                                                </div>
                                                <div :class="['flex items-center gap-1.5 text-[10px] font-bold transition-colors', /[A-Z]/.test(form.password) ? 'text-emerald-500' : 'text-gray-400']">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    1 UPPERCASE
                                                </div>
                                                <div :class="['flex items-center gap-1.5 text-[10px] font-bold transition-colors', /\d/.test(form.password) ? 'text-emerald-500' : 'text-gray-400']">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    1 NUMBER
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="md:col-span-2 space-y-3">
                                <label class="text-sm font-bold text-gray-700">Upload Government ID</label>
                                <div @click="$refs.govIdInput.click()" class="relative border-4 border-dashed border-gray-100 rounded-xl p-12 flex flex-col items-center justify-center hover:bg-emerald-50 hover:border-emerald-200 transition cursor-pointer group bg-gray-50/30">
                                    <input type="file" ref="govIdInput" class="hidden" @change="onFileChange($event, 'gov_id_file')">
                                    <div v-if="files.gov_id_file" class="flex flex-col items-center text-center w-full relative z-10">
                                        <button @click.stop="removeFile('gov_id_file')" class="absolute -top-6 -right-6 p-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-full transition shadow-sm z-20" title="Remove image">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                        <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mb-4 shadow-sm">
                                            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <h4 class="font-bold text-emerald-900 truncate w-full max-w-[200px]">{{ files.gov_id_file.name }}</h4>
                                        <p class="text-sm text-emerald-500 mt-1 uppercase font-bold tracking-widest">Document Ready</p>
                                    </div>
                                    <div v-else class="flex flex-col items-center text-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition shadow-sm">
                                            <svg class="w-10 h-10 text-gray-400 group-hover:text-emerald-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </div>
                                        <h4 class="font-bold text-gray-800">Click to Upload Document</h4>
                                        <p class="text-xs text-gray-400 mt-2 font-medium">PNG, JPG or PDF (MAX 5MB)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Physical Document Verification Alert -->
                        <div class="mt-8 p-5 rounded-2xl bg-blue-50 border border-blue-100 flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-blue-800 mb-1">ID Photo Requirements</h4>
                                <p class="text-xs text-blue-700 leading-relaxed">Please ensure your uploaded ID is a <strong>clear, unobstructed photo</strong> of a valid physical document. Blurry, cropped, or digitally edited photos will be <strong>rejected</strong>. Keep your original documents ready for physical presentation if requested.</p>
                            </div>
                        </div>
                    </div>


                    <!-- Step 2: Business Information -->
                    <div v-if="currentStep === 2" :key="2">
                        <div class="mb-10">
                            <h2 class="text-2xl font-bold text-gray-800">Business Information</h2>
                            <p class="text-gray-500 mt-1">Details about your shop or service center</p>
                        </div>

                        <!-- Business Form Selection Cards -->
                        <div class="mb-10">
                            <label class="text-sm font-bold text-gray-700 ml-1 mb-4 block">Business Form <span class="text-red-400">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div v-for="bf in businessForms" :key="bf.value"
                                     @click="form.business_form = bf.value"
                                     :class="[
                                         'relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-300 group',
                                         form.business_form === bf.value 
                                             ? 'border-emerald-500 bg-emerald-50/60 shadow-lg shadow-emerald-500/10 scale-[1.02]' 
                                             : 'border-gray-100 bg-white hover:border-emerald-200 hover:bg-emerald-50/30 hover:shadow-md'
                                     ]">
                                    <div class="flex flex-col items-center text-center gap-3">
                                        <div :class="[
                                            'w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-300',
                                            form.business_form === bf.value 
                                                ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' 
                                                : 'bg-gray-100 text-gray-400 group-hover:bg-emerald-100 group-hover:text-emerald-500'
                                        ]">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="bf.icon"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 :class="['font-bold text-sm transition-colors', form.business_form === bf.value ? 'text-emerald-700' : 'text-gray-800']">{{ bf.label }}</h4>
                                            <p class="text-[10px] text-gray-400 mt-1 leading-snug">{{ bf.desc }}</p>
                                        </div>
                                    </div>
                                    <div v-if="form.business_form === bf.value" class="absolute top-2 right-2">
                                        <div class="w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Name</label>
                                <input v-model="form.business_name" type="text" 
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                    placeholder="Enter your business name">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Type</label>
                                <select v-model="form.business_type" 
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white cursor-pointer appearance-none">
                                    <option value="" disabled>Select Business Type</option>
                                    <option value="Appliance Store">Appliance Store</option>
                                    <option value="Appliance Repair & Sales">Appliance Repair & Sales</option>
                                    <option value="Independent Technician">Independent Technician</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Email</label>
                                <input v-model="form.business_email" type="email" 
                                    :class="['w-full px-5 py-4 rounded-xl border focus:ring-4 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white', isValidBusinessEmail ? 'border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/10' : 'border-red-500 focus:border-red-500 focus:ring-red-500/10']" 
                                    placeholder="shop@email.com">
                                <p v-if="!isValidBusinessEmail" class="text-xs text-red-500 font-bold ml-1 mt-1 transition-all">Please enter a valid business email with '@' and domain.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Contact Number</label>
                                <input v-model="form.business_phone" type="text"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                    placeholder="09XX XXX XXXX" maxlength="11">
                            </div>
                            <div class="md:col-span-1 space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">TIN Number</label>
                                <input v-model="form.tin_number" @input="formatTIN" type="text" 
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                    placeholder="000-000-000-000" maxlength="15">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Business Address</label>
                                <textarea v-model="form.business_address" rows="3" 
                                    class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white resize-none" 
                                    placeholder="Enter complete business address"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Products / Services -->
                    <div v-if="currentStep === 3" :key="3">
                        <div class="mb-10">
                            <h2 class="text-2xl font-bold text-gray-800">Offerings & Pricing</h2>
                            <p class="text-gray-500 mt-1">Define what services or products you provide</p>
                        </div>
                        <div class="space-y-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 ml-1">Service/Product Description</label>
                                <textarea v-model="form.offer_details" rows="6" 
                                    class="w-full px-6 py-5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white resize-none" 
                                    placeholder="Explain your expertise, diagnostic fees, and specializations..."></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Service Coverage Area</label>
                                    <input v-model="form.service_area" type="text" 
                                        class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                        placeholder="e.g. Metro Manila, Cavite">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Est. Average Pricing (PHP)</label>
                                    <input v-model="form.avg_pricing" type="text"
                                        class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all duration-200 bg-gray-50/50 hover:bg-white focus:bg-white" 
                                        placeholder="500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Legitimacy -->
                    <div v-if="currentStep === 4" :key="4">
                        <div class="mb-10 text-center md:text-left">
                            <h2 class="text-2xl font-bold text-gray-800">Business Legitimacy</h2>
                            <p class="text-gray-500 mt-1">Required documents for trust and verification</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Business Permit -->
                            <div @click="$refs.permitInput.click()" class="relative border-4 border-dashed border-gray-100 rounded-xl p-10 flex flex-col items-center justify-center hover:bg-emerald-50 hover:border-emerald-200 transition cursor-pointer group bg-gray-50/20">
                                <input type="file" ref="permitInput" class="hidden" @change="onFileChange($event, 'business_permit')">
                                <div v-if="files.business_permit" class="flex flex-col items-center text-center w-full relative z-10">
                                    <button @click.stop="removeFile('business_permit')" class="absolute -top-4 -right-4 p-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-full transition shadow-sm z-20" title="Remove image">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                    <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h4 class="font-bold text-emerald-900">Business Permit</h4>
                                    <p class="text-[10px] text-emerald-500 mt-1 font-bold truncate max-w-[150px]">{{ files.business_permit.name }}</p>
                                </div>
                                <div v-else class="flex flex-col items-center text-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition">
                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-emerald-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                    <h4 class="font-bold text-gray-800">Business Permit</h4>
                                    <p class="text-xs text-gray-400 mt-1">Upload Permit (Max 5MB)</p>
                                </div>
                            </div>

                            <!-- DTI/SEC -->
                            <div @click="$refs.dtiInput.click()" class="relative border-4 border-dashed border-gray-100 rounded-xl p-10 flex flex-col items-center justify-center hover:bg-emerald-50 hover:border-emerald-200 transition cursor-pointer group bg-gray-50/20">
                                <input type="file" ref="dtiInput" class="hidden" @change="onFileChange($event, 'dti_registration')">
                                <div v-if="files.dti_registration" class="flex flex-col items-center text-center w-full relative z-10">
                                    <button @click.stop="removeFile('dti_registration')" class="absolute -top-4 -right-4 p-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-full transition shadow-sm z-20" title="Remove image">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                    <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h4 class="font-bold text-emerald-900">DTI/SEC Registration</h4>
                                    <p class="text-[10px] text-emerald-500 mt-1 font-bold truncate max-w-[150px]">{{ files.dti_registration.name }}</p>
                                </div>
                                <div v-else class="flex flex-col items-center text-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition">
                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-emerald-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                    <h4 class="font-bold text-gray-800">DTI/SEC Doc</h4>
                                    <p class="text-xs text-gray-400 mt-1">Registration Proof</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 p-6 rounded-2xl bg-amber-50 border border-amber-200 flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-amber-800 mb-1">⚠ Physical Verification Notice</h4>
                                <p class="text-sm text-amber-700 leading-relaxed font-medium">FixMart may require <strong>physical verification</strong> of all submitted documents. You must be able to present the <strong>original copies</strong> of your Government ID, Business Permit, and DTI/SEC Registration upon request. Failure to comply may result in account suspension.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Agreement -->
                    <div v-if="currentStep === 5" :key="5">
                        <div class="mb-10">
                            <h2 class="text-2xl font-bold text-gray-800">Platform Agreement</h2>
                            <p class="text-gray-500 mt-1">Please review our terms of partnership</p>
                        </div>
                        <div class="space-y-8">
                            <div class="bg-gray-50 rounded-xl p-8 max-h-[300px] overflow-y-auto border border-gray-100 shadow-inner">
                                <div class="space-y-6 text-sm text-gray-600 leading-relaxed">
                                    <h3 class="font-bold text-gray-900 border-b pb-2 uppercase tracking-widest text-xs">Terms of Service</h3>
                                    <p><strong>1. Merchant Integrity:</strong> You agree to provide high-quality services and genuine parts. FixMart does not tolerate fraudulent activities.</p>
                                    <p><strong>2. Commission structure:</strong> A standard platform fee is deducted from successful bookings. No hidden monthly charges.</p>
                                    <p><strong>3. Warranty policy:</strong> Merchants must honor the stated warranty for all repair jobs booked through our platform.</p>
                                    <p>By proceeding, you agree to comply with all FixMart Merchant Policies and the Data Privacy Act of 2012.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <label class="flex items-center gap-4 p-5 rounded-lg bg-gray-50/50 hover:bg-emerald-50 transition border border-gray-100 hover:border-emerald-200 cursor-pointer group">
                                    <input v-model="form.agreed" type="checkbox" class="w-6 h-6 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                    <span class="text-gray-700 font-bold group-hover:text-emerald-900 transition text-sm">I agree to the FixMart Merchant Terms and Conditions</span>
                                </label>
                                <label class="flex items-center gap-4 p-5 rounded-2xl bg-gray-50/50 hover:bg-emerald-50 transition border border-gray-100 hover:border-emerald-200 cursor-pointer group">
                                    <input v-model="form.certified" type="checkbox" class="w-6 h-6 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                    <span class="text-gray-700 font-bold group-hover:text-emerald-900 transition text-sm">I certify all provided data is true and authenticated</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- Action Buttons Area -->
                <div class="flex flex-wrap items-center justify-between gap-4 mt-12 pt-8 border-t border-gray-100">
                    <button @click="cancelBtn" class="px-10 py-4 rounded-lg font-bold text-gray-400 hover:text-gray-900 hover:bg-gray-100 transition">Cancel Application</button>
                    
                    <div class="flex items-center gap-4 ml-auto">
                        <button v-if="currentStep > 1" @click="backBtn" class="px-10 py-4 rounded-lg border border-gray-200 font-bold text-gray-700 hover:shadow-md transition bg-white active:scale-95">Back</button>
                        
                        <button v-if="currentStep < 5" @click="nextBtn" class="px-14 py-4 rounded-lg bg-emerald-500 font-extrabold text-white hover:bg-emerald-600 shadow-xl shadow-emerald-100 transition transform hover:scale-105 active:scale-95 flex items-center gap-3">
                            <span>Continue</span>
                            <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7M5 12h16"></path></svg>
                        </button>
                        
                        <button v-else @click="submitBtn" :disabled="!form.agreed || !form.certified || isSubmitting" class="px-14 py-4 rounded-lg bg-emerald-500 font-extrabold text-white hover:bg-emerald-600 shadow-xl shadow-emerald-100 transition transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="isSubmitting">Transmitting Data...</span>
                            <span v-else>Complete Registration</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="../../../Public/js/User/form.js?v=<?= filemtime(__DIR__ . '/../../../Public/js/User/form.js') ?>"></script>
</div>
</body>
</html>
