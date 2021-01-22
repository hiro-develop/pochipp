/**
 * @WordPress dependencies
 */
import { memo } from '@wordpress/element';

/**
 * ItemPreview
 */
export default memo(({ postTitle, parsedMeta }) => {
	if (!parsedMeta.searched_at) {
		return (
			<div className='__preview -null'>
				<p>商品を選択してください</p>
			</div>
		);
	}

	const { info, price } = parsedMeta;
	return (
		<div className='__preview components-disabled'>
			<div className='pochipp-box'>
				<div className='pochipp-box__image'>
					<img src={parsedMeta.m_image_url} alt='' />
				</div>
				<div className='pochipp-box__body'>
					<div className='pochipp-box__title'>{postTitle}</div>
					{info && <div className='pochipp-box__info'>{info}</div>}
					{price && (
						<div className='pochipp-box__price'>
							¥{price.toLocaleString()}
							<span>（{parsedMeta.price_at}時点）</span>
						</div>
					)}

					<div className='pochipp-box__btns'>
						{1 && (
							<div className='pochipp-box__btnwrap'>
								<div className='pochipp-box__btn -amazon'>
									Amazon
								</div>
							</div>
						)}

						<div className='pochipp-box__btnwrap'>
							<a href='###' className='pochipp-box__btn -rakuten'>
								楽天市場
							</a>
						</div>

						<div className='pochipp-box__btnwrap'>
							<a href='###' className='pochipp-box__btn -yahoo'>
								Yahooショッピング
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
});
