<?php
/**
 * Template part for displaying page content in page.php
 *
 * @package Energieburcht
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		$global_show = get_theme_mod( 'energieburcht_page_title_enable', false );
		$local_vis   = get_post_meta( get_the_ID(), '_energieburcht_title_visibility', true );

		$should_show = $global_show;

		if ( 'show' === $local_vis ) {
			$should_show = true;
		} elseif ( 'hide' === $local_vis ) {
			$should_show = false;
		}

		if ( $should_show ) :
			// Check if Hero is enabled and showing title
			$hero_global_enable = get_theme_mod( 'energieburcht_hero_enable', false );
			$hero_local_vis     = get_post_meta( get_the_ID(), '_energieburcht_hero_visibility', true );
			$hero_show_title    = get_theme_mod( 'energieburcht_hero_show_title', true );

			$hero_active = $hero_global_enable;
			if ( 'show' === $hero_local_vis ) {
				$hero_active = true;
			} elseif ( 'hide' === $hero_local_vis ) {
				$hero_active = false;
			}

			// If Hero is active AND showing title, don't show standard title
			if ( $hero_active && $hero_show_title ) {
				$should_show = false;
			}
		endif;

		if ( $should_show ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		endif;
		?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'energieburcht' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Edit <span class="screen-reader-text">%s</span>', 'energieburcht' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
