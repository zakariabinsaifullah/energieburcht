<?php
/**
 * Secure SVG Support
 *
 * Allows SVG uploads in the Media Library with a DOMDocument-based XSS
 * sanitization layer, correct MIME-type recognition via the WordPress
 * filetype filter, and dimension injection so SVGs never appear as 0×0
 * in the Media Library or in wp_get_attachment_image() output.
 *
 * Security model:
 *  - <script> and <foreignObject> elements are removed entirely.
 *  - All event-handler attributes (on*) are stripped.
 *  - href / xlink:href / src / action values using dangerous URI schemes
 *    (javascript:, data:, vbscript:) are removed.
 *  - <use> elements may only reference internal IDs (href="#...").
 *  - External entity loading is disabled to prevent XXE attacks.
 *  - DOCTYPE declarations are stripped before parsing.
 *
 * @package Energieburcht
 * @since   1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Energieburcht_Svg_Support
 */
final class Energieburcht_Svg_Support {

	/**
	 * Single shared instance of this class.
	 *
	 * @var Energieburcht_Svg_Support|null
	 */
	private static $instance = null;

	/**
	 * Elements to remove unconditionally — these are direct XSS vectors.
	 *
	 * @var string[]
	 */
	private static $dangerous_elements = array( 'script', 'foreignObject' );

	/**
	 * Attribute names (case-insensitive) that point to remote resources
	 * on <use> elements. Only internal references (#id) are permitted.
	 *
	 * @var string[]
	 */
	private static $use_ref_attrs = array( 'href', 'xlink:href' );

	/**
	 * URI schemes that must never appear in any attribute value.
	 *
	 * @var string[]
	 */
	private static $dangerous_schemes = array( 'javascript:', 'data:', 'vbscript:' );

	// =========================================================================
	// Singleton boilerplate
	// =========================================================================

