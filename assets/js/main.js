/**
 * Main JavaScript file for Energieburcht theme.
 */

document.addEventListener( 'DOMContentLoaded', function() {
    
    // Mobile Navigation Toggle
    const mobileToggle = document.querySelector('.mobile-nav-toggle');
    const mainNav = document.querySelector('.mobile-header-bottom');
    const body = document.body;
    const menuHeader = document.querySelector('.mobile-menu-header');
    const mobileMenu = document.querySelector('.mobile-menu');

    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', function() {
            const isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';
            
            mobileToggle.setAttribute('aria-expanded', !isExpanded);
            mainNav.classList.toggle('toggled');
            body.classList.toggle('menu-open');
            
            // Reset submenus on close
            if (isExpanded) {
                document.querySelectorAll('.sub-menu.active').forEach(function(el) {
                    el.classList.remove('active');
                });
            }
        });
    }

    // Mobile Drill-Down Menu Logic
    if (window.innerWidth <= 991) {
        const menuItemsWithChildren = document.querySelectorAll('.menu-item-has-children');
        
        // Chevron SVG
        const chevronIcon = '<span class="menu-arrow-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg></span>';

        menuItemsWithChildren.forEach(function(item) {
            // Append SVG Arrow to Parent Link
            item.innerHTML += chevronIcon;

            item.addEventListener('click', function(e) {
                e.preventDefault();
                mobileMenu.classList.toggle('child-nav-open');
                item.classList.toggle('active');
                menuHeader.classList.toggle('active');
                const chevron = item.querySelector('.menu-arrow-icon');
                chevron.classList.toggle('active');
                const subMenu = item.querySelector('.sub-menu');
                if (subMenu) {
                    subMenu.classList.toggle('active');
                }
            });
        });
    }

});
