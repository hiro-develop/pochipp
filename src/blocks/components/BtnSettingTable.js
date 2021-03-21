/**
 * @WordPress dependencies
 */
import { memo } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { Icon, external, search, closeSmall } from '@wordpress/icons';

/**
 * UrlConfBtn
 */
const UrlConfBtn = memo(({ url, hasSearchedLink }) => {
	return (
		<span className='__urlConfBtn'>
			<span>リンク先 : </span>
			<a href={url} target='_blank' rel='noreferrer noopener'>
				{hasSearchedLink ? '商品ページ' : '検索結果ページ'}
				<Icon icon={external} />
			</a>
		</span>
	);
});

/**
 * AdditionalSearchBtn
 */
const AdditionalSearchBtn = memo(({ type, openThickbox, hasSearchedLink }) => {
	let label = '';
	if ('amazon' === type) {
		label = 'Amazon';
	} else if ('rakuten' === type) {
		label = '楽天';
	} else if ('yahoo' === type) {
		label = 'Yahoo';
	}

	return (
		<Button
			icon={<Icon icon={search} />}
			isSecondary={true}
			onClick={() => {
				openThickbox(type);
			}}
		>
			{hasSearchedLink ? label + 'で再検索' : label + 'でも検索'}
		</Button>
	);
});

/**
 * DeleteDetailLinkBtn
 */
const DeleteDetailLinkBtn = ({ isHide, onClick }) => {
	return (
		<Button
			className={isHide ? '-hide' : ''}
			icon={<Icon icon={closeSmall} />}
			isSecondary={true}
			onClick={() => {
				onClick();
			}}
		>
			詳細リンクを削除
		</Button>
	);
};

/**
 * BtnSettingTable
 */
export default ({ attrs, openThickbox, deleteAmazon, deleteRakuten, deleteYahoo }) => {
	// meta情報
	const keywords = attrs.keywords || '';
	const amazonAsin = attrs.asin || '';

	// 商品が検索された状態かどうか
	const searchedAt = attrs.searched_at;
	// const hasSearchedItem = !!searchedAt;

	// 各APIから検索済みかどうか
	const amazonDetailUrl = amazonAsin ? `https://www.amazon.co.jp/dp/${amazonAsin}` : '';
	const amazonSearchedLink = amazonDetailUrl || '';
	const rakutenSearchedLink = attrs.rakuten_detail_url || '';
	const yahooSearchedLink = attrs.yahoo_detail_url || '';

	const amazonLink = amazonSearchedLink || 'https://www.amazon.co.jp/s?k=' + encodeURIComponent(keywords);
	const rakutenLink = rakutenSearchedLink || 'https://search.rakuten.co.jp/search/mall/' + encodeURIComponent(keywords);
	const yahooLink = yahooSearchedLink || 'https://shopping.yahoo.co.jp/search?p=' + encodeURIComponent(keywords);

	const hasAffi = window.pchppVars.hasAffi;

	return (
		<div className='pchpp-btnTable'>
			<table className='__table'>
				<tbody>
					{hasAffi.amazon && (
						<tr>
							<th>Amazon</th>
							<td>
								<UrlConfBtn url={amazonLink} hasSearchedLink={amazonSearchedLink} />
							</td>
							<td>
								{'amazon' === searchedAt ? (
									<span className='__mainLabel'>検索元</span>
								) : (
									<>
										<AdditionalSearchBtn
											type='amazon'
											openThickbox={openThickbox}
											hasSearchedLink={amazonSearchedLink}
										/>
										<DeleteDetailLinkBtn
											isHide={!amazonSearchedLink}
											onClick={() => {
												deleteAmazon();
											}}
										/>
									</>
								)}
							</td>
						</tr>
					)}
					{hasAffi.rakuten && (
						<tr>
							<th>楽天</th>
							<td>
								<UrlConfBtn url={rakutenLink} hasSearchedLink={rakutenSearchedLink} />
							</td>
							<td>
								{'rakuten' === searchedAt ? (
									<span className='__mainLabel'>検索元</span>
								) : (
									<>
										<AdditionalSearchBtn
											type='rakuten'
											openThickbox={openThickbox}
											hasSearchedLink={rakutenSearchedLink}
										/>
										<DeleteDetailLinkBtn
											isHide={!rakutenSearchedLink}
											onClick={() => {
												deleteRakuten();
											}}
										/>
									</>
								)}
							</td>
						</tr>
					)}
					{hasAffi.yahoo && (
						<tr>
							<th>Yahoo</th>
							<td>
								<UrlConfBtn url={yahooLink} hasSearchedLink={yahooSearchedLink} />
							</td>
							<td>
								{'yahoo' === searchedAt ? (
									<span className='__mainLabel'>検索元</span>
								) : (
									<>
										<AdditionalSearchBtn type='yahoo' openThickbox={openThickbox} hasSearchedLink={yahooSearchedLink} />
										<DeleteDetailLinkBtn
											isHide={!yahooSearchedLink}
											onClick={() => {
												deleteYahoo();
											}}
										/>
									</>
								)}
							</td>
						</tr>
					)}
				</tbody>
			</table>
		</div>
	);
};
