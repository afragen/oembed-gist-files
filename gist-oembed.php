<?php
/**
 * Gist oEmbed.
 *
 * @package Fragen\Gist_OEmbed
 *
 * Plugin Name:       Gist oEmbed
 * Plugin URI:        https://github.com/afragen/gist-oembed
 * Description:       oEmbed Gist or files within Gists.
 * Version:           0.7.0
 * Author:            Andy Fragen, Colin Stewart
 * License:           MIT
 * Requires at least: 5.9
 * Requires PHP:      7.1
 * GitHub Plugin URI: https://github.com/afragen/gist-oembed
 * Primary Branch:    main
 */

namespace Fragen;

/**
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

wp_embed_register_handler( 'gist', '#^https?://gist.github.com/.*#i', [ new Gist_OEmbed(), 'gist_result' ] );

/**
 * Class Gist_OEmbed.
 */
class Gist_OEmbed {

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
