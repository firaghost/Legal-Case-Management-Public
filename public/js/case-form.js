document.addEventListener('alpine:init', () => {
    // Base case form component
    Alpine.data('caseForm', () => ({
        // Form state
        type: '',
        role: document.body.getAttribute('data-user-role') || 'lawyer',
        branches: [],
        workUnits: [],
        lawyers: [],
        loading: false,
        errors: {},
        
        // Form data structure
        form: {
            // Common fields
            type: '',
            plaintiff: '',
            defendant: '',
            file_number: '',
            claimed_amount: '',
            
            // Case type specific fields will be merged in init()
        },
        
        // Initialize the form
        async init() {
            // Load initial data
            try {
                this.loading = true;
                await Promise.all([
                    this.fetchBranches(),
                    this.fetchWorkUnits(),
                    this.fetchLawyers()
                ]);
                
                // Set default values based on user role
                const userId = document.body.getAttribute('data-user-id');
                if (this.role === 'lawyer' && userId) {
                    this.form.lawyer_id = userId;
                }
                
                // Generate any default values
                this.generateCompanyFileNumber();
                
                // Set up form validation
                this.setupValidation();
                
                // Initialize any existing form data from server-side validation
                this.initializeFormData();
                
            } catch (error) {
                console.error('Error initializing form:', error);
                this.showError('Failed to initialize form. Please refresh the page.');
            } finally {
                this.loading = false;
            }
        },
        
        // Data fetching methods
        async fetchBranches() {
            try {
                const response = await fetch('/api/branches');
                if (!response.ok) throw new Error('Failed to fetch branches');
                this.branches = await response.json();
            } catch (error) {
                console.error('Error fetching branches:', error);
                this.showError('Failed to load branches');
            }
        },
        
        async fetchWorkUnits() {
            try {
                const response = await fetch('/api/work-units');
                if (!response.ok) throw new Error('Failed to fetch work units');
                this.workUnits = await response.json();
            } catch (error) {
                console.error('Error fetching work units:', error);
                this.showError('Failed to load work units');
            }
        },
        
        async fetchLawyers() {
            try {
                const response = await fetch('/api/lawyers');
                if (!response.ok) throw new Error('Failed to fetch lawyers');
                this.lawyers = await response.json();
            } catch (error) {
                console.error('Error fetching lawyers:', error);
                this.showError('Failed to load lawyers');
            }
        },
        
        // Form helpers
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
            // Try to get abbreviation from the selected option in the dropdown
            const select = document.getElementById('case_type');
            if (select) {
                const selected = select.options[select.selectedIndex];
                const abbr = selected.getAttribute('data-abbreviation');
                if (abbr) return abbr;
            }
            return 'GEN';
        },
        
        // Initialize form data from server-side validation
        initializeFormData() {
            // Get any old input from Laravel's validation
            const oldInput = JSON.parse(document.getElementById('old-input')?.textContent || '{}');
            
            // Merge old input with current form data
            if (Object.keys(oldInput).length > 0) {
                this.form = { ...this.form, ...oldInput };
                this.type = oldInput.type || this.type;
                
                // Show error messages if any
                const validationErrors = JSON.parse(document.getElementById('validation-errors')?.textContent || '{}');
                if (Object.keys(validationErrors).length > 0) {
                    this.errors = validationErrors;
                    this.scrollToFirstError();
                }
            }
        },
        
        // Validation
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
            
            // Common validations
            if (!this.form.plaintiff?.trim()) this.addError('plaintiff', 'Plaintiff is required');
            if (!this.form.defendant?.trim()) this.addError('defendant', 'Defendant is required');
            if (!this.form.file_number?.trim()) this.addError('file_number', 'File number is required');
            
            // Type-specific validations
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
        
        // UI Helpers
        showError(message) {
            // You can implement a toast notification here
            console.error(message);
            // Example: this.$dispatch('notify', { type: 'error', message });
        },
        
        // File handling
        handleFileUpload(field, event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file type and size
            const validTypes = [
                'application/pdf', 
                'image/jpeg', 
                'image/png', 
                'application/msword', 
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (!validTypes.includes(file.type)) {
                this.addError(field, 'Invalid file type. Please upload PDF, JPG, PNG, or Word documents.');
                return;
            }
            
            if (file.size > maxSize) {
                this.addError(field, 'File is too large. Maximum size is 10MB.');
                return;
            }
            
            // Handle the file (e.g., preview, upload to server, etc.)
            // This is a simplified example - you'd typically handle the actual upload in your controller
            this.form[field] = file;
            
            // Clear any previous errors for this field
            this.clearError(field);
            
            // Update file input label
            const label = event.target.nextElementSibling;
            if (label && label.classList.contains('file-upload-label')) {
                label.textContent = file.name;
            }
        },
        
        // Array field helpers
        addArrayItem(arrayName, value = '') {
            if (!this.form[arrayName]) {
                this.form[arrayName] = [];
            }
            this.form[arrayName].push(value);
        },
        
        removeArrayItem(arrayName, index) {
            if (this.form[arrayName] && this.form[arrayName].length > index) {
                this.form[arrayName].splice(index, 1);
            }
        },
        
        // Clean up when component is destroyed
        destroy() {
            // Clean up any event listeners if needed
        }
    }));
    
    // Clean Loan Form Component
    Alpine.data('cleanLoanForm', () => ({
        init() {
            // Initialize clean loan form specific logic
        },
        // Add clean loan specific methods here
    }));
    
    // Secured Loan Form Component
    Alpine.data('securedLoanForm', () => ({
        currentStep: 0,
        steps: ['Filing', 'Hearing', 'Judgment', 'Execution', 'Closure'],
        
        nextStep() {
            if (this.validateStep()) {
                this.currentStep++;
            }
        },
        
        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
            }
        },
        
        validateStep() {
            // Add step validation logic here
            return true;
        }
    }));
    
    // Criminal Form Component
    Alpine.data('criminalForm', () => ({
        currentStep: 0,
        steps: ['Police', 'Prosecutor', 'Court'],
        
        nextStep() {
            if (this.validateStep()) {
                this.currentStep++;
            }
        },
        
        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
            }
        },
        
        validateStep() {
            // Add step validation logic here
            return true;
        }
    }));
    
    // Initialize other case type components
    Alpine.data('laborForm', () => ({}));
    Alpine.data('civilForm', () => ({}));
    
    // Advisory Form Component
    Alpine.data('advisoryForm', () => ({
        tab: 'advice',
        toggleTab(tab) {
            this.tab = tab;
        },
        
        // Add advisory specific methods here
        handleDocumentUpload(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;
            
            // Process multiple files
            if (!this.form.documents) {
                this.form.documents = [];
            }
            
            files.forEach(file => {
                this.form.documents.push({
                    file,
                    name: file.name,
                    size: this.formatFileSize(file.size),
                    type: file.type
                });
            });
            
            // Reset file input
            event.target.value = '';
        },
        
        removeDocument(index) {
            this.form.documents.splice(index, 1);
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }));
});

// Initialize form when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Add any initialization code here if needed
});
