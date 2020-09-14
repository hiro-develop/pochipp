/**
 * ItemPreview
 */
export default function (props) {
	const { attributes, parsedMeta } = props;

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
							{attributes.title}
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
			<div className='u-mt-20'>データ確認用</div>
			<code style={{ whiteSpace: 'normal', width: '100%' }}>
				{JSON.stringify(parsedMeta)};
			</code>
		</>
	);
}
