/**
 * @WordPress dependencies
 */
// import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useCallback, useMemo, useState } from '@wordpress/element';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, CheckboxControl } from '@wordpress/components';

/**
 * @Internal dependencies
 */
import metadata from './block.json';
import ItemPreview from './components/ItemPreview';
import ItemSetting from './components/ItemSetting';
import { SearchBtn, UpdateBtn } from './components/settingBtns';
import BtnSettingTable from '@blocks/components/BtnSettingTable';
import { getParsedMeta, setCustomFieldArea, sendUpdateAjax } from '@blocks/helper';

/**
 * metadata
 */
const blockName = 'pochipp-block';
const { apiVersion, name, category, keywords, supports } = metadata;

/* eslint no-alert: 0 */
/* eslint no-console: 0 */

/**
 * メタデータ更新処理
 * iframe 側からも呼び出せるようにグローバル化
 *
 * @param {Object} itemData 商品データ
 * @param {boolean} isMerge データをマージして更新するかどうか
 */
window.setItemMetaData = (itemData, isMerge) => {
	console.log('setItemMetaData:', itemData);

	// 使用するメソッドの準備
	const { editPost } = wp.data.dispatch('core/editor');
	const { getEditedPostAttribute } = wp.data.select('core/editor');

	// タイトル情報の処理
	if (itemData.title) {
		// 更新
		editPost({ title: itemData.title });

		// metaには保存しない
		delete itemData.title;
	}

	// metaの取得
	const oldMeta = getEditedPostAttribute('meta');

	let newItemData = {};
	if (isMerge) {
		const oldItemData = getParsedMeta(oldMeta.pochipp_data);
		newItemData = { ...oldItemData, ...itemData };
	} else {
		newItemData = itemData;
	}

	// 新しいデータをjson文字列化
	const newItemJson = JSON.stringify(newItemData);

	// metaの更新 （他のmetaデータを持つ場合を考慮し、'pochipp_data' だけ対象に上書き）
	editPost({ meta: { ...oldMeta, pochipp_data: newItemJson } });

	// metaの更新 : gutenberのバグ対処用
	setCustomFieldArea('pochipp_data', newItemJson);

	// ブロックのattributesを更新する
	// const { updateBlockAttributes } = wp.data.dispatch('core/block-editor');
	// updateBlockAttributes(clientId, {
	// 	metadata: JSON.stringify(itemData),
	// });
};

/**
 * ポチップ登録用のブロック
 */
