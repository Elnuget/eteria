// Enhanced navbar functionality for responsive design
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced desktop hover behavior for dropdowns
    const hasHover = window.matchMedia('(hover: hover)').matches;
    let hoverTimeout;
    
    // Function to handle desktop hover interactions
    if (hasHover && window.innerWidth > 991.98) {
        document.querySelectorAll('.nav-item.dropdown').forEach(dropdown => {
            const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            
            if (!dropdownToggle || !dropdownMenu) return;
            
            // Show dropdown on hover
            dropdown.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                // Use Bootstrap's Dropdown API to show
                const bootstrapDropdown = new bootstrap.Dropdown(dropdownToggle);
                bootstrapDropdown.show();
            });
            
            // Hide dropdown on mouse leave with delay
            dropdown.addEventListener('mouseleave', function() {
                hoverTimeout = setTimeout(() => {
                    // Use Bootstrap's Dropdown API to hide
                    const bootstrapDropdown = bootstrap.Dropdown.getInstance(dropdownToggle);
                    if (bootstrapDropdown) {
                        bootstrapDropdown.hide();
                    }
                }, 150);
            });
        });
    }    
    // Enhanced keyboard navigation for accessibility
    document.addEventListener('keydown', function(e) {
        // Close all dropdowns on Escape
        if (e.key === 'Escape') {
            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                const dropdown = bootstrap.Dropdown.getInstance(toggle);
                if (dropdown) {
                    dropdown.hide();
                }
            });
        }
    });
    
    // Handle responsive behavior on window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // Close all dropdowns on resize to prevent layout issues
            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                const dropdown = bootstrap.Dropdown.getInstance(toggle);
                if (dropdown) {
                    dropdown.hide();
                }
            });
        }, 250);
    });
    
    // Handle orientation change on mobile devices
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                const dropdown = bootstrap.Dropdown.getInstance(toggle);
                if (dropdown) {
                    dropdown.hide();
                }
            });
        }, 500);
    });
    
    // Enhanced touch support for mobile
    if ('ontouchstart' in window) {
        let touchStartY = 0;
        let scrolling = false;
        
        document.addEventListener('touchstart', function(e) {
            touchStartY = e.touches[0].clientY;
            scrolling = false;
        });
        
        document.addEventListener('touchmove', function(e) {
            const touchY = e.touches[0].clientY;
            const deltaY = Math.abs(touchY - touchStartY);
            
            // If user is scrolling, mark as scrolling
            if (deltaY > 10) {
                scrolling = true;
            }
        });
        
        document.addEventListener('touchend', function(e) {
            // Close dropdowns if user was scrolling
            if (scrolling) {
                document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    const dropdown = bootstrap.Dropdown.getInstance(toggle);
                    if (dropdown) {
                        dropdown.hide();
                    }
                });
            }
        });
    }
});
