/**
 * @WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { registerBlockType } from '@wordpress/blocks';
import {
	RichText,
	// InnerBlocks,
	InspectorControls,
	BlockControls,
	// BlockIcon,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	BaseControl,
	Button,
	ButtonGroup,
	// Tooltip,
	TextareaControl,
	ToolbarButton,
	ToolbarGroup,
	Popover,
} from '@wordpress/components';

import { RawHTML } from '@wordpress/element';

/**
 * External dependencies
 */
import classnames from 'classnames';


// window.set_block_data = (data) => {
// 	console.log( data );
// }

/**
 * Pochipp
 */
registerBlockType('pochipp/linkbox', {
	title: 'ポチップ',
	icon: 'external',
	category: 'design',
	keywords: ['pochipp', 'linkbox'],
	// parent: ['arkhe-block/header-bar'],
	supports: {
		className: false,
		customClassName: false,
		multiple: false,
		reusable: false,
		html: false,
	},
	attributes: {
		amazonUrl: {
			type: 'string',
			default: '',
		},
	},
	edit: (props) => {

		const { attributes, clientId } = props;

		// 投稿タイプを取得
		const postType = useSelect(
			( select ) => select( 'core/editor' ).getCurrentPostType(),
			[]
		);
		console.log('postType:' + postType);

		// クリックデータを取得
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );
		console.log('test:', meta);
		if ( meta ) {
			const metaPochippData = meta.pochipp_data;
			let theMetaData = metaPochippData || '{}';
			theMetaData = JSON.parse( theMetaData );

			console.log('meta:', theMetaData);
		}



		return (
			<>
				{/* <InspectorControls></InspectorControls> */}
				<Button
					className="thickbox"
					isPrimary={ true }
					onClick={ () => {

						var url = 'media-upload.php?type=pochipp&tab=pochipp_search_amazon&cid=' + clientId + '&TB_iframe=true';
						tb_show('商品リンク変更', url);
					} }
				>
					商品検索
				</Button>
			</>
		);
	},

	save: (props) => {
		null;
	},
});
