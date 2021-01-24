/**
 * @WordPress dependencies
 */
import { memo } from '@wordpress/element';

/**
 * ItemPreview
 */
export default memo(({ postTitle, parsedMeta }) => {
	// 投稿タイトルもmeta情報も持たないとき。
	if (!postTitle && 0 === Object.keys(parsedMeta).length) {
		return (
			<div className='__preview -null'>
				<p>商品を選択してください</p>
			</div>
		);
	}

	const { info, price, keywords } = parsedMeta;

	// Amazonボタンを表示するか
	const showAmazon = !!(keywords || parsedMeta.amazon_detail_url);
	return (
		<div className='__preview components-disabled'>
			<div className='pochipp-box'>
				<div className='pochipp-box__image'>
					<img src={parsedMeta.image_url} alt='' />
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
						{showAmazon && (
							<div className='pochipp-box__btnwrap'>
								<div className='pochipp-box__btn -amazon'>
									{window.pchppVars.amazonBtnText || 'Amazon'}
								</div>
							</div>
						)}

						<div className='pochipp-box__btnwrap'>
							<a href='###' className='pochipp-box__btn -rakuten'>
								{window.pchppVars.rakutenBtnText || '楽天市場'}
							</a>
						</div>

						<div className='pochipp-box__btnwrap'>
							<a href='###' className='pochipp-box__btn -yahoo'>
								{window.pchppVars.yahooBtnText ||
									'Yahooショッピング'}
							</a>
						</div>

						{parsedMeta.custom_btn_url &&
							parsedMeta.custom_btn_text && (
								<div className='pochipp-box__btnwrap'>
									<a
										href='###'
										className='pochipp-box__btn -custom'
									>
										{parsedMeta.custom_btn_text}
									</a>
								</div>
							)}
					</div>
				</div>
			</div>
		</div>
	);
});
