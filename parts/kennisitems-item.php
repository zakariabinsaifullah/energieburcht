<?php
/**
 * Template part for a single Kennisitems card.
 *
 * Used inside the category-grouped grid in archive-kennisitems.php.
 * The featured image is optional — only rendered when the post has one.
 * Category is not shown on the card because it already appears as the
 * section heading above the grid row.
 *
 * @package Energieburcht
 */

$read_more_text = get_theme_mod( 'energieburcht_cpt_kennisitems_read_more_text', __( 'Lees meer', 'energieburcht' ) );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'kennisitems-item' ); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="kennisitems-thumbnail" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'large' ); ?>
		</a>
	<?php endif; ?>

	<div class="kennisitems-content">

		<h2 class="kennisitems-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<?php if ( has_excerpt() ) : ?>
			<div class="kennisitems-excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>

		<a href="<?php the_permalink(); ?>" class="kennisitems-read-more">
			<?php echo esc_html( $read_more_text ); ?>
		</a>

	</div><!-- .kennisitems-content -->

</article>