	/**
	 * Private constructor — obtain the instance via get_instance().
	 */
	private function __construct() {
		// Allow SVG MIME type in the uploader.
		add_filter( 'upload_mimes', array( $this, 'allow_svg_mime' ) );

		// Ensure wp_check_filetype_and_ext() recognises the SVG extension.
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'fix_svg_mime_check' ), 10, 5 );

		// Sanitize the SVG before it is written to the uploads directory.
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'sanitize_svg_on_upload' ) );

		// Populate attachment metadata so WP never stores an empty array for SVGs.
		// Without this, wp_prepare_attachment_for_js() returns width=0/height=0,
		// which causes the media-uploader JS to show "server cannot process image".
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'generate_svg_attachment_metadata' ), 10, 2 );

		// Inject correct dimensions into the Media Library JS model.
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'fix_svg_dimensions_for_media_library' ), 10, 3 );

		// Fix width/height returned by wp_get_attachment_image_src() for SVGs.
		add_filter( 'wp_get_attachment_image_src', array( $this, 'fix_svg_src_dimensions' ), 10, 4 );
	}

	/**
	 * Return (or lazily create) the single shared instance.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/** Cloning is forbidden on a singleton. */
	private function __clone() {}

	// =========================================================================
	// MIME type registration
	// =========================================================================

	/**
	 * Append the SVG MIME type to WordPress's allowlist.
	 *
	 * Restricted to users who already have the upload_files capability so
	 * that SVG support is not available to subscribers/contributors.
	 *
	 * @param  array $mimes Existing MIME type map keyed by extension.
	 * @return array
	 */
	public function allow_svg_mime( array $mimes ): array {
		if ( current_user_can( 'upload_files' ) ) {
			$mimes['svg']  = 'image/svg+xml';
			$mimes['svgz'] = 'image/svg+xml';
		}

		return $mimes;
	}

	/**
	 * Correct the ext/type values that wp_check_filetype_and_ext() produces
	 * for SVG files.
	 *
	 * PHP's fileinfo extension may return an empty MIME string for SVGs on some
	 * server configurations. When the extension is .svg/.svgz and the type is
	 * still empty, we set both to their correct values so WordPress accepts the
	 * upload instead of rejecting it with "Sorry, this file type is not permitted".
	 *
	 * @param  array       $data     {ext, type, proper_filename} from wp_check_filetype().
	 * @param  string      $file     Absolute path to the uploaded temp file.
	 * @param  string      $filename Original filename supplied by the browser.
	 * @param  array       $mimes    Allowed MIME types passed through the filter.
	 * @param  string|bool $real_mime MIME type detected by fileinfo, or false.
	 * @return array
	 */
	public function fix_svg_mime_check( array $data, string $file, string $filename, ?array $mimes, $real_mime ): array {
		if ( $data['type'] ) {
			return $data; // Already resolved — nothing to do.
		}

		$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

		if ( 'svg' === $ext || 'svgz' === $ext ) {
			$data['ext']  = $ext;
			$data['type'] = 'image/svg+xml';
		}

		return $data;
	}

	// =========================================================================
	// Upload sanitization
	// =========================================================================

	/**
	 * Intercept SVG uploads and run the sanitizer before the file is saved.
	 *
	 * Hooked to wp_handle_upload_prefilter. If the file cannot be parsed as
	 * valid SVG XML the upload is blocked and a descriptive error is returned.
	 *
	 * @param  array $file The $_FILES entry for the current upload.
	 * @return array       The (potentially modified) $_FILES entry.
	 */
	public function sanitize_svg_on_upload( array $file ): array {
		if ( ! isset( $file['tmp_name'], $file['name'] ) ) {
			return $file;
		}

		$ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

		if ( 'svg' !== $ext && 'svgz' !== $ext ) {
			return $file; // Not an SVG — leave untouched.
		}

		$sanitized = $this->sanitize_svg_file( $file['tmp_name'] );

		if ( false === $sanitized ) {
			$file['error'] = esc_html__(
				'SVG upload rejected: the file could not be parsed or contains unsafe markup.',
				'energieburcht'
			);
			return $file;
		}

		// Overwrite the temp file with the sanitized XML.
		// Suppress the PHP warning (permission issues) with @ so it cannot
		// leak into the AJAX JSON response and corrupt it.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		$written = @file_put_contents( $file['tmp_name'], $sanitized );

		if ( false === $written ) {
			// Cannot write the sanitized file — reject rather than pass
			// the original, potentially unsafe, SVG to the uploads folder.
			$file['error'] = esc_html__(
				'SVG upload rejected: the sanitized file could not be saved. Check temp-directory permissions.',
				'energieburcht'
			);
		}

		return $file;
	}

	// =========================================================================
	// SVG sanitization core
	// =========================================================================

	/**
	 * Load an SVG file from disk, strip all XSS vectors, and return the
	 * cleaned XML string. Returns false when the file cannot be parsed.
	 *
	 * @param  string $file_path Absolute path to the (temp) SVG file.
	 * @return string|false      Sanitized SVG markup, or false on failure.
	 */
	private function sanitize_svg_file( string $file_path ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents( $file_path );

		if ( false === $content || '' === trim( $content ) ) {
			return false;
		}

		// Strip DOCTYPE declarations (XXE vector).
		// Pass 1 — handles DOCTYPE with an internal subset: <!DOCTYPE svg [...]>
		// The [^]* inside \[...\] allows > characters within entity definitions.
		$content = preg_replace( '/<!DOCTYPE[^\[>]*\[[^\]]*\]\s*>/is', '', $content );
		// Pass 2 — handles simple DOCTYPE without an internal subset.
		$content = preg_replace( '/<!DOCTYPE[^>]*>/is', '', $content );

		// Strip the XML declaration — DOMDocument will add a clean one on saveXML().
		$content = preg_replace( '/<\?xml[^?]*\?>/i', '', $content );

		// Suppress libxml errors so we can inspect them ourselves.
		$previous_errors = libxml_use_internal_errors( true );

		// On PHP < 8.0, external entity loading is not disabled by default.
		// phpcs:disable PHPCompatibility.FunctionUse.RemovedFunctions.libxml_disable_entity_loaderDeprecated
		if ( PHP_VERSION_ID < 80000 ) {
			libxml_disable_entity_loader( true );
		}
		// phpcs:enable

		$dom                    = new \DOMDocument();
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput       = true;

		$loaded = $dom->loadXML( trim( $content ), LIBXML_NONET );

		libxml_clear_errors();
		libxml_use_internal_errors( $previous_errors );

		if ( ! $loaded ) {
			return false;
		}

		// Confirm the root element is <svg> (guards against non-SVG XML).
		$root = $dom->documentElement;
		if ( ! $root || 'svg' !== strtolower( $root->localName ) ) {
			return false;
		}

		$this->remove_dangerous_elements( $dom );
		$this->remove_dangerous_attributes( $dom );

		return $dom->saveXML();
	}

	/**
	 * Remove blacklisted elements from the DOM tree.
	 *
	 * Iterates with a while loop because removeChild() mutates live NodeLists.
	 *
	 * @param  \DOMDocument $dom Parsed SVG document.
	 * @return void
	 */
	private function remove_dangerous_elements( \DOMDocument $dom ): void {
		foreach ( self::$dangerous_elements as $tag ) {
			while ( $dom->getElementsByTagName( $tag )->length > 0 ) {
				$node = $dom->getElementsByTagName( $tag )->item( 0 );
				if ( $node && $node->parentNode ) {
					$node->parentNode->removeChild( $node );
				}
			}
		}
	}

	/**
	 * Walk every element in the document and strip dangerous attributes.
	 *
	 * Rules applied per attribute:
	 *  1. Any attribute whose name starts with "on" (event handler) is removed.
	 *  2. href / xlink:href / src / action whose value begins with a dangerous
	 *     URI scheme (javascript:, data:, vbscript:) is removed.
	 *  3. On <use> elements, href / xlink:href values that are not internal
	 *     fragment references (i.e. don't start with "#") are removed.
	 *
	 * @param  \DOMDocument $dom Parsed SVG document.
	 * @return void
	 */
	private function remove_dangerous_attributes( \DOMDocument $dom ): void {
		$xpath = new \DOMXPath( $dom );
		$nodes = $xpath->query( '//*[@*]' );

		if ( false === $nodes ) {
			return;
		}

		// Link-like attributes where dangerous URI schemes must be blocked.
		$link_attrs = array( 'href', 'xlink:href', 'src', 'action' );

		foreach ( $nodes as $node ) {
			if ( ! $node instanceof \DOMElement ) {
				continue;
			}

			$attrs_to_remove = array();
			$is_use_element  = 'use' === strtolower( $node->localName );

			foreach ( $node->attributes as $attr ) {
				$attr_name  = strtolower( $attr->localName );
				$attr_value = $attr->value;

				// Rule 1: Remove all event-handler attributes (onclick, onload, etc.).
				if ( 0 === strpos( $attr_name, 'on' ) ) {
					$attrs_to_remove[] = $attr->nodeName;
					continue;
				}

				// Rule 2: Remove link attributes pointing to dangerous URI schemes.
				if ( in_array( $attr_name, $link_attrs, true ) ) {
					$value_lower = strtolower( ltrim( $attr_value ) );

					foreach ( self::$dangerous_schemes as $scheme ) {
						if ( 0 === strpos( $value_lower, $scheme ) ) {
							$attrs_to_remove[] = $attr->nodeName;
							break;
						}
					}
				}

				// Rule 3: <use> href / xlink:href must be a local fragment (#id).
				if ( $is_use_element && in_array( $attr_name, self::$use_ref_attrs, true ) ) {
					if ( '#' !== substr( ltrim( $attr_value ), 0, 1 ) ) {
						$attrs_to_remove[] = $attr->nodeName;
					}
				}
			}

			foreach ( $attrs_to_remove as $attr_node_name ) {
				$node->removeAttribute( $attr_node_name );
			}
		}
	}

	// =========================================================================
	// Attachment metadata
	// =========================================================================

	/**
	 * Build and return valid attachment metadata for SVG files.
	 *
	 * WordPress calls wp_generate_attachment_metadata() for every new upload
	 * and stores its return value. For SVGs, WordPress skips its own image
	 * processing (because getimagesize() returns false for SVGs), leaving the
	 * metadata as an empty array. An empty array means:
	 *   - No 'file' key → wp_get_attachment_image_src() cannot resolve the URL.
	 *   - No 'width'/'height' keys → wp_prepare_attachment_for_js() sends 0×0.
	 *   - The media-uploader JS sees a 0×0 image type and shows:
	 *     "The server cannot process the image."
	 *
	 * This filter runs after WordPress's own processing and injects the
	 * correct keys so the Media Library and the block editor work correctly.
	 *
	 * @param  array $metadata      Metadata produced by WordPress (empty for SVGs).
	 * @param  int   $attachment_id Attachment post ID.
	 * @return array
	 */
	public function generate_svg_attachment_metadata( array $metadata, int $attachment_id ): array {
		if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
			return $metadata;
		}

		$file = get_attached_file( $attachment_id );

		if ( ! $file ) {
			return $metadata;
		}

		// Relative path from the uploads base-dir — mirrors what WP stores for raster images.
		$metadata['file']  = _wp_relative_upload_path( $file );
		$metadata['sizes'] = array(); // No generated sub-sizes for SVGs.

		$dimensions = $this->get_svg_dimensions( $file );

		if ( $dimensions ) {
			$metadata['width']  = $dimensions['width'];
			$metadata['height'] = $dimensions['height'];
		}

		return $metadata;
	}

	// =========================================================================
	// Dimension correction
	// =========================================================================

	/**
	 * Inject intrinsic dimensions into the attachment data passed to the
	 * Media Library JavaScript model, so SVGs render at the correct size in
	 * the media grid and attachment details pane instead of showing as 0×0.
	 *
	 * @param  array    $response   Attachment JS data array.
	 * @param  \WP_Post $attachment Attachment post object.
	 * @param  mixed    $meta       Attachment metadata (array or false).
	 * @return array
	 */
	public function fix_svg_dimensions_for_media_library( array $response, \WP_Post $attachment, $meta ): array {
		if ( 'image/svg+xml' !== $response['mime'] ) {
			return $response;
		}

		$dimensions = $this->get_svg_dimensions( get_attached_file( $attachment->ID ) );

		if ( ! $dimensions ) {
			return $response;
		}

		$response['width']  = $dimensions['width'];
		$response['height'] = $dimensions['height'];
		$response['sizes']  = array(
			'full' => array(
				'url'         => $response['url'],
				'width'       => $dimensions['width'],
				'height'      => $dimensions['height'],
				'orientation' => $dimensions['height'] > $dimensions['width'] ? 'portrait' : 'landscape',
			),
		);

		return $response;
	}

	/**
	 * Fix the width/height entries returned by wp_get_attachment_image_src()
	 * for SVG attachments.
	 *
	 * Without this, wp_get_attachment_image() renders an <img> with width="0"
	 * height="0" because WordPress never stores dimensions for SVGs.
	 *
	 * @param  array|false $image         {url, width, height, is_intermediate} or false.
	 * @param  int         $attachment_id Attachment post ID.
	 * @param  mixed       $size          Requested image size (string or int[]).
	 * @param  bool        $icon          Whether the image should be treated as an icon.
	 * @return array|false
	 */
	public function fix_svg_src_dimensions( $image, int $attachment_id, $size, bool $icon ) {
		if ( false === $image ) {
			return $image;
		}

		if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
			return $image;
		}

		if ( ! empty( $image[1] ) && ! empty( $image[2] ) ) {
			return $image; // Dimensions already present — nothing to fix.
		}

		$dimensions = $this->get_svg_dimensions( get_attached_file( $attachment_id ) );

		if ( $dimensions ) {
			$image[1] = $dimensions['width'];
			$image[2] = $dimensions['height'];
		}

		return $image;
	}

	/**
	 * Parse an SVG file and extract its intrinsic width and height.
	 *
	 * Checks, in order: explicit width/height attributes on <svg>, then the
	 * viewBox attribute (min-x min-y width height).
	 *
	 * @param  string|false $file_path Absolute path to the SVG file.
	 * @return array|false             Associative array {width, height} in px, or false.
	 */
	private function get_svg_dimensions( $file_path ) {
		if ( ! $file_path || ! file_exists( $file_path ) ) {
			return false;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents( $file_path );

		if ( false === $content ) {
			return false;
		}

		$previous = libxml_use_internal_errors( true );
		$dom      = new \DOMDocument();
		$dom->loadXML( $content, LIBXML_NONET );
		libxml_clear_errors();
		libxml_use_internal_errors( $previous );

		$svg = $dom->documentElement;

		if ( ! $svg ) {
			return false;
		}

		$width  = (float) $svg->getAttribute( 'width' );
		$height = (float) $svg->getAttribute( 'height' );

		// Fall back to viewBox when explicit dimensions are absent.
		if ( ! $width || ! $height ) {
			$viewbox = $svg->getAttribute( 'viewBox' );

			if ( $viewbox ) {
				$parts = preg_split( '/[\s,]+/', trim( $viewbox ) );

				if ( is_array( $parts ) && count( $parts ) >= 4 ) {
					$width  = (float) $parts[2];
					$height = (float) $parts[3];
				}
			}
		}

		if ( ! $width || ! $height ) {
			return false;
		}

		return array(
			'width'  => (int) round( $width ),
			'height' => (int) round( $height ),
		);
	}
}
