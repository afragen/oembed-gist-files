<?php
/**
 * oEmbed Gists and Files.
 *
 * @package Fragen\OEmbed_Gist
 *
 * Plugin Name:       oEmbed Gists and Files
 * Plugin URI:        https://github.com/afragen/oembed-gist-files
 * Description:       oEmbed Gist or files within Gists.
 * Version:           0.8.0
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

/**
 * Class Gist_OEmbed.
 */
class OEmbed_Gist {

	/**
	 * The Gist regex to match.
	 *
	 * @var string
	 */
	private $regex = '#^https?://gist.github.com/.*#i';

	/**
	 * Constructor.
	 */
	public function __construct() {
		wp_embed_register_handler( 'gist', $this->regex, [ $this, 'gist_result' ] );
		add_filter( 'pre_oembed_result', [ $this, 'pre_oembed_result' ], 10, 2 );
	}

	/**
	 * Render Gist for embed.
	 *
	 * @param array $url Gist URL.
	 *
	 * @return string
	 */
	public function gist_result( $url ) {
		$original_url = $url[0];
		$url          = strtolower( $original_url );

		// Adjust the URL if it contains a specific file within the Gist.
		$fragment = strpos( $url, '#' );
		if ( false !== $fragment ) {
			$url = $this->get_file_script( substr( $url, 0, $fragment ), substr( $url, $fragment + 1 ) );

			if ( '' === $url ) {
				$url = $original_url[0];
			}
		} elseif ( ! str_ends_with( $url, '.js' ) ) {
			$url .= '.js';
		}

		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
		return '<script src="' . esc_url( $url ) . '"></script>';
	}

	/**
	 * Gets the file's script URL.
	 *
	 * @param string $root The root URL for the Gist.
	 * @param string $file The file slug.
	 *
	 * @return string The file's script URL, or an empty string.
	 */
	private function get_file_script( $root, $file ) {
		$response = wp_remote_get( $root . '.js' );
		$body     = wp_remote_retrieve_body( $response );
		$links    = explode( '<a href=\"', $body );
		$count    = count( $links );

		for ( $i = 0; $i < $count; ++$i ) {
			if ( ! str_contains( $links[ $i ], $root . '#' . $file ) ) {
				continue;
			}

			$links[ $i - 1 ] = substr( $links[ $i - 1 ], 0, strpos( $links[ $i - 1 ], '\"' ) );
			$links[ $i - 1 ] = explode( '/', $links[ $i - 1 ] );
			$filename        = end( $links[ $i - 1 ] );

			return $root . '.js?file=' . rawurlencode( htmlspecialchars_decode( $filename ) );
		}

		return '';
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
		if ( preg_match( $this->regex, $url, $match ) ) {
			return $this->gist_result( $match );
		}

		return $html;
	}
}

new OEmbed_Gist();
