<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @package energieburcht
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'energieburcht' ); ?></a>

    <!-- Main Header -->
	<header id="masthead" class="site-header">
        
        <!-- Part 1: Top Bar (Logo + Widgets) : Desktop -->
        <div class="header-top">
            <div class="container">
                <div class="site-branding">
                    <?php
                        if ( has_custom_logo() ) {
                            the_custom_logo();
                        } else {
                            ?>
                            <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                            <?php
                        }
                    ?>
                </div><!-- .site-branding -->

                <div class="header-actions">
                    <?php
                        if ( is_active_sidebar( 'header-right' ) ) {
                            dynamic_sidebar( 'header-right' );
                        }

                        // Header Search
                        if ( get_theme_mod( 'energieburcht_header_search_enable', false ) ) {
                        ?>
                        <div class="header-search">
                            <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Zoeken...', 'placeholder', 'energieburcht' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                                <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'energieburcht' ); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </button>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                </div><!-- .header-actions -->
            </div><!-- .container -->
        </div><!-- .header-top -->

        <!-- Navigation: Desktop -->
        <nav id="site-navigation" class="desktop-header-bottom main-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'energieburcht' ); ?>">
            <div class="container">
                <div class="sticky-logo">
                    <?php
                        if ( has_custom_logo() ) {
                            the_custom_logo();
                        }
                    ?>
                </div>
                <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'container'      => false,
                            'menu_class'     => 'menu-items desktop-menu',
                        )
                    );
                ?>
            </div>
        </nav>

        <!-- Navigation: Mobile -->
		<div class="mobile-header-bottom">
            <div class="header-top">
                <div class="container">
                    <div class="site-branding">
                        <?php
                            if ( has_custom_logo() ) {
                                the_custom_logo();
                            } else {
                                ?>
                                <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                                <?php
                            }
                        ?>
                    </div><!-- .site-branding -->

                    <div class="header-actions">
                        <?php
                            // Header Search
                            if ( get_theme_mod( 'energieburcht_header_search_enable', false ) ) {
                            ?>
                            <div class="header-search">
                                <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                                    <input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Zoeken...', 'placeholder', 'energieburcht' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                                    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'energieburcht' ); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    </button>
                                </form>
                            </div>
                            <?php
                        }
                        ?>
                    </div><!-- .header-actions -->
                </div><!-- .container -->
            </div><!-- .header-top -->
            <?php
                if ( is_active_sidebar( 'header-right' ) ) {
                    ?>
                        <div class="mobile-header-right">
                            <?php dynamic_sidebar( 'header-right' ); ?>
                        </div>
                    <?php
                }
            ?>
            <div class="mobile-menu-header">
                <span class="mobile-menu-title"><?php esc_html_e( 'Menu', 'energieburcht' ); ?></span>
            </div>
            <!-- Mobile nav uses a distinct ID to avoid duplicate-ID HTML validation errors. -->
            <nav id="mobile-site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Mobile navigation', 'energieburcht' ); ?>">
                <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'mobile-primary-menu',
                            'container'      => false,
                            'menu_class'     => 'menu-items mobile-menu',
                        )
                    );
                ?>
            </nav>
		</div><!-- .mobile-header-bottom -->

	</header><!-- #masthead -->

    <!-- Mobile Navigation Toggle (Fixed Bottom) -->
    <button class="mobile-nav-toggle" aria-controls="primary-menu" aria-expanded="false">
        <span class="screen-reader-text"><?php esc_html_e( 'Menu', 'energieburcht' ); ?></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
