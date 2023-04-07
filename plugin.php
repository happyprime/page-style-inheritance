<?php
/**
 * Plugin Name:  Page Style Inheritance
 * Description:  Style inheritance for hierarchical pages.
 * Version:      0.0.1
 * Plugin URI:   https://github.com/happyprime/page-style-inheritance/
 * Author:       Happy Prime
 * Author URI:   https://happyprime.co
 * Text Domain:  page-style-inheritance
 * Requires PHP: 7.4
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package page-style-inheritance
 */

namespace PageStyleInheritance;

define( 'HP_PSI_PLUGIN_DIR', __DIR__ );
define( 'HP_PSI_PLUGIN_FILE', __FILE__ );

add_action( 'init', __NAMESPACE__ . '\register_meta' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets' );
add_filter( 'body_class', __NAMESPACE__ . '\filter_body_class' );
add_action( 'wp_head', __NAMESPACE__ . '\output_styles', 50 );

/**
 * Retrieve a list of registered styles.
 *
 * @since 1.0.0
 *
 * @return array $styles A list of styles available for inheritance.
 */
function get_styles() {
	$default = [
		''        => [
			'name' => __( 'Inherit parent style', 'page-style-inheritance' ),
		],
		'example' => [
			'name'      => __( 'Example', 'page-style-inheritance' ),
			'variables' => [
				'--page-style-color' => '#132324',
			],
			'selectors' => [
				'.example-selector' => [
					'color'            => 'var(--wp--preset--color--primary)',
					'background-color' => 'var(--wp--preset--color--secondary)',
				],
			],
		],
	];

	/**
	 * Filters the list of styles available for inheritance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $styles A multidimensional array of styles and their
	 *                         associated variables and selectors
	 */
	$styles = apply_filters( 'psi_styles', $default );

	return $styles;
}

/**
 * Register plugin meta keys.
 */
function register_meta() {
	register_post_meta(
		'page',
		'psi_page_style',
		[

			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'default'      => 'none',
		]
	);
}

/**
 * Retrieve the style to use for a page.
 *
 * This traverses the entire chain of a page and its parents and will use the
 * first style found.
 *
 * @return string The page style key.
 */
function get_page_style(): string {
	$page_id = get_queried_object_id();
	$pages   = get_post_ancestors( $page_id );
	array_unshift( $pages, $page_id );

	$styles = get_styles();

	foreach ( $pages as $page_id ) {
		$page_style = get_post_meta( $page_id, 'psi_page_style', true );

		if ( $page_style && array_key_exists( $page_style, $styles ) ) {
			return $page_style;
		}
	}

	return '';
}

/**
 * Filter the list of classes output on the body tag.
 *
 * @param string[] $classes A list of class names.
 * @return string[] A modified list of class names.
 */
function filter_body_class( array $classes ): array {
	if ( ! is_singular( 'page' ) ) {
		return $classes;
	}

	$page_style = get_page_style();
	$styles     = get_styles();

	if ( ! array_key_exists( $page_style, $styles ) ) {
		return $classes;
	}

	$classes[] = 'has-inherited-style-' . sanitize_key( $page_style );

	return $classes;
}

/**
 * Output inherited styles for the page in the head of the document.
 */
function output_styles() {
	if ( ! is_singular( 'page' ) ) {
		return;
	}

	$page_style = get_page_style();
	$styles     = get_styles();

	if ( ! array_key_exists( $page_style, $styles ) ) {
		return;
	}

	?>
	<style>
		<?php if ( 0 < count( $styles[ $page_style ]['variables'] ) ) : ?>
		:root {
			<?php
			foreach ( $styles[ $page_style ]['variables'] as $property => $value ) {
				echo esc_attr( $property ) . ': ' . esc_attr( $value ) . ";\n";
			}
			?>
		}
		<?php endif; ?>

		<?php
		if ( 0 < count( $styles[ $page_style ]['selectors'] ) ) {
			foreach ( $styles[ $page_style ]['selectors'] as $selector => $properties ) {
				echo esc_attr( $selector ) . " {\n";

				foreach ( $properties as $property => $value ) {
					echo esc_attr( $property ) . ': ' . esc_attr( $value ) . ";\n";
				}
				echo '}';
			}
		}
		?>
	</style>
	<?php
}

/**
 * Enqueue assets for the block editor.
 */
function enqueue_block_editor_assets() {
	if ( 'page' === get_current_screen()->id ) {
		$asset_data = require_once HP_PSI_PLUGIN_DIR . '/js/build/meta-box.asset.php';

		wp_register_script(
			'page-style-inheritance',
			plugins_url( 'js/build/meta-box.js', HP_PSI_PLUGIN_FILE ),
			$asset_data['dependencies'],
			$asset_data['version'],
			true
		);

		$styles = get_styles();

		$style_selections = [];

		foreach ( $styles as $key => $style ) {
			$style_selections[] = [
				'label' => $style['name'],
				'value' => $key,
			];
		}

		wp_localize_script(
			'page-style-inheritance',
			'pageStyleInheritance',
			$style_selections
		);

		wp_enqueue_script( 'page-style-inheritance' );
	}
}
