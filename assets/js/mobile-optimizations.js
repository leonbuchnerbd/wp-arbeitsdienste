/**
 * Mobile-optimized JavaScript für Arbeitsdienste Plugin
 */

(function() {
    'use strict';
    
    // Mobile detection
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeMobileOptimizations();
        initializeEmailIntegration();
        initializeAccessibility();
        initializePerformanceOptimizations();
    });
    
    /**
     * Mobile-specific optimizations
     */
    function initializeMobileOptimizations() {
        const container = document.querySelector('.arbeitsdienste-container');
        const anmeldeButtons = document.querySelectorAll('.arbeitsdienst-anmelden');
        
        if (!container) return;
        
        // Add mobile class to container
        if (isMobile || isTouch) {
            container.classList.add('mobile-optimized');
            document.body.classList.add('arbeitsdienste-mobile');
        }
        
        // Optimize for touch devices - nur Buttons, nicht ganze Kacheln
        if (isTouch && anmeldeButtons.length) {
            anmeldeButtons.forEach(function(button) {
                button.classList.add('touch-optimized');
                
                // Add haptic feedback simulation für Buttons
                button.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                    this.style.transition = 'transform 0.1s ease';
                }, { passive: true });
                
                button.addEventListener('touchend', function() {
                    const self = this;
                    setTimeout(function() {
                        self.style.transform = '';
                        self.style.transition = 'all 0.2s ease';
                    }, 150);
                }, { passive: true });
                
                button.addEventListener('touchcancel', function() {
                    this.style.transform = '';
                    this.style.transition = 'all 0.2s ease';
                }, { passive: true });
            });
        }
        
        // Handle orientation changes
        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                // Force layout recalculation
                container.style.display = 'none';
                container.offsetHeight; // Trigger reflow
                container.style.display = 'block'; // Simple block layout instead of grid
            }, 100);
        });
        
        // Optimize for viewport changes (mobile browser address bar)
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', vh + 'px');
        
        window.addEventListener('resize', function() {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', vh + 'px');
        });
    }
    
    /**
     * Enhanced email integration for mobile
     */
    function initializeEmailIntegration() {
        // Enhanced email opening function
        window.openEmailClient = function(element) {
            const email = element.getAttribute('data-email');
            const subject = element.getAttribute('data-subject');
            const body = element.getAttribute('data-body');
            
            if (!email) return;
            
            // Properly encode for mailto URL
            const mailtoLink = 'mailto:' + encodeURIComponent(email) + 
                             '?subject=' + encodeURIComponent(subject) + 
                             '&body=' + encodeURIComponent(body);
            
            // Mobile-optimized link opening
            if (isMobile) {
                // For mobile devices, try different approaches
                if (navigator.userAgent.match(/iPhone|iPad/)) {
                    // iOS specific handling
                    window.location.href = mailtoLink;
                } else if (navigator.userAgent.match(/Android/)) {
                    // Android specific handling
                    const link = document.createElement('a');
                    link.href = mailtoLink;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    // Generic mobile
                    window.open(mailtoLink, '_self');
                }
            } else {
                // Desktop
                window.location.href = mailtoLink;
            }
            
            // Provide visual feedback
            element.style.backgroundColor = '#007cba';
            element.style.color = 'white';
            setTimeout(function() {
                element.style.backgroundColor = '';
                element.style.color = '';
            }, 300);
        };
    }
    
    /**
     * Accessibility improvements for mobile
     */
    function initializeAccessibility() {
        const anmeldeButtons = document.querySelectorAll('.arbeitsdienst-anmelden');
        
        anmeldeButtons.forEach(function(button) {
            // Add ARIA attributes
            button.setAttribute('role', 'button');
            button.setAttribute('tabindex', '0');
            
            const kachel = button.closest('.arbeitsdienst-kachel');
            const title = kachel ? kachel.querySelector('h2').textContent : 'Arbeitsdienst';
            button.setAttribute('aria-label', 'Anmeldung für ' + title);
            
            // Keyboard support
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    window.openEmailClient(this);
                }
            });
        });
    }
    
    /**
     * Performance optimizations for mobile
     */
    function initializePerformanceOptimizations() {
        // Lazy loading for images (if any are added later)
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
        
        // Optimize animations for mobile
        if (isMobile) {
            // Reduce animation complexity on mobile
            const style = document.createElement('style');
            style.textContent = `
                .arbeitsdienst-kachel {
                    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
                }
                .arbeitsdienst-anmelden {
                    transition: background-color 0.2s ease !important;
                }
            `;
            document.head.appendChild(style);
        }
        
        // Debounce resize events
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Simple responsive handling for list layout
                const container = document.querySelector('.arbeitsdienste-container');
                if (container) {
                    // No specific grid adjustments needed for simple design
                    // Just ensure proper spacing on mobile
                    if (window.innerWidth <= 768) {
                        container.style.padding = '0 10px';
                    } else {
                        container.style.padding = '0 15px';
                    }
                }
            }, 250);
        });
    }
    
    /**
     * PWA support
     */
    function initializePWASupport() {
        // Add to homescreen prompt handling
        let deferredPrompt;
        
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button if desired
            const installButton = document.querySelector('.install-app-button');
            if (installButton) {
                installButton.style.display = 'block';
                installButton.addEventListener('click', function() {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function(choiceResult) {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                        }
                        deferredPrompt = null;
                    });
                });
            }
        });
    }
    
    // Initialize PWA support if needed
    if ('serviceWorker' in navigator) {
        initializePWASupport();
    }
    
})();
