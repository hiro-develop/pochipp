/**
 * @WordPress dependencies
 */
// import { __ } from '@wordpress/i18n';
// import apiFetch from '@wordpress/api-fetch';
// import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useState, useCallback } from '@wordpress/element';
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
	CheckboxControl,
	PanelBody,
} from '@wordpress/components';
import { Icon, search, rotateLeft, upload } from '@wordpress/icons';

/**
 * @External dependencies
 */
// import classnames from 'classnames';

/**
 * @Internal dependencies
 */
import metadata from './block.json';
import iconReSearch from './icon_re_search.js';
import {
	// getParsedMeta,
	// setCustomFieldArea,
	sendUpdateAjax,
} from '@blocks/helper';

/**
 * metadata
 */
const blockName = 'pochipp-block';
const { apiVersion, name, category, keywords, supports } = metadata;

/* eslint no-alert: 0 */
/* eslint no-console: 0 */

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
			title: undefined,
			searched_at: undefined,
			keywords: undefined,
			asin: undefined,
			itemcode: undefined,
			yahoo_itemcode: undefined,
			seller_id: undefined,
			image_url: undefined,
			info: undefined,
			price: undefined,
			price_at: undefined,
			amazon_affi_url: undefined,
			rakuten_detail_url: undefined,
			yahoo_detail_url: undefined,
			is_paypay: undefined,
			custom_btn_text: undefined,
			custom_btn_url: undefined,
		});
	} else {
		updateBlockAttributes(clientId, {
			pid: undefined,
			title: itemData.title || undefined,
			keywords: itemData.keywords || undefined,
			searched_at: itemData.searched_at || undefined,
			asin: itemData.asin || undefined,
			itemcode: itemData.itemcode || undefined,
			yahoo_itemcode: itemData.yahoo_itemcode || undefined,
			seller_id: itemData.seller_id || undefined,
			info: itemData.info || undefined,
			image_url: itemData.image_url || undefined,
			price: itemData.price + '' || undefined,
			price_at: itemData.price_at || undefined,
			amazon_affi_url: itemData.amazon_affi_url || undefined,
			rakuten_detail_url: itemData.rakuten_detail_url || undefined,
			yahoo_detail_url: itemData.yahoo_detail_url || undefined,
			is_paypay: itemData.is_paypay || undefined,
		});
	}
};

/**
 * ポチップ登録用のブロック
 */
