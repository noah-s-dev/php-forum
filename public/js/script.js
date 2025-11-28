// PHP Forum JavaScript Functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeAnimations();
    initializeFormValidation();
    initializeSearchEnhancements();
    initializeTooltips();
    initializeAutoResize();
    initializeLoadingStates();
    initializeLazyLoading();
});

// Add fade-in animations to cards
function initializeAnimations() {
    const cards = document.querySelectorAll('.card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    cards.forEach(card => {
        observer.observe(card);
    });
}

// Enhanced form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            // Real-time validation
            input.addEventListener('input', function() {
                validateField(this);
            });
            
            // Validation on blur
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
        
        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fix the errors before submitting.', 'error');
            } else {
                // Add loading state to submit button
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Restore button after 5 seconds (fallback)
                    setTimeout(() => {
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    }, 5000);
                }
            }
        });
    });
}

// Field validation function
function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    const minLength = field.getAttribute('minlength');
    const maxLength = field.getAttribute('maxlength');
    const pattern = field.getAttribute('pattern');
    
    // Remove existing validation classes
    field.classList.remove('is-valid', 'is-invalid');
    
    // Check if field is required and empty
    if (required && !value) {
        field.classList.add('is-invalid');
        return false;
    }
    
    // Check minimum length
    if (minLength && value.length < parseInt(minLength)) {
        field.classList.add('is-invalid');
        return false;
    }
    
    // Check maximum length
    if (maxLength && value.length > parseInt(maxLength)) {
        field.classList.add('is-invalid');
        return false;
    }
    
    // Check pattern
    if (pattern && value && !new RegExp(pattern).test(value)) {
        field.classList.add('is-invalid');
        return false;
    }
    
    // Email validation
    if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            field.classList.add('is-invalid');
            return false;
        }
    }
    
    // Password confirmation
    if (field.name === 'confirm_password') {
        const passwordField = document.querySelector('input[name="new_password"], input[name="password"]');
        if (passwordField && value !== passwordField.value) {
            field.classList.add('is-invalid');
            return false;
        }
    }
    
    // If we get here, field is valid
    if (value) {
        field.classList.add('is-valid');
    }
    return true;
}

// Enhanced search functionality
function initializeSearchEnhancements() {
    const searchInput = document.querySelector('input[name="search"]');
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        
        // Add loading indicator
        this.classList.add('loading');
        
        searchTimeout = setTimeout(() => {
            this.classList.remove('loading');
            // Auto-submit search after 1 second of no typing
            if (this.value.length >= 3 || this.value.length === 0) {
                this.form.submit();
            }
        }, 1000);
    });
    
    // Search suggestions (if implemented)
    searchInput.addEventListener('focus', function() {
        // Could implement search suggestions here
    });
}

// Initialize Bootstrap tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Auto-resize textareas
function initializeAutoResize() {
    const textareas = document.querySelectorAll('textarea');
    
    textareas.forEach(textarea => {
        // Set initial height
        autoResize(textarea);
        
        // Resize on input
        textarea.addEventListener('input', function() {
            autoResize(this);
        });
    });
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// Loading states for buttons and forms
function initializeLoadingStates() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit' || this.classList.contains('loading-trigger')) {
                this.classList.add('loading');
                
                // Remove loading state after 3 seconds (fallback)
                setTimeout(() => {
                    this.classList.remove('loading');
                }, 3000);
            }
        });
    });
}

// Lazy loading for images (if any)
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    }
}

// Notification system
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after duration
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Back to top button
function createBackToTopButton() {
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '<i class="bi bi-arrow-up"></i>';
    backToTop.className = 'btn btn-primary position-fixed';
    backToTop.style.cssText = 'bottom: 20px; right: 20px; z-index: 9999; border-radius: 50%; width: 50px; height: 50px; display: none;';
    backToTop.title = 'Back to top';
    
    document.body.appendChild(backToTop);
    
    // Show/hide based on scroll position
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.style.display = 'block';
        } else {
            backToTop.style.display = 'none';
        }
    });
    
    // Scroll to top on click
    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Initialize back to top button
createBackToTopButton();

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to submit forms
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const activeElement = document.activeElement;
        if (activeElement.tagName === 'TEXTAREA') {
            const form = activeElement.closest('form');
            if (form) {
                form.submit();
            }
        }
    }
    
    // Escape to close modals or cancel actions
    if (e.key === 'Escape') {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        });
    }
});

// Enhanced table interactions
document.querySelectorAll('.table-hover tbody tr').forEach(row => {
    row.addEventListener('click', function() {
        const link = this.querySelector('a');
        if (link && !window.getSelection().toString()) {
            window.location.href = link.href;
        }
    });
});

// Form auto-save (for longer forms)
function initializeAutoSave() {
    const forms = document.querySelectorAll('form[data-autosave]');
    
    forms.forEach(form => {
        const formId = form.id || 'form_' + Date.now();
        const inputs = form.querySelectorAll('input, textarea, select');
        
        // Load saved data
        inputs.forEach(input => {
            const savedValue = localStorage.getItem(`autosave_${formId}_${input.name}`);
            if (savedValue && !input.value) {
                input.value = savedValue;
            }
        });
        
        // Save data on input
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                localStorage.setItem(`autosave_${formId}_${this.name}`, this.value);
            });
        });
        
        // Clear saved data on successful submit
        form.addEventListener('submit', function() {
            inputs.forEach(input => {
                localStorage.removeItem(`autosave_${formId}_${input.name}`);
            });
        });
    });
}

// Initialize auto-save
initializeAutoSave();

// Performance monitoring
if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(() => {
            const perfData = performance.getEntriesByType('navigation')[0];
            if (perfData.loadEventEnd - perfData.loadEventStart > 3000) {
                console.warn('Page load time is slow:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
            }
        }, 0);
    });
}

