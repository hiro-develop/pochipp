/**
 * @WordPress dependencies
 */
// import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { registerBlockType } from '@wordpress/blocks';
import { Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useMemo } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';
import {
	// BlockControls,
	// RichText,
	// AlignmentToolbar,
	// InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * @External dependencies
 */
// import classnames from 'classnames';

/**
 * @Internal dependencies
 */
import ItemPreview from './ItemPreview';
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
 * @param {string} itemTitle 商品タイトル
 * @param {Object} itemData 商品データ
 * @param {string} clientId ブロックID
 */
window.set_block_data = (itemTitle, itemData, clientId) => {
	console.log('itemData:', itemData);

	// ブロックのattributesを更新する
	const { updateBlockAttributes } = wp.data.dispatch('core/block-editor');
	updateBlockAttributes(clientId, {
		title: itemTitle,
		pid: itemData.post_id || undefined,
		// metadata: JSON.stringify(itemData), // jsonにして保存
	});
};

/**
 * JSONのパース
 *
 * @param {string} data メタデータ(JSON形式)
 * @return {Array} parsed 配列に変換したメタデータ
 */
const getParsedMeta = (data) => {
	try {
		const parsed = JSON.parse(data);
		return parsed;
	} catch (ex) {
		return [];
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
	edit: ({ attributes, clientId }) => {
		const { pid } = attributes;

		// const metadata = useMemo(() => {
		// 	// metadata = pid;

		// const { getEditedPostAttribute } = wp.data.select('core/editor');
		// const meta = getEditedEntityRecord('meta').my_meta_key;
		// console.log(meta);

		// 	// getEntityRecord;

		// 	return '';
		// }, [pid]);

		// const metatest = wp.data
		// 	.select('core')
		// 	.getEditedEntityRecord('postData', 'meta', 69);

		// console.log(metatest);

		// const { getEditedPostAttribute } = wp.data.select('core/editor');
		// const meta = getEditedPostAttribute('meta');
		// console.log(meta);

		// const pid = 69;

		apiFetch({
			path: '/pochipp/data',
			method: 'POST',
			data: {
				pid: 69,
			},
		}).then((posts) => {
			console.log('pochipp', posts);
		});

		const metadata = '';

		// apiFetch({
		// 	path: '/pochipp/test',
		// 	method: 'GET',
		// 	data: {
		// 		pid: 69,
		// 	},
		// }).then((res) => {
		// 	// if (res) {}
		// 	console.log('res', res);
		// 	// metadata = res;
		// });

		// 投稿IDを取得
		const postId = useSelect(
			(select) => select('core/editor').getCurrentPostId(),
			[]
		);

		// 投稿タイプを取得
		// const postType = useSelect(
		// 	(select) => select('core/editor').getCurrentPostType(),
		// 	[]
		// );

		// ブロックprops
		const blockProps = useBlockProps({
			className: blockName,
		});

		return (
			<>
				{/* <div>attr:{attributes.metadata || 'none'}</div> */}
				{/* <div>meta data:{meta.pochipp_data || 'empty'}</div> */}
				<div {...blockProps}>
					<Button
						className='thickbox'
						isPrimary={true}
						onClick={() => {
							let url = 'media-upload.php?type=pochipp';
							url += `&at=editor`;
							url += `&tab=pochipp_search_amazon`;
							url += `&blockid=${clientId}`;
							url += `&postid=${postId}`;
							url += '&TB_iframe=true';

							window.tb_show('商品検索', url);

							const tbWindow = document.querySelector(
								'#TB_window'
							);
							if (tbWindow) {
								tbWindow.classList.add('by-pochipp');
							}
						}}
					>
						商品検索
					</Button>
					{/* <ItemPreview {...props} /> */}
					<ServerSideRender
						block={name}
						attributes={attributes}
						className={`${blockName}__preview`}
					/>
				</div>
			</>
		);
	},

	save: () => {
		return null;
	},
});
