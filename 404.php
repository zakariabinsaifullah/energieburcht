<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * Shows a full-width hero with the 404 message, then a centered search
 * form so visitors can quickly find what they were looking for.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Energieburcht
 */

get_header();
?>

<main id="primary" class="site-main error-404-page">

	<section id="error-404-hero" class="page-hero error-404-hero">
		<div class="container">
			<div class="page-hero-content">
				<p class="error-404-hero__code" aria-hidden="true">404</p>
				<h1 class="page-hero-title error-404-hero__title">
					<?php esc_html_e( 'Pagina niet gevonden', 'energieburcht' ); ?>
				</h1>
				<p class="error-404-hero__subtitle">
					<?php esc_html_e( 'De pagina die u zoekt bestaat niet meer, is verplaatst of is tijdelijk niet beschikbaar.', 'energieburcht' ); ?>
				</p>
			</div><!-- .page-hero-content -->
		</div><!-- .container -->
	</section><!-- #error-404-hero -->

	<div class="container boxed-content">

		<div class="error-404-body">

			<div class="error-404-search">
				<h2 class="error-404-search__heading">
					<?php esc_html_e( 'Zoek wat u zoekt', 'energieburcht' ); ?>
				</h2>
				<p class="error-404-search__description">
					<?php esc_html_e( 'Gebruik het zoekveld hieronder om de gewenste pagina te vinden.', 'energieburcht' ); ?>
				</p>
				<?php get_search_form(); ?>
			</div><!-- .error-404-search -->

			<div class="error-404-links">
				<h2 class="error-404-links__heading">
					<?php esc_html_e( 'Of bekijk een van deze pagina\'s', 'energieburcht' ); ?>
				</h2>
				<ul class="error-404-links__list">
					<li>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<?php esc_html_e( 'Startpagina', 'energieburcht' ); ?>
						</a>
					</li>
					<?php
					// Output the primary nav as quick links when available.
					$nav_items = wp_get_nav_menu_items( 'primary' );
					if ( $nav_items ) :
						foreach ( $nav_items as $item ) :
							// Top-level items only (menu_item_parent == 0).
							if ( '0' !== $item->menu_item_parent ) {
								continue;
							}
							?>
							<li>
								<a href="<?php echo esc_url( $item->url ); ?>">
									<?php echo esc_html( $item->title ); ?>
								</a>
							</li>
							<?php
						endforeach;
					endif;
					?>
				</ul>
			</div><!-- .error-404-links -->

		</div><!-- .error-404-body -->

	</div><!-- .container -->

</main><!-- #primary -->

<?php
get_footer();
