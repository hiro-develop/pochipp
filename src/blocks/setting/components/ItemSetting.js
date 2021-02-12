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
		</>
	);
});
