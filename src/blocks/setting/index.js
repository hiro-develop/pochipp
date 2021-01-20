/**
 * @WordPress dependencies
 */
// import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { registerBlockType } from '@wordpress/blocks';
import {
	// BlockControls,
	// RichText,
	// AlignmentToolbar,
	// InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';

import { Button } from '@wordpress/components';
// import { RawHTML } from '@wordpress/element';

/**
 * External dependencies
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

/**
 * iframe 側から呼び出すメソッド
 *
 * @param {Object} itemData 商品データ
 * @param {string} clientId ブロックID
 */
window.set_block_data = (itemData, clientId) => {
	// console.log('itemData:', itemData);

	const itemTitle = itemData.title || '';

	// タイトルの更新
	const { editPost } = wp.data.dispatch('core/editor');
	editPost({ title: itemTitle });

	// ブロックのattributesを更新する
	const { updateBlockAttributes } = wp.data.dispatch('core/block-editor');
	updateBlockAttributes(clientId, {
		metadata: JSON.stringify(itemData), // jsonにして保存
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
 * エディター下の「カスタムフィールド」の値を強制的にセットする処理
 */
const setCustomFieldArea = (metaKey, metaVal) => {
	const customField = document.querySelector('#postcustomstuff');
	if (null === customField) return;

	const keyInput = customField.querySelector(`input[value="${metaKey}"]`);
	if (null === keyInput) return;

	const nextTd = keyInput.parentNode.nextElementSibling;
	if (null === nextTd) return;

	const textarea = nextTd.querySelector('textarea');
	if (null === textarea) return;

	textarea.value = metaVal;
};

/**
 * ポチップ登録用のブロック
 */
registerBlockType(name, {
	apiVersion,
	title: '商品データ',
	icon: 'clipboard',
	category,
	keywords,
	supports,
	attributes: metadata.attributes,
	edit: ({ attributes, setAttributes, clientId }) => {
		// 投稿ID・投稿タイプを取得
		const { postId, postType } = useSelect((select) => {
			return {
				postId: select('core/editor').getCurrentPostId(),
				postType: select('core/editor').getCurrentPostType(),
			};
		}, []);

		// メタデータを取得
		const [meta, setMeta] = useEntityProp('postType', postType, 'meta');

		if (!meta) {
			return <p>WordPressのバージョンを確認してください。</p>;
		}

		// attributesが更新されていればカスタムフィールドを更新
		if (meta.pochipp_data !== attributes.metadata) {
			setMeta({ ...meta, pochipp_data: attributes.metadata });
			setCustomFieldArea('pochipp_data', attributes.metadata); // gutenberのバグに対応
		}

		// console.log('attributes の metadata: ' + attributes.metadata);
		// console.log('カスタムフィールドに保存中のデータ ' + meta.pochipp_data);

		// メタデータ(JSON)を配列に変換
		// memo: パースするのは meta でも attributes でもどっちでも。 フロントのブロックは、マージさせたものをパースする？
		const parsedMeta = getParsedMeta(meta.pochipp_data);

		// タイトル更新用関数
		const { editPost } = wp.data.dispatch('core/editor');

		// ブロックprops
		const blockProps = useBlockProps({
			className: `${blockName}--setting`,
		});

		return (
			<>
				<div {...blockProps}>
					{/* <div>attr:{attributes.metadata || 'none'}</div> */}
					{/* <div>meta data:{meta.pochipp_data || 'empty'}</div> */}
					<Button
						className='thickbox'
						isPrimary={true}
						onClick={() => {
							let url = 'media-upload.php?type=pochipp';
							url += `&at=setting`;
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
					<ItemPreview
						{...{ attributes, setAttributes, parsedMeta, editPost }}
					/>
				</div>
			</>
		);
	},

	save: () => {
		return null;
	},
});
