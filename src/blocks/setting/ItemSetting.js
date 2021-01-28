/**
 * @WordPress dependencies
 */
import { memo } from '@wordpress/element';
import { TextControl } from '@wordpress/components';

/**
 * ItemPreview
 */
export default memo(({ postTitle, parsedMeta, updateMetadata }) => {
	// タイトル更新用関数
	const { editPost } = wp.data.dispatch('core/editor');

	return (
		<>
			<div className='__setting'>
				<TextControl
					label='検索キーワード'
					value={parsedMeta.keywords}
					onChange={(val) => {
						updateMetadata('keywords', val);
					}}
				/>
				<TextControl
					label='商品タイトル'
					value={postTitle}
					onChange={(newTitle) => {
						editPost({ title: newTitle });
						// setItemTitle(newTitle);
					}}
				/>
				<TextControl
					label='タイトル下に表示するテキスト'
					value={parsedMeta.info}
					onChange={(val) => {
						updateMetadata('info', val);
					}}
				/>

				<TextControl
					label='カスタムボタン用URL'
					value={parsedMeta.custom_btn_url}
					onChange={(val) => {
						updateMetadata('custom_btn_url', val);
					}}
				/>
				<TextControl
					label='カスタムボタン用テキスト'
					value={parsedMeta.custom_btn_text}
					onChange={(val) => {
						updateMetadata('custom_btn_text', val);
					}}
				/>

				{/* <TextControl label='ASIN' value={parsedMeta.asin} disabled /> */}

				{/* -- 各ボタンの設定 -- */}
				{/* <TextControl
					label='Amazon商品詳細ページのURL'
					value={parsedMeta.amazon_affi_url}
					onChange={(val) => {
						updateMetadata('amazon_affi_url', val);
					}}
				/> */}
				{/* <TextControl
					label='楽天商品詳細ページのURL'
					value={parsedMeta.rakuten_detail_url}
					onChange={(val) => {
						updateMetadata('rakuten_detail_url', val);
					}}
				/>
				<div className='__note'>
					アフィリエイトリンクではなく、通常の商品ページのURLを入力してください。
				</div> */}
				{/* <TextControl
					label='商品画像 '
					value={parsedMeta.image_url}
					onChange={(val) => {
						updateMetadata('image_url', val);
					}}
				/> */}
			</div>
		</>
	);
});
