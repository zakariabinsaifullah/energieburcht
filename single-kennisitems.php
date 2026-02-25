<?php
/**
 * The template for displaying single Kennisitems posts.
 *
 * Layout (all within a 90 % max-width wrapper):
 *   Col 1 — Sticky vertical social share (Facebook, X, LinkedIn).
 *   Col 2 — Post title, meta (category tag + date), entry content, author box.
 *   Col 3 — Sticky table of contents (populated by kennisitems-single.js).
 *
 * Visibility of columns 1 and 3 is controlled via Customizer toggles.
 * Grid adapts automatically via modifier classes on .single-kennisitems-body.
 *
 * Full-width related posts section rendered below via template part.
 *
 * @package Energieburcht
 */

// ── Customizer: visibility ─────────────────────────────────────────────────────
$show_social    = (bool) get_theme_mod( 'energieburcht_cpt_kennisitems_single_show_social',         true );
$show_toc       = (bool) get_theme_mod( 'energieburcht_cpt_kennisitems_single_show_toc',             true );
$show_author    = (bool) get_theme_mod( 'energieburcht_cpt_kennisitems_single_show_author',          true );
$show_feat_img  = (bool) get_theme_mod( 'energieburcht_cpt_kennisitems_single_show_featured_image', true );

// ── Customizer: social share colours ──────────────────────────────────────────
$social_color       = get_theme_mod( 'energieburcht_cpt_kennisitems_single_social_color',       '#003449' );
$social_bg          = get_theme_mod( 'energieburcht_cpt_kennisitems_single_social_bg',           '#f0f4f8' );
$social_hover_color = get_theme_mod( 'energieburcht_cpt_kennisitems_single_social_hover_color',  '#ffffff' );
$social_hover_bg    = get_theme_mod( 'energieburcht_cpt_kennisitems_single_social_hover_bg',     '#00acdd' );

// ── Customizer: TOC colours ────────────────────────────────────────────────────
$toc_bg           = get_theme_mod( 'energieburcht_cpt_kennisitems_single_toc_bg',           '#f8f9fa' );
$toc_title_color  = get_theme_mod( 'energieburcht_cpt_kennisitems_single_toc_title_color',  '#003449' );
$toc_link_color   = get_theme_mod( 'energieburcht_cpt_kennisitems_single_toc_link_color',   '#003449' );
$toc_active_color = get_theme_mod( 'energieburcht_cpt_kennisitems_single_toc_active_color', '#00acdd' );

// ── Customizer: author box colours ────────────────────────────────────────────
$author_bg         = get_theme_mod( 'energieburcht_cpt_kennisitems_single_author_bg',         '#f8f9fa' );
$author_name_color = get_theme_mod( 'energieburcht_cpt_kennisitems_single_author_name_color',  '#003449' );
$author_bio_color  = get_theme_mod( 'energieburcht_cpt_kennisitems_single_author_bio_color',   '#212529' );

// ── Build body modifier class ──────────────────────────────────────────────────
$body_class = 'single-kennisitems-body';
if ( ! $show_social ) {
	$body_class .= ' no-social';
}
if ( ! $show_toc ) {
	$body_class .= ' no-toc';
}

get_header();
?>

<style>
.single-kennisitems {
	--ki-social-color:       <?php echo esc_attr( $social_color ); ?>;
	--ki-social-bg:          <?php echo esc_attr( $social_bg ); ?>;
	--ki-social-hover-color: <?php echo esc_attr( $social_hover_color ); ?>;
	--ki-social-hover-bg:    <?php echo esc_attr( $social_hover_bg ); ?>;
	--ki-toc-bg:             <?php echo esc_attr( $toc_bg ); ?>;
	--ki-toc-title-color:    <?php echo esc_attr( $toc_title_color ); ?>;
	--ki-toc-link-color:     <?php echo esc_attr( $toc_link_color ); ?>;
	--ki-toc-active-color:   <?php echo esc_attr( $toc_active_color ); ?>;
	--ki-author-bg:          <?php echo esc_attr( $author_bg ); ?>;
	--ki-author-name-color:  <?php echo esc_attr( $author_name_color ); ?>;
	--ki-author-bio-color:   <?php echo esc_attr( $author_bio_color ); ?>;
}
</style>

