/**
 * Main JavaScript file for Energieburcht theme.
 */

document.addEventListener( 'DOMContentLoaded', function() {
    
    // Mobile Navigation Toggle
    const mobileToggle = document.querySelector('.mobile-nav-toggle');
    const mainNav = document.querySelector('.main-navigation');
    const body = document.body;

    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', function() {
            const isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';
            
            mobileToggle.setAttribute('aria-expanded', !isExpanded);
            mainNav.classList.toggle('toggled');
            body.classList.toggle('menu-open');
            
            // Trap focus or handle accessibility if needed
        });
    }

});
