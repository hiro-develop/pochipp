/**
 * @WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { registerBlockType } from '@wordpress/blocks';
// import {
// 	RichText,
// 	InnerBlocks,
// 	InspectorControls,
// 	BlockControls,
// 	BlockIcon,
// } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

// import { RawHTML } from '@wordpress/element';

/**
 * External dependencies
 */
// import classnames from 'classnames';

//
/**
 * iframe 側から呼び出すメソッド
 *
 * @param {*} data 商品データ
 * @param {*} clientId ブロックID
 */
window.set_block_data = (data, clientId) => {
	// console.log('data:', data);

	// ブロックのattributes更新
	const { updateBlockAttributes } = wp.data.dispatch('core/block-editor');
	updateBlockAttributes(clientId, {
		metadata: JSON.stringify(data),
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
registerBlockType('pochipp/setting', {
	title: 'ポチップ登録',
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
		metadata: {
			type: 'string',
			default: '',
		},
	},
	edit: (props) => {
		const { attributes, clientId } = props;

		// 投稿IDを取得
		const postId = useSelect(
			(select) => select('core/editor').getCurrentPostId(),
			[]
		);

		// 投稿タイプを取得
		const postType = useSelect(
			(select) => select('core/editor').getCurrentPostType(),
			[]
		);

		// 「カスタムフィールド」の値をセットする処理
		const setCustomFieldArea = (metaKey, metaVal) => {
			const customField = document.querySelector('#postcustomstuff');
			if (null === customField) return;

			const keyInput = customField.querySelector(
				`input[value="${metaKey}"]`
			);
			if (null === keyInput) return;

			const nextTd = keyInput.parentNode.nextElementSibling;
			if (null === nextTd) return;

			const textarea = nextTd.querySelector('textarea');
			if (null === textarea) return;

			textarea.value = metaVal;
		};

		// クリックデータを取得
		const [meta, setMeta] = useEntityProp('postType', postType, 'meta');

		if (!meta) {
			return <p>WordPressのバージョンを確認してください。</p>;
		}

		// attributesが更新されていればカスタムフィールドを更新
		if (meta.pochipp_data !== attributes.metadata) {
			setMeta({ ...meta, pochipp_data: attributes.metadata });
			setCustomFieldArea('pochipp_data', attributes.metadata); // gutenberのバグに対応
		}

		// console.log('attr: ' + attributes.metadata);
		// console.log('custom field: ' + meta.pochipp_data);

		// メタデータ(JSON)を配列に変換
		// pochi: パースするのは meta でも attributes でもどっちでも。 フロントのブロックは、マージさせたものをパースする？
		const parsedMeta = getParsedMeta(meta.pochipp_data);
		// console.log('parsedMeta', parsedMeta);

		return (
			<>
				{/* <div>attr:{attributes.metadata || 'none'}</div> */}
				<div>meta data:{meta.pochipp_data || 'empty'}</div>
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

						const tbWindow = document.querySelector('#TB_window');
						if (tbWindow) {
							tbWindow.classList.add('by-pochipp');
						}
					}}
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