registerBlockType(name, {
	apiVersion,
	title: 'ポチップ',
	icon: 'pets',
	category,
	keywords,
	supports,
	attributes: metadata.attributes,
	edit: ({ attributes, setAttributes, clientId }) => {
		const { pid, title, info } = attributes;
		// console.log('attributes', attributes);

		// ステート
		const [isRegistering, setIsRegistering] = useState(false);

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
		const hasRegisterdItem = !!pid;
		const hasItem = !!pid || !!title;

		// ブロックprops
		const blockProps = useBlockProps({
			className: blockName,
			'data-has-item': hasItem ? '1' : null,
			'data-registering': isRegistering ? '1' : null,
		});

		// openThickbox
		const openThickbox = useCallback(() => {
			let url = 'media-upload.php?type=pochipp';
			url += `&at=editor`;
			url += `&tab=pochipp_search_registerd`;
			url += `&blockid=${clientId}`;
			url += `&postid=${postId}`;
			url += '&TB_iframe=true';

			window.tb_show('商品検索', url);

			const tbWindow = document.querySelector('#TB_window');
			if (tbWindow) {
				tbWindow.classList.add('by-pochipp');
			}
		}, [postId, clientId]);

		// 商品データを登録する
		const registerPochippData = useCallback(() => {
			console.log('registerPochippData');

			console.log(attributes);
			console.log(JSON.stringify(attributes));
			const params = new URLSearchParams();
			params.append('action', 'pochipp_registerd_by_block');
			params.append('attributes', JSON.stringify(attributes));
			params.append('clientId', clientId);

			setIsRegistering(true);

			const doneFunc = (response) => {
				console.log('registerPochippData: response', response);
				const newPid = response.pid;
				if (newPid) {
					setAttributes({
						pid: newPid,
						title: undefined,
						searched_at: undefined,
						keywords: undefined,
						asin: undefined,
						itemcode: undefined,
						yahoo_itemcode: undefined,
						seller_id: undefined,
						image_url: undefined,
						info: undefined,
						price: undefined,
						price_at: undefined,
						amazon_affi_url: undefined,
						rakuten_detail_url: undefined,
						yahoo_detail_url: undefined,
						is_paypay: undefined,
						custom_btn_text: undefined,
						custom_btn_url: undefined,
					});
					alert('登録が完了しました！');
				} else {
					alert('エラー : 新規IDが取得できませんでした。');
				}
				setIsRegistering(false);
			};
			const failFunc = (err) => {
				alert('登録に失敗しました。');
				console.error(err);
				setIsRegistering(false);
			};

			// ajax処理
			sendUpdateAjax(params, doneFunc, failFunc);
		}, [clientId, attributes, setAttributes]);

		// memo: <RichText allowedFormats={[]} />

		return (
			<>
				{hasItem && (
					<BlockControls>
						<ToolbarGroup
							data-registering={isRegistering ? '1' : null}
						>
							<ToolbarButton
								className='thickbox'
								label='商品を再検索'
								icon={<Icon icon={iconReSearch} />}
								onClick={openThickbox}
							/>
							{!hasRegisterdItem && (
								<ToolbarButton
									className=''
									label='商品データをポチップ管理画面に登録する'
									icon={<Icon icon={upload} />}
									onClick={registerPochippData}
								/>
							)}
						</ToolbarGroup>
					</BlockControls>
				)}
				{hasItem && (
					<InspectorControls>
						{!hasRegisterdItem && (
							<PanelBody title='検索キーワード'>
								<TextControl
									value={attributes.keywords}
									onChange={(newText) => {
										setAttributes({ keywords: newText });
									}}
								/>
							</PanelBody>
						)}
						<PanelBody title='情報の表示設定'>
							<TextControl
								label={
									hasRegisterdItem
										? '商品タイトルを上書き'
										: '商品タイトル'
								}
								value={title}
								onChange={(newText) => {
									setAttributes({ title: newText });
								}}
							/>
							<TextControl
								label={
									hasRegisterdItem
										? 'タイトル下テキストを上書き'
										: 'タイトル下テキスト'
								}
								value={info}
								onChange={(newText) => {
									setAttributes({ info: newText });
								}}
							/>
							<CheckboxControl
								label='補足情報を非表示'
								className='pchpp-hideCheck'
								checked={attributes.hideInfo}
								onChange={(checked) => {
									setAttributes({
										hideInfo: checked,
									});
								}}
							/>
							<CheckboxControl
								label='価格を非表示'
								className='pchpp-hideCheck'
								checked={attributes.hidePrice}
								onChange={(checked) => {
									setAttributes({
										hidePrice: checked,
									});
								}}
							/>
							<CheckboxControl
								label='Amazonボタンを非表示'
								className='pchpp-hideCheck'
								checked={attributes.hideAmazon}
								onChange={(checked) => {
									setAttributes({
										hideAmazon: checked,
									});
								}}
							/>
							<CheckboxControl
								label='楽天ボタンを非表示'
								className='pchpp-hideCheck'
								checked={attributes.hideRakuten}
								onChange={(checked) => {
									setAttributes({
										hideRakuten: checked,
									});
								}}
							/>
							<CheckboxControl
								label='Yahooボタンを非表示'
								className='pchpp-hideCheck'
								checked={attributes.hideYahoo}
								onChange={(checked) => {
									setAttributes({
										hideYahoo: checked,
									});
								}}
							/>
						</PanelBody>
						<PanelBody title='カスタムボタン設定'>
							<TextControl
								label='カスタムボタンのURL'
								value={attributes.custom_btn_url}
								onChange={(newText) => {
									setAttributes({
										custom_btn_url: newText,
									});
								}}
							/>
							<TextControl
								label='カスタムボタンのテキスト'
								value={attributes.custom_btn_text}
								onChange={(newText) => {
									setAttributes({
										custom_btn_text: newText,
									});
								}}
							/>
							<CheckboxControl
								label='カスタムボタンを非表示'
								className='pchpp-hideCheck'
								checked={attributes.hideCustom}
								onChange={(checked) => {
									setAttributes({
										hideCustom: checked,
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
					<div className={`${blockName}__preview`}>
						<ServerSideRender
							block={name}
							attributes={attributes}
							className={`components-disabled`}
						/>
					</div>
					{hasItem && !hasRegisterdItem && (
						<div className={`${blockName}__note`}>
							※ ポチップ管理には未登録のブロックです。
						</div>
					)}
				</div>
			</>
		);
	},

	save: () => {
		return null;
	},
});
