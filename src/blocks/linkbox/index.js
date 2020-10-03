/**
 * @WordPress dependencies
 */
// import { __ } from '@wordpress/i18n';
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
import apiFetch from '@wordpress/api-fetch';
import {
	useMemo,
	//RawHTML
} from '@wordpress/element';

/**
 * External dependencies
 */
// import classnames from 'classnames';

/**
 * My compornent
 */
import ItemPreview from './ItemPreview';

//
/**
 * iframe 側から呼び出すメソッド
 *
 * @param {string} itemTitle 商品タイトル
 * @param {Object} itemData 商品データ
 * @param {string} clientId ブロックID
 */
window.set_block_data = (itemTitle, itemData, clientId) => {
	// console.log('itemData:', itemData);

	// タイトルの更新
	// itemTitle
	// const postTitle = document.querySelector(
	// 	'textarea.editor-post-title__input'
	// );
	// console.log(postTitle);

	// if (postTitle) {
	// 	postTitle.textContent = itemTitle;
	// }
	const { editPost } = wp.data.dispatch('core/editor');
	editPost({ title: itemTitle });

	// ブロックのattributesを更新する
	const { updateBlockAttributes } = wp.data.dispatch('core/block-editor');

	updateBlockAttributes(clientId, {
		title: itemTitle,
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
 * ポチップ登録用のブロック
 */
registerBlockType('pochipp/linkbox', {
	title: 'ポチップ',
	icon: 'buddicons-activity',
	category: 'design',
	keywords: ['pochipp', 'linkbox'],
	supports: {
		className: false,
		customClassName: false,
		multiple: false,
		reusable: false,
		html: false,
	},
	attributes: {
		pid: {
			type: 'string',
			default: '',
		},
		title: {
			type: 'string',
			default: '',
		},
		amazonBtn: {
			type: 'string',
			default: '',
		},
		rakutenBtn: {
			type: 'string',
			default: '',
		},
		customBtn: {
			type: 'string',
			default: '',
		},
		hidePrice: {
			type: 'boolean',
			default: false,
		},
		otherData: {
			type: 'string',
			default: '',
		},
		// metadata: {
		// 	type: 'string',
		// 	default: '',
		// },
	},
	edit: (props) => {
		const { attributes, clientId } = props;

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

		return (
			<>
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

						const tbWindow = document.querySelector('#TB_window');
						if (tbWindow) {
							tbWindow.classList.add('by-pochipp');
						}
					}}
				>
					商品検索
				</Button>
				{/* <ItemPreview {...props} /> */}
			</>
		);
	},

	save: (props) => {
		null;
	},
});
