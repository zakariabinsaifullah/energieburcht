/**
 * Back to Top Button Logic
 * 
 * Toggles visibility of the back-to-top button based on scroll position
 * and handles smooth scrolling to the top.
 */
document.addEventListener('DOMContentLoaded', function() {
    var backToTopButton = document.getElementById('back-to-top');

    if (backToTopButton) {
        // Toggle visibility on scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });

        // Smooth scroll to top
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
