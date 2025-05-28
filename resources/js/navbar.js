// Navbar functionality
document.addEventListener('DOMContentLoaded', function() {
    let closeTimeout;
    const CLOSE_DELAY = 300; // 300ms antes de cerrar

    // Función para cerrar todos los dropdowns
    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
        document.querySelectorAll('.menu-dropdown').forEach(button => {
            button.setAttribute('aria-expanded', 'false');
        });
    }

    // Función para cerrar un dropdown específico con delay
    function closeDropdownWithDelay(dropdownMenu, button) {
        clearTimeout(closeTimeout);
        closeTimeout = setTimeout(() => {
            if (!dropdownMenu.matches(':hover') && !button.matches(':hover')) {
                dropdownMenu.classList.remove('show');
                button.setAttribute('aria-expanded', 'false');
            }
        }, CLOSE_DELAY);
    }

    // Manejar clic en los botones del menú
    document.querySelectorAll('.menu-dropdown').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdownMenu = this.nextElementSibling;
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Cerrar otros dropdowns
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
            
            // Toggle del dropdown actual
            dropdownMenu.classList.toggle('show');
            this.setAttribute('aria-expanded', !isExpanded);
        });
    });

    // Manejar hover en dispositivos no táctiles
    if (window.matchMedia('(hover: hover)').matches) {
        document.querySelectorAll('.menu-section').forEach(section => {
            section.addEventListener('mouseenter', function() {
                clearTimeout(closeTimeout);
                const dropdownMenu = this.querySelector('.dropdown-menu');
                const button = this.querySelector('.menu-dropdown');
                
                // Cerrar otros dropdowns
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
                
                // Abrir el actual
                dropdownMenu.classList.add('show');
                button.setAttribute('aria-expanded', 'true');
            });

            section.addEventListener('mouseleave', function() {
                const dropdownMenu = this.querySelector('.dropdown-menu');
                const button = this.querySelector('.menu-dropdown');
                closeDropdownWithDelay(dropdownMenu, button);
            });
        });

        // Mantener abierto mientras el cursor esté sobre el menú
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

    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });

    // Cerrar dropdowns al presionar la tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
        }
    });

    // Prevenir que los clicks en los items del menú propaguen
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
