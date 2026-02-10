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

	<header id="masthead" class="site-header">
        
        <!-- Part 1: Top Bar (Logo + Widgets) -->
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

        <!-- Part 2: Navigation Bar -->
		<nav id="site-navigation" class="main-navigation header-bottom">
            <div class="container">
                <?php
                    // Header Right Widget for Mobile (Hidden on Desktop via CSS)
                    if ( is_active_sidebar( 'header-right' ) ) {
                        echo '<div class="mobile-header-widget">';
                        dynamic_sidebar( 'header-right' );
                        echo '</div>';
                    }

                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'container'      => false, // We use the nav tag as container
                            'menu_class'     => 'menu-items',
                        )
                    );
                ?>
            </div>
		</nav><!-- #site-navigation -->

	</header><!-- #masthead -->

    <!-- Mobile Navigation Toggle (Fixed Bottom) -->
    <button class="mobile-nav-toggle" aria-controls="primary-menu" aria-expanded="false">
        <span class="screen-reader-text"><?php esc_html_e( 'Menu', 'energieburcht' ); ?></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
