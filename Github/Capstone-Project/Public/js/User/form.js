const { createApp } = Vue;

createApp({
    data() {
        return {
            currentStep: 1,
            isSubmitting: false,
            showPassword: false, // Added for password visibility toggle
            appStatus: null,
            rejectionReason: null,
            steps: [
                { title: 'Owner Information', desc: 'Verify business owner identity' },
                { title: 'Business Information', desc: 'Shop details and contact' },
                { title: 'Products / Services', desc: 'Offer details and pricing' },
                { title: 'Business Legitimacy', desc: 'Documents and verification' },
                { title: 'Platform Agreement', desc: 'Review and confirm' }
            ],
            form: {
                first_name: '', last_name: '', email: '', phone: '', gov_id: '', id_type: '',
                username: '', password: '',
                business_name: '', business_type: '', business_form: '', business_email: '', business_phone: '',
                business_address: '', offer_details: '', tin_number: '',
                service_area: '', avg_pricing: '',
                agreed: false, certified: false
            },
            files: {
                gov_id_file: null,
                business_permit: null,
                dti_registration: null
            },
            // Business Form Types
            businessForms: [
                { value: 'sole_proprietorship', label: 'Sole Proprietorship', desc: 'Single owner, DTI registered', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
                { value: 'partnership', label: 'Partnership', desc: 'Shared ownership, SEC registered', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
                { value: 'corporation', label: 'Corporation', desc: 'Incorporated entity, SEC registered', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
                { value: 'cooperative', label: 'Cooperative', desc: 'Member-owned, CDA registered', icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9' }
            ],
            // Defensive properties for navbar compatibility
            user: { name: '', image: '' },
            notifications: 0,
            unreadCount: 0,
            showProfileMenu: false
        }
    },
    computed: {
        parsedRejectionReasons() {
            if (!this.rejectionReason) return [];
            // Format: "Reason 1; Reason 2 | Note: extra text"
            const parts = this.rejectionReason.split(' | ');
            const reasonPart = parts[0] || '';
            return reasonPart.split('; ').map(r => r.trim()).filter(Boolean);
        },
        rejectionNote() {
            if (!this.rejectionReason) return '';
            const match = this.rejectionReason.match(/\| Note: (.+)$/);
            return match ? match[1].trim() : '';
        },
        isValidEmail() {
            if (!this.form.email) return true;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(this.form.email);
        },
        isValidBusinessEmail() {
            if (!this.form.business_email) return true;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(this.form.business_email);
        }
    },
    watch: {
        'form.phone'(newVal) {
            this.form.phone = String(newVal).replace(/\D/g, '');
        },
        'form.gov_id'(newVal) {
            this.form.gov_id = String(newVal).replace(/\D/g, '');
        },
        'form.business_phone'(newVal) {
            this.form.business_phone = String(newVal).replace(/\D/g, '');
        },
        'form.avg_pricing'(newVal) {
            this.form.avg_pricing = String(newVal).replace(/\D/g, '');
        }
    },
    async mounted() {
        if (typeof IS_LOGGED_IN !== 'undefined' && IS_LOGGED_IN) {
            try {
                const res = await fetch(`${BASE_URL}Controller/business-controller.php?action=my_status`);
                const data = await res.json();
                if (data.status === 'success' && data.data) {
                    this.appStatus = data.data.status;
                    this.rejectionReason = data.data.rejection_reason || null;
                }
            } catch (e) { /* silently ignore */ }
        }
    },
    methods: {
        validateStep(step) {
            const f = this.form;
            if (step === 1) {
                if (!f.first_name || !f.last_name || !f.email || !f.phone || !f.id_type || !f.gov_id || !this.files.gov_id_file) {
                    Swal.fire('Required Fields', 'Please complete all owner information and upload your ID.', 'warning');
                    return false;
                }
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(f.email)) {
                    Swal.fire('Invalid Email Format', 'Please enter a valid email address containing "@" and domain.', 'warning');
                    return false;
                }
                const phoneRegex = /^[0-9]{11}$/;
                if (!phoneRegex.test(f.phone)) {
                    Swal.fire('Invalid Mobile Number', 'Mobile number must be exactly 11 digits (numbers only).', 'warning');
                    return false;
                }
                const numRegex = /^[0-9]+$/;
                if (!numRegex.test(f.gov_id)) {
                    Swal.fire('Invalid ID Number', 'ID Number must contain only numbers.', 'warning');
                    return false;
                }
                // Guest verification
                if (!IS_LOGGED_IN) {
                    if (!f.username || !f.password) {
                        Swal.fire('Account Required', 'Please choose a username and password for your new shop account.', 'warning');
                        return false;
                    }
                    if (f.username.length < 3) {
                        Swal.fire('Invalid Username', 'Username must be at least 3 characters.', 'warning');
                        return false;
                    }
                    if (f.password.length < 8) {
                        Swal.fire('Weak Password', 'Password must be at least 8 characters.', 'warning');
                        return false;
                    }
                }
            }
            if (step === 2) {
                if (!f.business_form) {
                    Swal.fire('Business Form Required', 'Please select your business form type (e.g., Sole Proprietorship, Partnership, etc.).', 'warning');
                    return false;
                }
                if (!f.business_name || !f.business_type || !f.business_email || !f.business_phone || !f.business_address || !f.tin_number) {
                    Swal.fire('Required Fields', 'Please provide complete business details and TIN.', 'warning');
                    return false;
                }
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(f.business_email)) {
                    Swal.fire('Invalid Email Format', 'Please enter a valid business email address containing "@" and domain.', 'warning');
                    return false;
                }
                const phoneRegex = /^[0-9]{11}$/;
                if (!phoneRegex.test(f.business_phone)) {
                    Swal.fire('Invalid Contact Number', 'Business contact number must be exactly 11 digits (numbers only).', 'warning');
                    return false;
                }
                // Allow formats like 000-000-000-000
                const tinRegex = /^[0-9-]{11,15}$/;
                if (!tinRegex.test(f.tin_number)) {
                    Swal.fire('Invalid TIN Number', 'TIN Number must be properly formatted (numbers only).', 'warning');
                    return false;
                }
            }
            if (step === 3) {
                if (!f.offer_details || !f.service_area || !f.avg_pricing) {
                    Swal.fire('Required Fields', 'Please describe your services and pricing.', 'warning');
                    return false;
                }
                const numRegex = /^[0-9]+$/;
                if (!numRegex.test(f.avg_pricing)) {
                    Swal.fire('Invalid Pricing', 'Average pricing must be a valid number.', 'warning');
                    return false;
                }
            }
            if (step === 4) {
                if (!this.files.business_permit || !this.files.dti_registration) {
                    Swal.fire('Required Documents', 'Please upload your business permit and DTI/SEC registration.', 'warning');
                    return false;
                }
            }
            return true;
        },
        async validateStepAsync(step) {
            // Synchronous validation first
            if (!this.validateStep(step)) return false;
            // Async confirmation for Step 4 (document verification)
            if (step === 4) {
                const result = await Swal.fire({
                    title: 'Document Verification Confirmation',
                    html: '<div style="text-align:left;font-size:14px;line-height:1.6;">' +
                          '<p>By proceeding, you confirm that:</p>' +
                          '<ul style="margin-top:8px;padding-left:20px;">' +
                          '<li>✅ All uploaded documents are <strong>genuine and unaltered</strong></li>' +
                          '<li>✅ You can present the <strong>original physical copies</strong> for verification if requested by FixMart</li>' +
                          '<li>✅ You understand that submitting fraudulent documents may result in <strong>permanent account ban</strong></li>' +
                          '</ul></div>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'I Confirm — Proceed',
                    cancelButtonText: 'Go Back',
                    customClass: { popup: 'rounded-2xl' }
                });
                return result.isConfirmed;
            }
            return true;
        },
        async nextBtn() {
            // Use async validation for Step 4 (document confirmation)
            if (this.currentStep === 4) {
                const ok = await this.validateStepAsync(4);
                if (ok && this.currentStep < 5) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } else {
                if (this.validateStep(this.currentStep)) {
                    if (this.currentStep < 5) {
                        this.currentStep++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            }
        },
        backBtn() {
            if (this.currentStep > 1) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        cancelBtn() {
            Swal.fire({
                title: 'Cancel registration?',
                text: 'Your progress will be lost.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `${BASE_URL}View/User/Home/index.php`;
                }
            });
        },
        onFileChange(event, field) {
            const file = event.target.files[0];
            if (file) {
                this.files[field] = file;
            }
        },
        formatTIN() {
            // Remove all non-digit characters
            let val = this.form.tin_number.replace(/\D/g, '');
            // Limit to 12 digits
            val = val.substring(0, 12);
            // Add hyphens every 3 digits
            if (val.length > 0) {
                this.form.tin_number = val.match(/.{1,3}/g).join('-');
            } else {
                this.form.tin_number = '';
            }
        },
        async submitBtn() {
            if (!this.form.agreed || !this.form.certified) {
                Swal.fire('Incomplete', 'Please agree to the terms and certify your information.', 'warning');
                return;
            }

            this.isSubmitting = true;

            const formData = new FormData();

            for (const key in this.form) {
                formData.append(key, this.form[key]);
            }

            if (this.files.gov_id_file) formData.append('gov_id_file', this.files.gov_id_file);
            if (this.files.business_permit) formData.append('business_permit', this.files.business_permit);
            if (this.files.dti_registration) formData.append('dti_registration', this.files.dti_registration);

            try {
                const response = await fetch(`${BASE_URL}Controller/business-controller.php?action=submit`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your application has been submitted successfully and is pending review.',
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = `${BASE_URL}View/Owner/Dashboard/dashboard.php`;
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to submit application.', 'error');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                Swal.fire('Error', 'A server error occurred. Please try again later.', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        toggleProfileMenu() {
            this.showProfileMenu = !this.showProfileMenu;
        },
        handleImageError(e) {
            e.target.src = 'https://ui-avatars.com/api/?name=User';
        },
        onlyNumbers(field) {
            this.form[field] = String(this.form[field]).replace(/\D/g, '');
        },
        removeFile(field) {
            this.files[field] = null;
            if (field === 'gov_id_file' && this.$refs.govIdInput) this.$refs.govIdInput.value = '';
            if (field === 'business_permit' && this.$refs.permitInput) this.$refs.permitInput.value = '';
            if (field === 'dti_registration' && this.$refs.dtiInput) this.$refs.dtiInput.value = '';
        }
    }
}).mount('#app');