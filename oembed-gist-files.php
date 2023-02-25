<?php
/**
 * oEmbed Gists and Files.
 *
 * @package Fragen\OEmbed_Gist
 *
 * Plugin Name:       oEmbed Gists and Files
 * Plugin URI:        https://github.com/afragen/oembed-gist-files
 * Description:       oEmbed Gist or files within Gists.
 * Version:           0.7.1
 * Author:            Andy Fragen, Colin Stewart
 * License:           MIT
 * Requires at least: 5.9
 * Requires PHP:      7.1
 * GitHub Plugin URI: https://github.com/afragen/oembed-gist-files
 * Primary Branch:    main
 */

namespace Fragen;

/**
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

wp_embed_register_handler( 'gist', '#^https?://gist.github.com/.*#i', [ new OEmbed_Gist(), 'gist_result' ] );

/**
 * Class Gist_OEmbed.
 */
class OEmbed_Gist {

	/**
	 * Render Gist for embed.
	 *
	 * @param array $url Gist URL.
	 *
	 * @return string
	 */
	public function gist_result( $url ) {
		$url = strtolower( $url[0] );

		// Adjust the URL if it contains a specific file within the Gist.
		$fragment = strpos( $url, '#' );
		if ( false !== $fragment ) {
			if ( str_contains( $url, '.js#' ) ) {
				$url = str_replace( '.js#file-', '.js?file=', $url );
			} else {
				$url = str_replace( '#file-', '.js?file=', $url );
			}
			
			$last_hyphen = strrpos( $url, '-' );

			if ( false !== $last_hyphen && $last_hyphen > $fragment ) {
				$url[ $last_hyphen ] = '.';
			}
		} elseif ( ! str_ends_with( $url, '.js' ) ) {
			$url .= '.js';
		}

		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
		return '<script src="' . esc_url( $url ) . '"></script>';
	}
}
