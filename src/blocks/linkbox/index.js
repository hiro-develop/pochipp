/**
 * @WordPress dependencies
 */
// import { __ } from '@wordpress/i18n';
// import apiFetch from '@wordpress/api-fetch';
// import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useCallback } from '@wordpress/element';
import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';
import {
	useBlockProps,
	BlockControls,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	Button,
	ToolbarGroup,
	ToolbarButton,
	TextControl,
	// CheckboxControl,
	PanelBody,
} from '@wordpress/components';
import { Icon, search, rotateLeft } from '@wordpress/icons';

/**
 * @External dependencies
 */
// import classnames from 'classnames';

/**
 * @Internal dependencies
 */
// import ItemPreview from './ItemPreview';
import metadata from './block.json';

/**
 * metadata
 */
const blockName = 'pochipp-block';
const { apiVersion, name, category, keywords, supports } = metadata;

//
/**
 * iframe 側から呼び出すメソッド。商品選択時の処理。
 *
 * @param {Object} itemData 商品データ
 * @param {string} clientId ブロックID
 */
window.set_block_data_at_editor = (itemData, clientId) => {
	// console.log('itemData:', itemData);

	// ブロックのattributesを更新する
	const { updateBlockAttributes } = wp.data.dispatch('core/block-editor');
	if (itemData.post_id) {
		updateBlockAttributes(clientId, {
			pid: itemData.post_id,
		});
	} else {
		updateBlockAttributes(clientId, {
			title: itemData.title || '',
			keywords: itemData.keywords || '',
			searched_at: itemData.searched_at || '',
			asin: itemData.asin || '',
			itemcode: itemData.itemcode || '',
			info: itemData.info || '',
			amazon_detail_url: itemData.amazon_detail_url || '',
			rakuten_detail_url: itemData.rakuten_detail_url || '',
			l_image_url: itemData.l_image_url || '',
			m_image_url: itemData.m_image_url || '',
			s_image_url: itemData.s_image_url || '',
			price: itemData.price || '',
			price_at: itemData.price_at || '',
			affi_rate: itemData.affi_rate || '',
			review_score: itemData.review_score || '',
			pid: undefined,
			// metadata: JSON.stringify(itemData), // jsonにして保存
		});
	}
};

/**
 * ポチップ登録用のブロック
 */
registerBlockType(name, {
	apiVersion,
	title: 'ポチップ',
	icon: 'buddicons-activity',
	category,
	keywords,
	supports,
	attributes: metadata.attributes,
	edit: ({ attributes, setAttributes, clientId }) => {
		const { pid, title, info } = attributes;

		// apiFetch で meta取得 -> そんなことしなくていい
		// apiFetch({
		// 	path: '/pochipp/data',
		// 	method: 'POST',
		// 	data: {
		// 		pid: 69,
		// 	},
		// }).then((posts) => {
		// 	console.log('pochipp', posts);
		// });

		// 投稿IDを取得
		const postId = useSelect(
			(select) => select('core/editor').getCurrentPostId(),
			[]
		);

		// 商品セットされているか
		const hasItem = !!pid || !!title;

		// ブロックprops
		const blockProps = useBlockProps({
			className: blockName,
			'data-has-item': hasItem ? '1' : null,
		});

		// openThickbox
		const openThickbox = useCallback(() => {
			let url = 'media-upload.php?type=pochipp';
			url += `&at=editor`;
			url += `&tab=pochipp_search_amazon`;
			url += `&blockid=${clientId}`;
			url += `&postid=${postId}`;
			url += '&TB_iframe=true';

			window.tb_show('商品検索', url);

			const tbWindow = document.querySelector('#TB_window');
			if (tbWindow) {
				tbWindow.classList.add('by-pochipp');
			}
		}, [postId, clientId]);

		// memo: <RichText allowedFormats={[]} />

		return (
			<>
				{hasItem && (
					<BlockControls>
						<ToolbarGroup>
							<ToolbarButton
								className='thickbox _components-toolbar__control'
								label='商品を再検索'
								icon={<Icon icon={rotateLeft} />}
								onClick={openThickbox}
							/>
						</ToolbarGroup>
					</BlockControls>
				)}
				{hasItem && (
					<InspectorControls>
						<PanelBody title='設定'>
							<TextControl
								label='商品名'
								value={title}
								onChange={(newText) => {
									setAttributes({ title: newText });
								}}
							/>
							<TextControl
								label='補足情報'
								value={info}
								onChange={(newText) => {
									setAttributes({ info: newText });
								}}
							/>
							<TextControl
								label='Amazonリンク先URL'
								value={attributes.amazon_detail_url}
								onChange={(newText) => {
									setAttributes({
										amazon_detail_url: newText,
									});
								}}
							/>
						</PanelBody>
					</InspectorControls>
				)}

				<div {...blockProps}>
					{!hasItem && (
						<Button
							icon={<Icon icon={search} />}
							className={`${blockName}__searchBtn thickbox`}
							isPrimary={true}
							onClick={openThickbox}
						>
							商品を検索
						</Button>
					)}
					{/* <ItemPreview {...props} /> */}
					<div className={`${blockName}__preview`}>
						<ServerSideRender
							block={name}
							attributes={attributes}
							className={`components-disabled`}
						/>
					</div>
				</div>
			</>
		);
	},

	save: () => {
		return null;
	},
});
