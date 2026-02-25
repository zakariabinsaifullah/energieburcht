<?php
/**
 * Template part for a single Projecten card.
 *
 * Used in two contexts:
 *  - archive-projecten.php  : called via get_template_part() inside the main loop
 *  - ajax_projecten_filter()  : called via get_template_part() inside a WP_Query loop
 *
 * @package Energieburcht
 */

$read_more_text = get_theme_mod( 'energieburcht_cpt_projecten_read_more_text', __( 'Lees meer', 'energieburcht' ) );

// Fetch the first assigned category for the category tag.
$terms     = get_the_terms( get_the_ID(), 'projecten-categorie' );
$first_cat = ( $terms && ! is_wp_error( $terms ) ) ? reset( $terms ) : null;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'projecten-item' ); ?> data-term-id="<?php echo $first_cat ? esc_attr( $first_cat->term_id ) : '0'; ?>">

	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="projecten-thumbnail" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'large' ); ?>
		</a>
	<?php endif; ?>

	<div class="projecten-content">

		<?php if ( $first_cat ) : ?>
			<span class="projecten-cat-tag"><?php echo esc_html( $first_cat->name ); ?></span>
		<?php endif; ?>

		<h2 class="projecten-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<?php if ( has_excerpt() ) : ?>
			<div class="projecten-excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>

		<a href="<?php the_permalink(); ?>" class="projecten-read-more">
			<?php echo esc_html( $read_more_text ); ?>
		</a>

	</div><!-- .projecten-content -->

</article>
