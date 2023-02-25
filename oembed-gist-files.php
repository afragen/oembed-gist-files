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

$oembed_gist = new OEmbed_Gist();
wp_embed_register_handler( 'gist', '#^https?://gist.github.com/.*#i', [ $oembed_gist, 'gist_result' ] );
add_filter( 'pre_oembed_result', [ $oembed_gist, 'pre_oembed_result' ], 10, 2 );

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
		$url      = $url[0];
		$fragment = '';
		$parsed   = explode( '#', $url );

		// Parse elements of URL for specific file within Gist.
		if ( isset( $parsed[1] ) ) {
			$url      = $parsed[0];
			$fragment = str_replace( 'file-', '', $parsed[1] );
			$file_arr = explode( '-', $fragment );
			$ext      = array_pop( $file_arr );
			$fragment = implode( '-', $file_arr ) . '.' . $ext;
			$fragment = '?file=' . $fragment;
		}

		if ( ! str_ends_with( strtolower( $url ), '.js' ) ) {
			$url .= '.js';
		}

		$url = ! empty( $fragment ) ? $url . $fragment : $url;

		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
		return '<script src="' . esc_url( $url ) . '"></script>';
	}

	/**
	 * Returns the HTML for a Gist.
	 *
	 * @param string|null $html The existing HTML, or null.
	 * @param string      $url  The URL.
	 *
	 * @return string|null The Gist HTML, the existing HTML, or null.
	 */
	public function pre_oembed_result( $html, $url ) {
		if ( preg_match( '#^https?://gist.github.com/.*#i', $url, $match ) ) {
			return $this->gist_result( $match );
		}

		return $html;
	}
}
