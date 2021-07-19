/**
 * @WordPress dependencies
 */
import { useEntityProp } from '@wordpress/core-data';
import {
	memo,
	//useMemo
} from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { applyFilters, hasFilter } from '@wordpress/hooks';
import { ToggleControl, PanelBody } from '@wordpress/components';

/**
 * ItemPreview
 */
export default memo(({ clientId, isCount, cvKey, setAttributes }) => {
	const cvMetaKey = window.pchppProVars?.cvMetaKey;
	// console.log(cvMetaKey);
	if (!hasFilter('pochipp.exPanel', 'pochipp-pro') || !cvMetaKey) {
		return (
			<PanelBody title='クリック率計測'>
				<p>
					<a href='https://pochipp.com/pochipp-pro/'>Pichipp Pro</a>を導入すると、ブロックごとのクリック率が計測可能になります。
				</p>
			</PanelBody>
		);
	}

	// 投稿タイプを取得
	const postType = useSelect((select) => select('core/editor').getCurrentPostType(), []);

	// 計測が利用可能な投稿タイプかどうかチェック
	const countablePostTypes = window.pchppProVars.countablePostTypes;
	if (-1 === countablePostTypes.indexOf(postType)) {
		return (
			<PanelBody title='クリック率計測'>
				<p>この投稿タイプでは計測機能を利用できません。</p>
			</PanelBody>
		);
	}

	// meta取得
	const [meta] = useEntityProp('postType', postType, 'meta');
	const cvMetaData = meta[cvMetaKey];

	const notFounded = <p>まだ計測データはありません。</p>;

	return (
		<PanelBody title='クリック率計測'>
			<ToggleControl
				label='クリック率を計測する'
				checked={isCount}
				onChange={(value) => {
					setAttributes({ isCount: value });
					if (value && !cvKey) {
						// trueの時、計測用のIDも自動生成する。
						const newID = clientId.split('-');
						setAttributes({ cvKey: newID[0] || '' });
					}
				}}
			/>
			{isCount && (
				<div className='pochipp-cv-data'>
					<div className='__title'>計測結果</div>
					<div className='__result'>{applyFilters('pochipp.exPanel', notFounded, cvKey, cvMetaData)}</div>
				</div>
			)}
		</PanelBody>
	);
});
