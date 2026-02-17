/**
 * Main JavaScript file for Energieburcht theme.
 */

(function($){


    // Mobile Navigation Toggle
    const $mobileToggle = $('.mobile-nav-toggle');
    const $mainNav = $('.mobile-header-bottom');
    const $body = $('body');
    const $menuHeader = $('.mobile-menu-header');
    const $mobileMenu = $('.mobile-menu');

    if ($mobileToggle.length && $mainNav.length) {
        $mobileToggle.on('click', function() {
            const isExpanded = $mobileToggle.attr('aria-expanded') === 'true';
            
            $mobileToggle.attr('aria-expanded', !isExpanded);
            $mainNav.toggleClass('toggled');
            $body.toggleClass('menu-open');
            
            // Reset submenus on close
            if (isExpanded) {
                $('.sub-menu.active').removeClass('active');
            }
        });
    }

    // Mobile Drill-Down Menu Logic
    if (window.innerWidth <= 991) {
        const $menuItemsWithChildren = $('.menu-item-has-children');
        
        // Chevron SVG
        const chevronIcon = '<span class="menu-arrow-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg></span>';

        $menuItemsWithChildren.each(function() {
            const $item = $(this);
            // Append SVG Arrow to Parent Link
            $item.append(chevronIcon);

            $item.on('click', function(e) {
                e.preventDefault();
                $mobileMenu.toggleClass('child-nav-open');
                $item.toggleClass('active');
                $menuHeader.toggleClass('active');
                
                const $chevron = $item.find('.menu-arrow-icon');
                $chevron.toggleClass('active');
                
                const $subMenu = $item.find('.sub-menu');
                if ($subMenu.length) {
                    $subMenu.toggleClass('active');
                }
            });
        });
    }


    // Sticky Header Logic
    const $desktopNav = $('.desktop-header-bottom');
    const $headerTop = $('.header-top');

    if ($desktopNav.length && $headerTop.length) {
        const navHeight = $desktopNav.outerHeight();
        const topBarHeight = $headerTop.outerHeight();
        // Calculate the threshold: when we scroll past the top bar
        const stickyThreshold = topBarHeight;

        $(window).on('scroll', function() {
            if ($(window).scrollTop() > stickyThreshold) {
                if (!$body.hasClass('is-sticky')) {
                    $body.addClass('is-sticky');
                    // Add padding to body to prevent jump, matching nav height
                    // only if we want to push content down. 
                    // Alternatively, we can add a placeholder dynamically.
                    // For simplicity and robustness, adding padding-top to body or a wrapper is common.
                    // Let's check if there's a better wrapper, otherwise body padding.
                    // But site-header might be transparent. Let's try adding a placeholder logic or just body padding.
                    // Given the previous jumpiness, padding is key.
                    $body.css('padding-top', navHeight + 'px');
                }
            } else {
                if ($body.hasClass('is-sticky')) {
                    $body.removeClass('is-sticky');
                    $body.css('padding-top', 0);
                }
            }
        });
    }

})(jQuery);
