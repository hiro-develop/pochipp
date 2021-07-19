/**
 * @WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { useState, useCallback } from '@wordpress/element';
import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, BlockControls, InspectorControls } from '@wordpress/block-editor';
import { Button, ToolbarGroup, ToolbarButton, TextControl, CheckboxControl, SelectControl, PanelBody } from '@wordpress/components';
import { Icon, search, upload, edit } from '@wordpress/icons';

/**
 * @Internal dependencies
 */
import metadata from './block.json';
import iconReSearch from './icon_re_search.js';
import { sendUpdateAjax } from '@blocks/helper';
import BtnSettingTable from '@blocks/components/BtnSettingTable';
import ExPanel from './components/ExPanel.js';

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
			custom_btn_text_2: undefined,
			custom_btn_url_2: undefined,
		});
	} else {
		// pid: undefined,
		updateBlockAttributes(clientId, itemData);
	}
};

/**
 * 設定項目
 */
const btnLayoutsPC = [
	{ value: '', label: 'ポチップ設定のまま' },
	{ value: 'fit', label: '自動フィット' },
	{ value: 'text', label: 'テキストに応じる' },
	{ value: '3', label: '3列幅' },
	{ value: '2', label: '2列幅' },
];
const btnLayoutsSP = [
	{ value: '', label: 'ポチップ設定のまま' },
	{ value: '1', label: '1列幅' },
	{ value: '2', label: '2列幅' },
];

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
	// styles: [
	// 	{ name: 'default', label: 'デフォルト', isDefault: true },
	// 	{ name: 'vrtcl', label: 'PCでも縦並び' },
	// ],
	attributes: metadata.attributes,
	edit: ({ attributes, setAttributes, clientId, isSelected }) => {
		const { pid, title, info, isCount, cvKey } = attributes;

		// 投稿IDを取得
		const postId = useSelect((select) => select('core/editor').getCurrentPostId(), []);

		// ステート
		const [isRegistering, setIsRegistering] = useState(false);

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
		const openThickbox = useCallback(
			(only = '') => {
				let url = 'media-upload.php?type=pochipp';
				url += `&at=editor`;
				url += `&blockid=${clientId}`;
				url += `&postid=${postId}`;
				if (only) {
					url += `&tab=pochipp_search_${only}`;
					url += `&only=${only}`;
				} else {
					url += `&tab=pochipp_search_registerd`;
				}
				url += '&TB_iframe=true'; // これは最後に。

				window.tb_show('商品検索', url);

				const tbWindow = document.querySelector('#TB_window');
				if (tbWindow) {
					tbWindow.classList.add('by-pochipp');
				}
			},
			[postId, clientId]
		);

		// 商品データを登録する
		const registerPochippData = useCallback(() => {
			// console.log('registerPochippData');
			// console.log(attributes);
			// console.log(JSON.stringify(attributes));
			const params = new URLSearchParams();
			params.append('action', 'pochipp_registerd_by_block');
			params.append('attributes', JSON.stringify(attributes));
			params.append('clientId', clientId);

			setIsRegistering(true);

			const doneFunc = (response) => {
				// console.log('registerPochippData: response', response);
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
						custom_btn_text_2: undefined,
						custom_btn_url_2: undefined,
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

		// pochipp編集ページ
		const adminUrl = window.pchppVars.adminUrl || '';
		const itemEditUrl = pid ? `${adminUrl}/post.php?post=${pid}&action=edit` : '';

		let branchContent = null;
		if (isSelected && hasRegisterdItem) {
			// ポチップ登録済みのブロックにのみ表示
			branchContent = (
				<div className='__bigBtnWrap' style={{ padding: '16px 0 8px' }}>
					<Button
						icon={<Icon icon={edit} />}
						className='__bigBtn'
						isPrimary={true}
						onClick={() => {
							window.open(itemEditUrl);
						}}
					>
						ポチップ管理画面で編集する
					</Button>
				</div>
			);
		} else if (isSelected && hasItem && !hasRegisterdItem) {
			// ポチップ未登録のブロックにのみ表示
			branchContent = (
				<>
					<BtnSettingTable
						attrs={attributes}
						openThickbox={openThickbox}
						deleteAmazon={() => {
							setAttributes({
								asin: undefined,
								amazon_affi_url: undefined,
							});
						}}
						deleteRakuten={() => {
							setAttributes({
								itemcode: undefined,
								rakuten_detail_url: undefined,
							});
						}}
						deleteYahoo={() => {
							setAttributes({
								yahoo_itemcode: undefined,
								seller_id: undefined,
								is_paypay: undefined,
								yahoo_detail_url: undefined,
							});
						}}
					/>
					<div className='__bigBtnWrap' style={{ padding: '0 0 8px' }}>
						<Button
							icon={<Icon icon={upload} />}
							className='__bigBtn'
							isPrimary={true}
							onClick={() => {
								if (window.confirm('本当に登録しますか？')) {
									registerPochippData();
								}
							}}
						>
							商品データをポチップ管理画面に登録する
						</Button>
					</div>
				</>
			);
		}

		// 現在のクラス
		const nowClass = attributes.className || '';
		return (
			<>
				{hasItem && (
					<BlockControls>
						<ToolbarGroup data-registering={isRegistering ? '1' : null}>
							<ToolbarButton
								className='thickbox'
								label='商品を再検索'
								icon={<Icon icon={iconReSearch} />}
								onClick={() => {
									openThickbox();
								}}
							/>
						</ToolbarGroup>
					</BlockControls>
				)}
				{hasItem && (
					<InspectorControls>
						<PanelBody title='スタイル'>
							<CheckboxControl
								label='全デバイスで縦並び表示にする'
								checked={-1 !== nowClass.indexOf('is-vrtcl')}
								onChange={(checked) => {
									let newClass = '';
									if (checked) {
										newClass = nowClass + ' is-vrtcl';
									} else {
										newClass = nowClass.replace('is-vrtcl', '');
									}
									setAttributes({ className: newClass.trim() });
								}}
							/>
							{hasRegisterdItem ? (
								<p>
									ボタンレイアウトを
									<a href={itemEditUrl} target='_blank' rel='noreferrer noopener'>
										ポチップ管理ページ
									</a>
									で商品ごとに設定できます。
								</p>
							) : (
								<>
									<SelectControl
										label='ボタン幅（PC）'
										value={attributes.btnLayoutPC}
										options={btnLayoutsPC}
										onChange={(val) => {
											setAttributes({ btnLayoutPC: val });
										}}
									/>
									<SelectControl
										label='ボタン幅（SP）'
										value={attributes.btnLayoutSP}
										options={btnLayoutsSP}
										onChange={(val) => {
											setAttributes({ btnLayoutSP: val });
										}}
									/>
								</>
							)}
						</PanelBody>
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
								label={hasRegisterdItem ? '商品タイトルを上書き' : '商品タイトル'}
								value={title}
								onChange={(newText) => {
									setAttributes({ title: newText });
								}}
							/>
							<TextControl
								label={hasRegisterdItem ? 'タイトル下テキストを上書き' : 'タイトル下テキスト'}
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
							{hasRegisterdItem ? (
								<>
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
									<CheckboxControl
										label='カスタムボタン2を非表示'
										className='pchpp-hideCheck'
										checked={attributes.hideCustom2}
										onChange={(checked) => {
											setAttributes({
												hideCustom2: checked,
											});
										}}
									/>
									<p>
										ボタンの内容は
										<a href={itemEditUrl} target='_blank' rel='noreferrer noopener'>
											ポチップ管理ページ
										</a>
										で編集できます。
									</p>
								</>
							) : (
								<>
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
									<TextControl
										label='カスタムボタン2のURL'
										value={attributes.custom_btn_url_2}
										onChange={(newText) => {
											setAttributes({
												custom_btn_url_2: newText,
											});
										}}
									/>
									<TextControl
										label='カスタムボタン2のテキスト'
										value={attributes.custom_btn_text_2}
										onChange={(newText) => {
											setAttributes({
												custom_btn_text_2: newText,
											});
										}}
									/>
								</>
							)}
						</PanelBody>
						<ExPanel {...{ clientId, isCount, cvKey, setAttributes }} />
					</InspectorControls>
				)}

				<div {...blockProps}>
					{!hasItem && (
						<Button
							icon={<Icon icon={search} />}
							className='__bigBtn thickbox'
							isPrimary={true}
							onClick={() => {
								openThickbox();
							}}
						>
							商品を検索
						</Button>
					)}
					<div className='__preview'>
						<ServerSideRender block={name} attributes={attributes} className={`components-disabled`} />
					</div>
					{hasItem && !hasRegisterdItem && <div className='__note'>※ ポチップ管理には未登録のブロックです。</div>}
					{branchContent}
				</div>
			</>
		);
	},

	save: () => {
		return null;
	},
});
