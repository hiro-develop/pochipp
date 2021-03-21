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

	// ポチップ設定データ
	const pchppVars = window.pchppVars || {};

	let dataBtnStyle = pchppVars.btnStyle || 'dflt';
	if ('default' === dataBtnStyle) dataBtnStyle = 'dflt';

	const hidePrice = parsedMeta.hidePrice || false;

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
					{price && !hidePrice && (
						<div className='pochipp-box__price'>
							¥{price.toLocaleString()}
							<span>
								（{parsedMeta.price_at}時点 | {parsedMeta.searched_at}調べ）
							</span>
						</div>
					)}
					<ServerSideRender
						block='pochipp/setting-preview'
						attributes={{ meta: JSON.stringify(parsedMeta) }}
						className={`_components-disabled`}
					/>
				</div>
			</div>
			<div className='__helpText'>※ このプレビュー内のボタンはアフィリエイトリンク化されていません。</div>
		</div>
	);
});
