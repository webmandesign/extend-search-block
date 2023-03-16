<?php
/**
 * Block.
 *
 * @package    Extend Search Block
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Block\Mod\Search;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Block {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'enqueue_block_editor_assets', __CLASS__ . '::enqueue_scripts' );

				add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_styles' );

			// Filters

				add_filter( 'render_block', __CLASS__ . '::render_block__post_type', 10, 2 );
				add_filter( 'render_block', __CLASS__ . '::render_block__taxonomy', 10, 2 );

				add_filter( 'register_page_post_type_args', __CLASS__ . '::make_page_publicly_queryable' );

	} // /init

	/**
	 * Enqueue block scripts.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function enqueue_scripts() {

		// Variables

			$handle = 'extend-search-block';


		// Processing

			wp_register_script(
				$handle,
				EXTEND_SEARCH_BLOCK_URL . 'blocks/search/mods.js',
				array( // @TODO
					'wp-blocks',
					'wp-hooks',
					'wp-element',
					'wp-compose',
					'wp-components',
					'wp-i18n',
					'wp-block-editor',
					'wp-polyfill',
					'lodash',
				),
				'v' . EXTEND_SEARCH_BLOCK_VERSION
			);

				wp_add_inline_script(
					$handle,
					'var wmdExtendSearchBlock = {};', // @TODO
					'before'
				);

				wp_enqueue_script( $handle );

	} // /enqueue_scripts

	/**
	 * Enqueue block styles.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function enqueue_styles() {

		// Processing

			ob_start();
			include_once EXTEND_SEARCH_BLOCK_PATH . 'blocks/search/block.css';
			$css = ob_get_clean();

			wp_add_inline_style(
				'wp-block-search',
				wp_strip_all_tags( $css )
			);

	} // /enqueue_styles

	/**
	 * Block output modification: Add hidden field for post type.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $block_content  The rendered content. Default null.
	 * @param  array  $block          The block being rendered.
	 *
	 * @return  string
	 */
	public static function render_block__post_type( string $block_content, array $block ): string {

		// Processing

			if (
				'core/search' === $block['blockName']
				&& ! empty( $block['attrs']['postType'] )
			) {

				$fields = array_filter( array_map(
					function( $post_type ) {
						if ( post_type_exists( $post_type ) ) {
							return '<input '
							. 'type="hidden" '
							. 'name="post_type[]" '
							. 'value="' . esc_attr( $post_type ) . '" '
							. '/>';
						} else {
							return '';
						}
					},
					(array) $block['attrs']['postType']
				) );

				$block_content = str_replace(
					'</form>',
					implode( '', $fields ) . '</form>',
					$block_content
				);
			}


		// Output

			return $block_content;

	} // /render_block__post_type

	/**
	 * Block output modification: Add dropdown for taxonomy.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $block_content  The rendered content. Default null.
	 * @param  array  $block          The block being rendered.
	 *
	 * @return  string
	 */
	public static function render_block__taxonomy( string $block_content, array $block ): string {

		// Processing

			if (
				'core/search' === $block['blockName']
				&& ! empty( $block['attrs']['taxonomy'] )
			) {

				$fields = array_filter( array_map(
					function( $taxonomy ) {
						if ( $taxonomy = get_taxonomy( $taxonomy ) ) {

							$select = wp_dropdown_categories( array(
								'taxonomy'          => (string) $taxonomy->name,
								'name'              => (string) $taxonomy->query_var,
								'value_field'       => 'slug',
								'orderby'           => 'name',
								'show_option_none'  => (string) $taxonomy->labels->singular_name,
								'option_none_value' => '',
								'hierarchical'      => (bool) $taxonomy->hierarchical,
								'show_count'        => true,
								'hide_empty'        => true,
								'class'             => 'wp-block-search__select wp-block-search__select--' . (string) $taxonomy->name,
								'echo'              => false,
							) );

							return str_replace(
								'<select ',
								'<select aria-label="' . esc_attr( (string) $taxonomy->labels->search_items ) . '" ',
								$select
							);
						} else {
							return '';
						}
					},
					(array) $block['attrs']['taxonomy']
				) );

				// Select element inline styles.
				if ( is_callable( 'styles_for_block_core_search' ) ) {
					$style = styles_for_block_core_search( $block['attrs'] );
				} else {
					$style = array( 'input' => '' );
				}

				// Select element CSS class.
				if ( ! empty( $block['attrs']['borderColor'] ) ) {
					$class = ' has-border-color has-' . $block['attrs']['borderColor'] . '-border-color';
				} else {
					$class = '';
				}

				$block_content = str_replace(
					array(
						'wp-block-search__inside-wrapper',
						'<input type="search"',
						'<select ',
						'wp-block-search__select',
					),
					array(
						'wp-block-search__inside-wrapper has-dropdown',
						implode( '', $fields ) . '<input type="search"',
						'<select ' . $style['input'] . ' ',
						'wp-block-search__select' . $class,
					),
					$block_content
				);
			}


		// Output

			return $block_content;

	} // /render_block__taxonomy

	/**
	 * Makes Page post type publicly queryable.
	 *
	 * Pages are no publicly queryable in WordPress by default.
	 * This prevents search form to narrow search results to Page
	 * post type only, so why we need to enable Page post type
	 * to be publicly queryable.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $args
	 *
	 * @return  array
	 */
	public static function make_page_publicly_queryable( array $args ): array {

		// Processing

			$args['publicly_queryable'] = true;


		// Output

			return $args;

	} // /make_page_publicly_queryable

}