registerBlockType(name, {
	apiVersion,
	title: '商品データ',
	icon: 'pets',
	category,
	keywords,
	supports,
	attributes: metadata.attributes,
	edit: ({ attributes, setAttributes, clientId }) => {
		const { meta } = attributes;

		const [isStickey, setIsStickey] = useState(false);

		// 投稿ID・投稿タイプを取得
		const { postId } = useSelect((select) => {
			return {
				postId: select('core/editor').getCurrentPostId(),
				// postType: select('core/editor').getCurrentPostType(),
			};
		}, []);

		// 投稿タイトルを取得
		// memo: getCurrentPostAttribute は編集時のデータは取得できない。 getEditedPostAttribute だとOK。
		const postTitle = useSelect((select) => select('core/editor').getEditedPostAttribute('title'), []);

		// メタデータを取得
		// const [meta, setMeta] = useEntityProp('postType', postType, 'meta');
		const parsedMeta = useMemo(() => getParsedMeta(meta), [meta]);
		// console.log(meta, parsedMeta);

		const updateMetadata = useCallback(
			(key, newVal) => {
				parsedMeta[key] = newVal;
				const newPochippMeta = JSON.stringify(parsedMeta);

				// meta更新
				// setMeta({ ...meta, pochipp_data: newPochippMeta });
				setAttributes({ meta: newPochippMeta });
				setCustomFieldArea('pochipp_data', newPochippMeta); // gutenberのバグにも対応
			},
			[parsedMeta]
		);

		// 商品検索
		const openThickbox = useCallback(
			(only = '') => {
				let url = 'media-upload.php?type=pochipp';
				url += `&at=setting`;
				// url += `&tab=pochipp_search_${type}`;
				url += `&blockid=${clientId}`;
				url += `&postid=${postId}`;
				if (only) {
					url += `&tab=pochipp_search_${only}`;
					url += `&only=${only}`;
				} else {
					url += `&tab=pochipp_search_amazon`;
				}
				url += '&TB_iframe=true'; //これは最後に。

				// #TB_window を開く
				window.tb_show('商品検索', url);

				// 開いた #TB_window を取得してクラス追加
				const tbWindow = document.querySelector('#TB_window');

				if (tbWindow) {
					tbWindow.classList.add('by-pochipp');
				}
			},
			[postId, clientId]
		);

		// meta情報
		const amazonAsin = parsedMeta.asin || '';
		const amazonAffiUrl = parsedMeta.amazon_affi_url || '';

		// 商品が検索された状態かどうか
		const searchedAt = parsedMeta.searched_at;
		const hasSearchedItem = !!searchedAt;

		// 商品データ更新用の itemCode
		let itemCode = '';

		// 情報更新ボタンを表示するかどうか
		let showUpdateBtn = false;
		if ('amazon' === searchedAt) {
			itemCode = amazonAsin || '';
			showUpdateBtn = !!(itemCode && amazonAffiUrl);
		} else if ('rakuten' === searchedAt) {
			itemCode = parsedMeta.itemcode || '';
			showUpdateBtn = !!itemCode;
		} else if ('yahoo' === searchedAt) {
			// itemCode = parsedMeta.yahoo_itemcode || '';
			// showUpdateBtn = !!itemCode;
		}

		// 商品データ更新処理
		const updateItemData = useCallback(() => {
			const params = new URLSearchParams();
			params.append('action', 'pochipp_update_data');
			params.append('itemcode', itemCode);
			params.append('keywords', parsedMeta.keywords);
			params.append('searched_at', searchedAt);

			const btns = document.querySelector('.pochipp-block--setting .__btns');
			btns.classList.add('-updating');

			const doneFunc = (response) => {
				const itemData = response.data;
				window.setItemMetaData(itemData, true);

				alert('更新が完了しました！');
				btns.classList.remove('-updating');
			};
			const failFunc = (err) => {
				alert('更新に失敗しました。');
				console.error(err);
				btns.classList.remove('-updating');
			};

			// ajax処理
			sendUpdateAjax(params, doneFunc, failFunc);
		}, [itemCode, parsedMeta]);

		return (
			<>
				<div
					{...useBlockProps({
						className: `${blockName}--setting`,
						'data-sticky': isStickey ? '1' : null,
					})}
				>
					<ItemPreview {...{ postTitle, parsedMeta }} />
					<div className='__settings'>
						<div className='__btns'>
							<SearchBtn onClick={openThickbox} text={hasSearchedItem ? '商品を再検索' : '商品を検索'} />
							{showUpdateBtn && <UpdateBtn onClick={updateItemData} />}
						</div>
						{hasSearchedItem && (
							<div className='__fields'>
								<div className='components-base-control'>
									<div className='components-base-control__label'>ボタンリンク先</div>
									<BtnSettingTable
										attrs={parsedMeta}
										openThickbox={openThickbox}
										deleteAmazon={() => {
											updateMetadata('asin', '');
											updateMetadata('amazon_affi_url', '');
										}}
										deleteRakuten={() => {
											updateMetadata('itemcode', '');
											updateMetadata('rakuten_detail_url', '');
										}}
										deleteYahoo={() => {
											updateMetadata('yahoo_itemcode', '');
											updateMetadata('seller_id', '');
											updateMetadata('is_paypay', '');
											updateMetadata('yahoo_detail_url', '');
										}}
									/>
								</div>
								<ItemSetting {...{ postTitle, parsedMeta, updateMetadata }} />
							</div>
						)}
						{/* <div>【開発用】データ確認</div>
					<div className='pochipp-block-dump'>
						{Object.keys(parsedMeta).map((metakey) => {
							return (
								<div key={metakey}>
									<code>{metakey}</code> :{' '}
									{String(parsedMeta[metakey])}
								</div>
							);
						})}
					</div> */}
					</div>
				</div>
				<InspectorControls>
					<PanelBody>
						<CheckboxControl
							label='プレビューを画面上部に固定表示'
							checked={isStickey}
							onChange={(checked) => {
								setIsStickey(checked);
							}}
						/>
					</PanelBody>
				</InspectorControls>
			</>
		);
	},

	save: () => {
		return null;
	},
});
