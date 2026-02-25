/**
 * Main JavaScript file for Energieburcht theme.
 */

(function($){

    $(document).ready(function() {

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
        // Chevrons are always injected; CSS hides them on desktop.
        // The window.innerWidth check is inside the click handler so it
        // responds correctly after a browser window resize.
        const chevronIcon = '<span class="menu-arrow-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg></span>';

        $('.menu-item-has-children').each(function() {
            const $item = $(this);

            // Guard against duplicate injection (e.g. script runs twice)
            if (!$item.find('.menu-arrow-icon').length) {
                $item.append(chevronIcon);
            }

            $item.on('click', function() {
                if (window.innerWidth <= 991) {
                    $mobileMenu.toggleClass('child-nav-open');
                    $item.toggleClass('active');
                    $menuHeader.toggleClass('active');

                    $item.find('.menu-arrow-icon').toggleClass('active');

                    const $subMenu = $item.find('.sub-menu');
                    if ($subMenu.length) {
                        $subMenu.toggleClass('active');
                    }
                }
            });
        });


        // Sticky Header Logic
        const $desktopNav = $('.desktop-header-bottom');
        const $headerTop = $('.header-top');

        if ($desktopNav.length && $headerTop.length) {
            const navHeight = $desktopNav.outerHeight();
            const topBarHeight = $headerTop.outerHeight();
            const stickyThreshold = topBarHeight;

            $(window).on('scroll', function() {
                if (window.innerWidth > 991) {
                    if ($(window).scrollTop() > stickyThreshold) {
                        if (!$body.hasClass('is-sticky')) {
                            $body.addClass('is-sticky');
                            $body.css('padding-top', navHeight + 'px');
                        }
                    } else {
                        if ($body.hasClass('is-sticky')) {
                            $body.removeClass('is-sticky');
                            $body.css('padding-top', 0);
                        }
                    }
                } else {
                    if ($body.hasClass('is-sticky')) {
                        $body.removeClass('is-sticky');
                        $body.css('padding-top', 0);
                    }
                }
            });
        }

    });

})(jQuery);
