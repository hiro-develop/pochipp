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
					label='商品名'
					value={postTitle}
					onChange={(newTitle) => {
						editPost({ title: newTitle });
						// setItemTitle(newTitle);
					}}
				/>
				<TextControl
					label='キーワード'
					value={parsedMeta.keywords}
					onChange={(val) => {
						updateMetadata('keywords', val);
					}}
				/>
				<TextControl
					label='商品名の下に表示するテキスト'
					value={parsedMeta.info}
					onChange={(val) => {
						updateMetadata('info', val);
					}}
				/>
				{/* <TextControl label='ASIN' value={parsedMeta.asin} disabled /> */}

				{/* -- 各ボタンの設定 -- */}
				<TextControl
					label='Amazon商品詳細ページのURL'
					value={parsedMeta.amazon_detail_url}
					onChange={(val) => {
						updateMetadata('amazon_detail_url', val);
					}}
				/>
				<div className='__note'>
					{/* <code>https://www.amazon.co.jp/dp/商品のasin</code>
					の形式で入力してください。 */}
					アフィリエイトリンクではなく、通常の商品ページのURLを入力してください。
				</div>
				<TextControl
					label='楽天商品詳細ページのURL'
					value={parsedMeta.rakuten_detail_url}
					onChange={(val) => {
						updateMetadata('rakuten_detail_url', val);
					}}
				/>
				<div className='__note'>
					アフィリエイトリンクではなく、通常の商品ページのURLを入力してください。
				</div>

				<TextControl
					label='商品画像 ( Lサイズ )'
					value={parsedMeta.l_image_url}
					onChange={(val) => {
						updateMetadata('l_image_url', val);
					}}
				/>
				<TextControl
					label='商品画像 ( Mサイズ )'
					value={parsedMeta.m_image_url}
					onChange={(val) => {
						updateMetadata('m_image_url', val);
					}}
				/>
				<TextControl
					label='商品画像 ( Sサイズ )'
					value={parsedMeta.s_image_url}
					onChange={(val) => {
						updateMetadata('s_image_url', val);
					}}
				/>
			</div>
		</>
	);
});
