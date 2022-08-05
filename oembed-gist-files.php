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
}