<main id="primary" class="site-main single-kennisitems">

	<?php get_template_part( 'parts/breadcrumbs' ); ?>

	<div class="container boxed-content">
		<div class="single-kennisitems-wrap">

			<?php while ( have_posts() ) : the_post(); ?>

			<div class="<?php echo esc_attr( $body_class ); ?>">

				<?php /* ── Col 1: Sticky social share ──────────────────────── */ ?>
				<?php if ( $show_social ) : ?>
				<aside class="single-kennisitems-social" aria-label="<?php esc_attr_e( 'Deel dit artikel', 'energieburcht' ); ?>">
					<div class="ki-social-share">

						<?php
						$share_url   = rawurlencode( get_permalink() );
						$share_title = rawurlencode( get_the_title() );
						?>

						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>"
						   class="ki-social-share__btn ki-social-share__btn--facebook"
						   target="_blank" rel="noopener noreferrer"
						   aria-label="<?php esc_attr_e( 'Deel op Facebook', 'energieburcht' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true">
								<path d="M24 12.073C24 5.406 18.627 0 12 0S0 5.406 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047v-2.66c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.884v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
							</svg>
						</a>

						<a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>"
						   class="ki-social-share__btn ki-social-share__btn--twitter"
						   target="_blank" rel="noopener noreferrer"
						   aria-label="<?php esc_attr_e( 'Deel op X (Twitter)', 'energieburcht' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true">
								<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
							</svg>
						</a>

						<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>"
						   class="ki-social-share__btn ki-social-share__btn--linkedin"
						   target="_blank" rel="noopener noreferrer"
						   aria-label="<?php esc_attr_e( 'Deel op LinkedIn', 'energieburcht' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true">
								<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
							</svg>
						</a>

					</div><!-- .ki-social-share -->
				</aside>
				<?php endif; ?>

				<?php /* ── Col 2: Main content ──────────────────────────────── */ ?>
				<article class="single-kennisitems-content entry-content-wrapper">

					<?php /* Post header */ ?>
					<header class="single-kennisitems-header">
						<h1 class="single-kennisitems-title"><?php the_title(); ?></h1>

						<div class="ki-post-meta">
							<?php
							$meta_terms = get_the_terms( get_the_ID(), 'kennisitems-categorie' );
							if ( $meta_terms && ! is_wp_error( $meta_terms ) ) :
								foreach ( $meta_terms as $meta_term ) :
							?>
								<span class="ki-post-meta__cat"><?php echo esc_html( $meta_term->name ); ?></span>
							<?php
								endforeach;
							endif;
							?>
							<span class="ki-post-meta__date">
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<?php echo get_the_date(); ?>
								</time>
							</span>
						</div><!-- .ki-post-meta -->
					</header><!-- .single-kennisitems-header -->

					<?php /* Featured image */ ?>
					<?php if ( $show_feat_img && has_post_thumbnail() ) : ?>
						<div class="ki-featured-image">
							<?php the_post_thumbnail( 'large', array( 'class' => 'ki-featured-image__img' ) ); ?>
						</div>
					<?php endif; ?>

					<?php /* Main post content */ ?>
					<div class="entry-content ki-entry-content">
						<?php the_content(); ?>
					</div>

					<?php /* Author box */ ?>
					<?php if ( $show_author ) :
						$author_bio    = get_the_author_meta( 'description' );
						$author_name   = get_the_author();
						$author_avatar = get_avatar( get_the_author_meta( 'ID' ), 80 );
						if ( $author_bio || $author_avatar ) :
					?>
					<div class="ki-author-box">
						<?php if ( $author_avatar ) : ?>
							<div class="ki-author-box__avatar"><?php echo $author_avatar; ?></div>
						<?php endif; ?>
						<div class="ki-author-box__info">
							<?php if ( $author_name ) : ?>
								<p class="ki-author-box__name"><?php echo esc_html( $author_name ); ?></p>
							<?php endif; ?>
							<?php if ( $author_bio ) : ?>
								<p class="ki-author-box__bio"><?php echo wp_kses_post( $author_bio ); ?></p>
							<?php endif; ?>
						</div>
					</div><!-- .ki-author-box -->
					<?php endif; endif; ?>

				</article><!-- .single-kennisitems-content -->

				<?php /* ── Col 3: Sticky table of contents ─────────────────── */ ?>
				<?php if ( $show_toc ) : ?>
				<aside class="single-kennisitems-toc" aria-label="<?php esc_attr_e( 'Inhoudsopgave', 'energieburcht' ); ?>">
					<div class="ki-toc" id="ki-toc">
						<p class="ki-toc__title"><?php esc_html_e( 'Inhoudsopgave', 'energieburcht' ); ?></p>
						<nav class="ki-toc__nav" id="ki-toc-nav" aria-label="<?php esc_attr_e( 'Inhoudsopgave navigatie', 'energieburcht' ); ?>">
							<?php /* Populated by kennisitems-single.js */ ?>
						</nav>
					</div>
				</aside>
				<?php endif; ?>

			</div><!-- .single-kennisitems-body -->

			<?php endwhile; ?>

		</div><!-- .single-kennisitems-wrap -->
	</div><!-- .container -->

	<?php /* ── Full-width related posts ────────────────────────────────────── */ ?>
	<?php get_template_part( 'parts/related', 'kennisitems' ); ?>

</main><!-- #primary -->

<?php
get_footer();
