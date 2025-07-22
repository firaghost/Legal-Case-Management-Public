document.addEventListener('alpine:init', () => {
    Alpine.data('caseForm', () => ({
        type: '',
        role: document.body.getAttribute('data-user-role') || 'lawyer',
        branches: [],
        workUnits: [],
        lawyers: [],
        loading: false,
        errors: {},
        // Track current step for multi-step criminal form
        currentCriminalStep: 0,
        // Track step for secured loan recovery flow
        recoveryStep: 0,
        // Array proxy for template bindings
        defendants: [{ name: '', contact: '' }],
        form: {
            type: '',
            plaintiffs: [{ name: '', contact: '' }],
            defendants: [{ name: '', contact: '' }],
            file_number: '',
            claimed_amount: '',
        },
        async init() {
            try {
                this.loading = true;
                await Promise.all([
                    this.fetchBranches(),
                    this.fetchWorkUnits(),
                ]);
                const userId = document.body.getAttribute('data-user-id');
                if (this.role === 'lawyer' && userId) {
                    this.form.lawyer_id = userId;
                }
                // Keep top-level defendants array in sync with form.defendants
                this.defendants = this.form.defendants;
                this.generateCompanyFileNumber();
                this.setupValidation();
                this.initializeFormData();
            } catch (error) {
                console.error('Error initializing form:', error);
                this.showError('Failed to initialize form. Please refresh the page.');
            } finally {
                this.loading = false;
            }
        },
        async fetchBranches() {
            try {
                const response = await fetch('/branches');
                if (!response.ok) throw new Error('Failed to fetch branches');
                this.branches = await response.json();
            } catch (error) {
                console.error('Error fetching branches:', error);
                this.showError('Failed to load branches');
            }
        },
        async fetchWorkUnits() {
            try {
                const response = await fetch('/work-units');
                if (!response.ok) throw new Error('Failed to fetch work units');
                this.workUnits = await response.json();
            } catch (error) {
                console.error('Error fetching work units:', error);
                this.showError('Failed to load work units');
            }
        },
        generateCompanyFileNumber() {
            if (!this.form.company_file_number) {
                const prefix = this.getCaseTypePrefix();
                const date = new Date();
                const year = date.getFullYear().toString().slice(-2);
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const random = Math.floor(1000 + Math.random() * 9000);
                this.form.company_file_number = `${prefix}-LO-${year}${month}${random}`;
            }
        },
        getCaseTypePrefix() {
            switch (this.form.type) {
                case 'clean_loan': return 'CLN';
                case 'secured_loan': return 'SCN';
                case 'labor': return 'LBR';
                case 'civil': return 'CIV';
                case 'criminal': return 'CRM';
                case 'advisory': return 'ADV';
                default: return 'GEN';
            }
        },
        initializeFormData() {
            const oldInput = JSON.parse(document.getElementById('old-input')?.textContent || '{}');
            if (Object.keys(oldInput).length > 0) {
                this.form = { ...this.form, ...oldInput };
                this.type = oldInput.type || this.type;
                const validationErrors = JSON.parse(document.getElementById('validation-errors')?.textContent || '{}');
                if (Object.keys(validationErrors).length > 0) {
                    this.errors = validationErrors;
                    this.scrollToFirstError();
                }
            }
        },
        setupValidation() {
            const form = this.$el.closest('form');
            if (!form) return;
            form.addEventListener('submit', (e) => {
                if (!this.validateForm()) {
                    e.preventDefault();
                    this.scrollToFirstError();
                }
            });
        },
        validateForm() {
            this.errors = {};
            if (!this.form.plaintiff?.trim()) this.addError('plaintiff', 'Plaintiff is required');
            if (!this.form.defendant?.trim()) this.addError('defendant', 'Defendant is required');
            if (!this.form.file_number?.trim()) this.addError('file_number', 'File number is required');
            if (this.type === 'clean_loan') {
                if (!this.form.branch_id) this.addError('branch_id', 'Branch is required');
                if (!this.form.court_file_number?.trim()) this.addError('court_file_number', 'Court file number is required');
            }
            return Object.keys(this.errors).length === 0;
        },
        addError(field, message) {
            if (!this.errors[field]) {
                this.errors[field] = [];
            }
            this.errors[field].push(message);
        },
        clearError(field) {
            if (this.errors[field]) {
                delete this.errors[field];
            }
        },
        scrollToFirstError() {
            this.$nextTick(() => {
                const firstError = this.$el.querySelector('[x-text^="errors."], [x-text*=" errors."]');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const input = firstError.closest('.form-group')?.querySelector('input, select, textarea');
                    if (input) {
                        input.focus({ preventScroll: true });
                    }
                }
            });
        },
        showError(message) {
            console.error(message);
        },
        handleFileUpload(field, event) {
            // ...
        },
        addArrayItem(arrayName, value = '') {
            // ...
        },
        removeArrayItem(arrayName, index) {
            // ...
        },
        destroy() {
            // ...
        },
        nextStep() {
            // ...
        },
        prevStep() {
            // ...
        },
        validateStep() {
            // ...
        },
        // Criminal form step navigation
        nextCriminalStep() {
            if (this.currentCriminalStep < 3) {
                this.currentCriminalStep++;
            }
        },
        previousCriminalStep() {
            if (this.currentCriminalStep > 0) {
                this.currentCriminalStep--;
            }
        },
// Secured-loan navigation
nextRecoveryStep() {
    if (this.recoveryStep < 3) this.recoveryStep++;
},
previousRecoveryStep() {
    if (this.recoveryStep > 0) this.recoveryStep--;
},

// Progress bar percentage (for civil / secured-loan forms)
get progress() {
    return (this.recoveryStep / 3) * 100;
},

// Outstanding balance formatted for secured loan recovery
get outstandingBalanceFormatted() {
    const outstanding = parseFloat(this.form.outstanding_amount || 0);
    const recovered = parseFloat(this.form.amount_recovered || 0);
    const balance = Math.max(outstanding - recovered, 0);
    return balance.toLocaleString(undefined, { style: 'currency', currency: 'USD' });
},

// Human-readable title
caseTypeTitle() {
    switch (this.type) {
        case 'clean_loan':    return 'Clean Loan Recovery';
        case 'secured_loan':  return 'Secured Loan Recovery';
        case 'labor':         return 'Labor Litigation';
        case 'civil':         return 'Other Civil Litigation';
        case 'criminal':      return 'Criminal Litigation';
        case 'advisory':      return 'Legal Advisory & Document Review';
        default:              return 'Case Information';
    }
},

        toggleTab(tab) {
            // ...
        },
        handleDocumentUpload(event) {
            // ...
        },
        removeDocument(index) {
            // ...
        },
        formatFileSize(bytes) {
            // ...
        },
    }));
}); 