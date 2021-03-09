/**
 * @WordPress dependencies
 */
import { memo } from '@wordpress/element';
import { TextControl, CheckboxControl } from '@wordpress/components';

/**
 * ItemPreview
 */
export default memo(({ postTitle, parsedMeta, updateMetadata }) => {
	// タイトル更新用関数
	const { editPost } = wp.data.dispatch('core/editor');

	return (
		<>
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
			<TextControl
				label='カスタムボタン2用URL'
				value={parsedMeta.custom_btn_url_2}
				onChange={(val) => {
					updateMetadata('custom_btn_url_2', val);
				}}
			/>
			<TextControl
				label='カスタムボタン2用テキスト'
				value={parsedMeta.custom_btn_text_2}
				onChange={(val) => {
					updateMetadata('custom_btn_text_2', val);
				}}
			/>
			{/* <CheckboxControl
				label='価格情報を表示しない'
				checked={parsedMeta.hidePrice}
				onChange={(checked) => {
					updateMetadata('hidePrice', checked);
				}}
			/> */}
		</>
	);
});
