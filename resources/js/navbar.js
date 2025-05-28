// Enhanced navbar functionality for responsive design
document.addEventListener('DOMContentLoaded', function() {
    let closeTimeout;
    const CLOSE_DELAY = 300;
    const isMobile = window.innerWidth <= 991.98;
    
    // Detect if device supports hover
    const hasHover = window.matchMedia('(hover: hover)').matches;
    
    // Function to close all dropdowns
    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
        document.querySelectorAll('.menu-dropdown').forEach(button => {
            button.setAttribute('aria-expanded', 'false');
        });
    }

    // Function to close dropdown with delay (for hover interactions)
    function closeDropdownWithDelay(dropdownMenu, button) {
        if (!hasHover || window.innerWidth <= 991.98) return; // Skip delay on mobile
        
        clearTimeout(closeTimeout);
        closeTimeout = setTimeout(() => {
            if (!dropdownMenu.matches(':hover') && !button.matches(':hover')) {
                dropdownMenu.classList.remove('show');
                button.setAttribute('aria-expanded', 'false');
            }
        }, CLOSE_DELAY);
    }

    // Handle menu dropdown clicks
    document.querySelectorAll('.menu-dropdown').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdownMenu = this.nextElementSibling;
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // On mobile, close other dropdowns
            if (window.innerWidth <= 991.98) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                    }
                });
                document.querySelectorAll('.menu-dropdown').forEach(btn => {
                    if (btn !== this) {
                        btn.setAttribute('aria-expanded', 'false');
                    }
                });
            }
            
            // Toggle current dropdown
            dropdownMenu.classList.toggle('show');
            this.setAttribute('aria-expanded', !isExpanded);
            
            // Add accessibility features
            if (!isExpanded) {
                // Focus first item when opening
                const firstItem = dropdownMenu.querySelector('.dropdown-item');
                if (firstItem) {
                    setTimeout(() => firstItem.focus(), 100);
                }
            }
        });

        // Handle keyboard navigation
        button.addEventListener('keydown', function(e) {
            const dropdownMenu = this.nextElementSibling;
            
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!dropdownMenu.classList.contains('show')) {
                    this.click();
                } else {
                    const firstItem = dropdownMenu.querySelector('.dropdown-item');
                    if (firstItem) firstItem.focus();
                }
            }
        });
    });

    // Handle dropdown item keyboard navigation
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('keydown', function(e) {
            const dropdown = this.closest('.dropdown-menu');
            const items = dropdown.querySelectorAll('.dropdown-item');
            const currentIndex = Array.from(items).indexOf(this);
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    const nextIndex = (currentIndex + 1) % items.length;
                    items[nextIndex].focus();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    const prevIndex = currentIndex === 0 ? items.length - 1 : currentIndex - 1;
                    items[prevIndex].focus();
                    break;
                case 'Escape':
                    e.preventDefault();
                    const button = dropdown.previousElementSibling;
                    dropdown.classList.remove('show');
                    button.setAttribute('aria-expanded', 'false');
                    button.focus();
                    break;
                case 'Tab':
                    // Allow normal tab behavior to close dropdown
                    setTimeout(() => {
                        if (!dropdown.contains(document.activeElement)) {
                            dropdown.classList.remove('show');
                            dropdown.previousElementSibling.setAttribute('aria-expanded', 'false');
                        }
                    }, 10);
                    break;
            }
        });
    });

    // Handle hover interactions on desktop
    if (hasHover && window.innerWidth > 991.98) {
        document.querySelectorAll('.menu-section').forEach(section => {
            section.addEventListener('mouseenter', function() {
                clearTimeout(closeTimeout);
                const dropdownMenu = this.querySelector('.dropdown-menu');
                const button = this.querySelector('.menu-dropdown');
                
                if (!dropdownMenu || !button) return;
                
                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                    }
                });
                document.querySelectorAll('.menu-dropdown').forEach(btn => {
                    if (btn !== button) {
                        btn.setAttribute('aria-expanded', 'false');
                    }
                });
                
                // Open current dropdown
                dropdownMenu.classList.add('show');
                button.setAttribute('aria-expanded', 'true');
            });

            section.addEventListener('mouseleave', function() {
                const dropdownMenu = this.querySelector('.dropdown-menu');
                const button = this.querySelector('.menu-dropdown');
                if (dropdownMenu && button) {
                    closeDropdownWithDelay(dropdownMenu, button);
                }
            });
        });

        // Keep dropdown open when hovering over menu items
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.addEventListener('mouseenter', function() {
                clearTimeout(closeTimeout);
            });

            menu.addEventListener('mouseleave', function() {
                const button = this.previousElementSibling;
                closeDropdownWithDelay(this, button);
            });
        });
    }

    // Handle navbar collapse on mobile
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            // Close all dropdowns when toggling navbar
            closeAllDropdowns();
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown') && !e.target.closest('.menu-section')) {
            closeAllDropdowns();
        }
    });

    // Close dropdowns on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
            // Return focus to the first menu button
            const firstMenuButton = document.querySelector('.menu-dropdown');
            if (firstMenuButton) firstMenuButton.focus();
        }
    });

    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // Close all dropdowns on resize to prevent layout issues
            closeAllDropdowns();
            
            // Update mobile detection
            const newIsMobile = window.innerWidth <= 991.98;
            if (newIsMobile !== isMobile) {
                location.reload(); // Reload to reset JavaScript behavior
            }
        }, 250);
    });

    // Prevent dropdown item clicks from propagating
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // Allow the click to proceed but close the dropdown after navigation
            setTimeout(() => {
                closeAllDropdowns();
            }, 100);
        });
    });

    // Handle orientation change on mobile devices
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            closeAllDropdowns();
        }, 500);
    });

    // Add touch support for better mobile experience
    if ('ontouchstart' in window) {
        let touchStartY = 0;
        
        document.addEventListener('touchstart', function(e) {
            touchStartY = e.touches[0].clientY;
        });
        
        document.addEventListener('touchmove', function(e) {
            const touchY = e.touches[0].clientY;
            const deltaY = touchY - touchStartY;
            
            // If user is scrolling vertically, close dropdowns
            if (Math.abs(deltaY) > 10) {
                closeAllDropdowns();
            }
        });
    }

    // Initialize accessibility attributes
    document.querySelectorAll('.menu-dropdown').forEach(button => {
        button.setAttribute('aria-haspopup', 'true');
        button.setAttribute('aria-expanded', 'false');
    });

    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.setAttribute('role', 'menu');
    });

    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.setAttribute('role', 'menuitem');
    });
});
