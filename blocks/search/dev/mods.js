/**
 * Block editor script.
 *
 * @package    Extend Search Block
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

( ( wp ) => {
	'use strict';

	// Variables

		const
			{ __ }        = wp.i18n,
			{ addFilter } = wp.hooks,
			Editor        = wp.blockEditor,
			Comp          = wp.components,
			Fragment      = wp.element.Fragment,
			Element       = wp.element.createElement,
			HOComponent   = wp.compose.createHigherOrderComponent;


	// Processing

		addFilter(
			'blocks.registerBlockType',
			'extend-search-block/mods/add-attributes',
			( settings, name ) => {

				// Requirements check

					if ( 'core/search' !== name ) {
						return settings;
					}


				// Processing

					settings.attributes.postType = {
						type    : 'array',
						default : [],
					};

					settings.attributes.taxonomy = {
						type    : 'array',
						default : [],
					};


				// Output

					return settings;

			}
		);

		addFilter(
			'editor.BlockEdit',
			'extend-search-block/mods/add-controls',
			( BlockEdit ) => {

				const withInspectorControls = HOComponent(
					( BlockEdit ) => {

						return ( props ) => {

							if ( 'core/search' !== props.name ) {
								return Element( BlockEdit, props );
							}

							const
								// { info, hooks }   = wmdActionHookBlock,
								{ postType, taxonomy } = props.attributes;

							return Element( Fragment, {},
								Element( BlockEdit, props ),
								Element( Editor.InspectorControls, {},
									Element( Comp.PanelBody,
										{
											title       : __( 'Search modifiers', 'extend-search-block' ),
											initialOpen : false,
										},
										Element( 'p', { className: 'description' }, __( 'Keep pressing CTRL (Windows) or CMD (Mac) key while selecting or deselecting item(s) in the fields below.', 'extend-search-block' ) ),
										Element( 'hr' ),
										Element( Comp.SelectControl,
											{
												multiple : true,
												label    : __( 'Post type', 'extend-search-block' ),
												help     : __( 'Narrows search results to selected post type(s) only.', 'extend-search-block' ),
												value    : postType,
												onChange : ( newValue ) => props.setAttributes( { postType: newValue } ),
												options  : [ // @TODO
													{ label: __( 'Post', 'extend-search-block' ), value: 'post' },
													{ label: __( 'Page', 'extend-search-block' ), value: 'page' },
												],
											}
										),
										Element( Comp.SelectControl,
											{
												multiple : true,
												label    : __( 'Taxonomy', 'extend-search-block' ),
												help     : __( 'Adds selected taxonomy dropdown to the search form on the front-end of your website.', 'extend-search-block' ) + ' ' + __( 'User will be able to narrow down the search results to specific taxonomy term only.', 'extend-search-block' ),
												value    : taxonomy,
												onChange : ( newValue ) => props.setAttributes( { taxonomy: newValue } ),
												options  : [ // @TODO
													{ label: __( 'Tags', 'extend-search-block' ), value: 'post_tag' },
													{ label: __( 'Categories', 'extend-search-block' ), value: 'category' },
												],
											}
										)
									)
								)
							);

						};
					}
				);

				return withInspectorControls( BlockEdit );
			}
		);

} )( window.wp );
