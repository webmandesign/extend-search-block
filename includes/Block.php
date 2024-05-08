<?php
/**
 * Block.
 *
 * @package    Extend Search Block
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.1
 */

namespace WebManDesign\Block\Mod\Search;

use WP_HTML_Tag_Processor;

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
				add_filter( 'render_block', __CLASS__ . '::render_block__empty_search_term', 10, 2 );

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

			$post_types = get_post_types( array( 'exclude_from_search' => false ), 'objects' );
			$taxonomies = get_taxonomies( array( 'publicly_queryable' => true ), 'objects' );


		// Processing

			// Preparing JS object from post types array.

				/**
				 * Filters post types array.
				 *
				 * @since  1.0.0
				 *
				 * @param  array $post_types
				 */
				$post_types = (array) apply_filters( 'extend-search-block/post_types', $post_types );

				foreach ( $post_types as $name => $post_type ) {
					unset( $post_types[ $name ] );
					$label = $post_type->label;
					$post_types[ $label ] = '{label:"' . esc_js( $label ) . '",value:"' . esc_js( $name ) . '"}';
				}

				ksort( $post_types );

			// Preparing JS object from taxonomies array.

				/**
				 * Filters taxonomies array.
				 *
				 * @since  1.0.0
				 *
				 * @param  array $taxonomies
				 */
				$taxonomies = (array) apply_filters( 'extend-search-block/taxonomies', $taxonomies );

				foreach ( $taxonomies as $name => $taxonomy ) {
					unset( $taxonomies[ $name ] );

					$object_types = array_map(
						function( $object_type ) {
							return get_post_type_object( $object_type )->label;
						},
						(array) $taxonomy->object_type
					);

					$label  = $taxonomy->label;
					$label .= ' (' . implode( ', ', (array) $object_types ) . ')';

					$taxonomies[ $label ] = '{label:"' . esc_js( $label ) . '",value:"' . esc_js( $name ) . '"}';
				}

				ksort( $taxonomies );

			// Registering and enqueuing scripts.

				wp_register_script(
					$handle,
					EXTEND_SEARCH_BLOCK_URL . 'blocks/search/mods.js',
					array(
						'wp-hooks',
						'wp-element',
						'wp-compose',
						'wp-components',
						'wp-i18n',
						'wp-block-editor',
						'wp-polyfill',
					),
					'v' . EXTEND_SEARCH_BLOCK_VERSION
				);

				wp_add_inline_script(
					$handle,
					'var wmdExtendSearchBlock={'
					. 'postTypes:[' . implode( ',', $post_types ) . '],'
					. 'taxonomies:[' . implode( ',', $taxonomies ) . '],'
					.'};',
					'before'
				);

				wp_enqueue_script( $handle );

	} // /enqueue_scripts

	/**
	 * Enqueue block styles.
	 *
	 * @since    1.0.0
	 * @version  1.0.1
	 *
	 * @return  void
	 */
	public static function enqueue_styles() {

		// Processing

			ob_start();
			include_once EXTEND_SEARCH_BLOCK_PATH . 'blocks/search/block.css';
			$css = ob_get_clean();

			wp_add_inline_style(
				'wp-block-library',
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
	 * @since    1.0.0
	 * @version  1.0.1
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

				// Get dropdown fields HTML.
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

				// Modifying new fields.

					$dropdowns     = new WP_HTML_Tag_Processor( implode( '', $fields ) );
					$count         = count( $fields );
					$inline_styles = styles_for_block_core_search( $block['attrs'] );
					$inline_styles = str_replace( [ ' style="', 'style="', '"' ], '', $inline_styles['input'] );
					$classes       = array(
						get_border_color_classes_for_block_core_search( $block['attrs'] ),
						get_typography_classes_for_block_core_search( $block['attrs'] ),
					);

					while ( $count > 0 ) {

						$dropdowns->next_tag( 'select' );
						$dropdowns->add_class( implode( ' ', $classes ) );
						$dropdowns->set_attribute( 'style', $inline_styles );

						--$count;
					}

					$fields = $dropdowns->get_updated_html();

				// Add new fields to search form.

					$seach = array(
						'wp-block-search__inside-wrapper',
						// Just in case, we need both of these:
						'<input type="search"',
						'<input class="wp-block-search__input',
					);

					$block_content = str_replace(
						$seach,
						array(
							$seach[0] . ' has-dropdown',
							$fields . $seach[1],
							$fields . $seach[2],
						),
						$block_content
					);
			}


		// Output

			return $block_content;

	} // /render_block__taxonomy

	/**
	 * Block output modification: Allow empty search term.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $block_content  The rendered content. Default null.
	 * @param  array  $block          The block being rendered.
	 *
	 * @return  string
	 */
	public static function render_block__empty_search_term( string $block_content, array $block ): string {

		// Processing

			if ( 'core/search' === $block['blockName'] ) {

				$block_content = str_replace(
					' required',
					' data-required',
					$block_content
				);
			}


		// Output

			return $block_content;

	} // /render_block__empty_search_term

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
