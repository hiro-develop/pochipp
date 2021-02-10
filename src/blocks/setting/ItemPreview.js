/**
 * @WordPress dependencies
 */
import { memo } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';

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

	const { info, price } = parsedMeta;

	// Amazonボタンを表示するか
	// const showAmazon = !!(keywords || parsedMeta.amazon_detail_url);

	// ポチップ設定データ
	const pchppVars = window.pchppVars || {};

	let dataBtnStyle = pchppVars.btnStyle || 'dflt';
	if ('default' === dataBtnStyle) dataBtnStyle = 'dflt';

	// const isPayPay = !!parsedMeta.is_paypay;
	// const yahooClass = isPayPay ? '-paypay' : '-yahoo';

	return (
		<div className='__preview'>
			<div
				className='pochipp-box'
				data-img={pchppVars.imgPosition || 'l'}
				data-lyt-pc={pchppVars.boxLayoutPC || 'dflt'}
				data-lyt-mb={pchppVars.boxLayoutMB || 'vrtcl'}
				data-btn-style={dataBtnStyle}
				data-btn-radius={pchppVars.btnRadius || 'off'}
			>
				<div className='pochipp-box__image'>
					<img src={parsedMeta.image_url} alt='' />
				</div>
				<div className='pochipp-box__body'>
					<div className='pochipp-box__title'>{postTitle}</div>
					{info && <div className='pochipp-box__info'>{info}</div>}
					{price && (
						<div className='pochipp-box__price'>
							¥{price.toLocaleString()}
							<span>
								（{parsedMeta.price_at}時点 |{' '}
								{parsedMeta.searched_at}調べ）
							</span>
						</div>
					)}

					<ServerSideRender
						block='pochipp/setting-preview'
						attributes={{ meta: JSON.stringify(parsedMeta) }}
						className={`_components-disabled`}
					/>
					{/* <div
						className='pochipp-box__btns'
						data-maxclmn-pc={pchppVars.maxClmnPC || 'fit'}
						data-maxclmn-mb={pchppVars.maxClmnMB || '1'}
					>
						{showAmazon && (
							<div className='pochipp-box__btnwrap -amazon'>
								<div className='pochipp-box__btn'>
									{pchppVars.amazonBtnText || 'Amazon'}
								</div>
							</div>
						)}

						<div className='pochipp-box__btnwrap -rakuten'>
							<a href='###' className='pochipp-box__btn'>
								{pchppVars.rakutenBtnText || '楽天市場'}
							</a>
						</div>

						<div className={`pochipp-box__btnwrap ${yahooClass}`}>
							<a href='###' className='pochipp-box__btn'>
								{isPayPay
									? pchppVars.paypayBtnText || 'PayPayモール'
									: pchppVars.yahooBtnText || 'Yahoo'}
							</a>
						</div>

						{parsedMeta.custom_btn_url &&
							parsedMeta.custom_btn_text && (
								<div className='pochipp-box__btnwrap -custom'>
									<a href='###' className='pochipp-box__btn'>
										{parsedMeta.custom_btn_text}
									</a>
								</div>
							)}
					</div> */}
				</div>
			</div>

			<div className='__helpText'>
				※ プレビュー内のボタンはアフィリエイトリンク化されていません。
			</div>
		</div>
	);
});
