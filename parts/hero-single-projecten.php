<?php
/**
 * Template part for the Single Projecten Hero.
 *
 * Displays the featured image as a container-width banner.
 * The post title is anchored to the bottom of the image, revealed
 * by a gradient that fades from solid black at the base to fully
 * transparent at the top.
 *
 * If the post has no featured image this part returns silently.
 *
 * Loaded by single-projecten.php via:
 *   get_template_part( 'parts/hero-single', 'projecten' )
 *
 * @package Energieburcht
 */

if ( ! has_post_thumbnail() ) {
	return;
}

$img_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
?>

<div
	class="single-projecten-hero"
	style="background-image: url('<?php echo esc_url( $img_url ); ?>');"
	role="img"
	aria-label="<?php the_title_attribute(); ?>"
>
	<div class="single-projecten-hero__gradient" aria-hidden="true"></div>

	<div class="single-projecten-hero__content">
		<h1 class="single-projecten-hero__title"><?php the_title(); ?></h1>
	</div>
</div>
