import { TextControl } from '@wordpress/components';

/**
 * ItemPreview
 */
export default ({ attributes, setAttributes, parsedMeta, editPost }) => {
	if (1 > parsedMeta.length) {
		return (
			<div className='pochipp-block-preview'>
				<p>商品を選択してください</p>
			</div>
		);
	}

	const { brand, price } = parsedMeta;
	return (
		<>
			<div className='pochipp-block-preview'>
				<div className='pochipp-box'>
					<div className='pochipp-box__image'>
						<img src={parsedMeta.m_image_url} alt='' />
					</div>
					<div className='pochipp-box__body'>
						<div className='pochipp-box__title'>
							{parsedMeta.title}
						</div>
						{brand && (
							<div className='pochipp-box__brand'>{brand}</div>
						)}
						{price && (
							<div className='pochipp-box__price'>
								¥{price.toLocaleString()}
							</div>
						)}

						<div className='pochipp-box__btns'>
							<a href='###' className='pochipp-box__btn -amazon'>
								Amazon
							</a>

							<a href='###' className='pochipp-box__btn -rakuten'>
								楽天市場
							</a>
						</div>
					</div>
				</div>
			</div>
			<TextControl
				// className='__idInput'
				// placeholder={__('Post ID', 'arkhe-blocks')}
				value={parsedMeta.title}
				onChange={(newTitle) => {
					parsedMeta.title = newTitle;
					editPost({ title: newTitle });
					setAttributes({
						metadata: JSON.stringify(parsedMeta),
					});
				}}
			/>

			<div className='u-mt-20'>データ確認用</div>
			<div className='pochipp-block-dump'>
				{/* {JSON.stringify(parsedMeta)}; */}
				{Object.keys(parsedMeta).map((metakey) => {
					return (
						<div key={metakey}>
							<code>{metakey}</code> : {parsedMeta[metakey]}
						</div>
					);
				})}
			</div>
		</>
	);
};
