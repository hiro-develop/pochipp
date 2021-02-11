/**
 * @WordPress dependencies
 */
import { memo } from '@wordpress/element';
import { Button } from '@wordpress/components';
import {
	Icon,
	external,
	search,
	closeSmall,
	rotateLeft,
} from '@wordpress/icons';

/**
 * SearchBtn
 */
export const SearchBtn = memo(({ text, onClick }) => {
	return (
		<Button
			icon={<Icon icon={search} />}
			className='__searchBtn thickbox'
			isPrimary={true}
			onClick={() => {
				onClick();
			}}
		>
			{text}
		</Button>
	);
});

/**
 * UpdateBtn
 */
export const UpdateBtn = memo(({ onClick }) => {
	return (
		<Button
			icon={<Icon icon={rotateLeft} />}
			className='__updateBtn'
			isSecondary={true}
			onClick={() => {
				onClick();
			}}
		>
			最新情報に更新
		</Button>
	);
});

/**
 * UrlConfBtn
 */
export const UrlConfBtn = memo(({ type, hasSearchedLink }) => {
	return (
		<Button
			className='-icon-right'
			icon={<Icon icon={external} />}
			// isSecondary={true}
			onClick={() => {
				const btn = document.querySelector(
					`.__preview .-${type} .pochipp-box__btn`
				);
				btn.click();
			}}
		>
			{hasSearchedLink ? '商品詳細ページ' : 'キーワード検索結果ページ'}
		</Button>
	);
});

/**
 * AdditionalSearchBtn
 */
export const AdditionalSearchBtn = memo(
	({ type, openThickbox, hasSearchedLink }) => {
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
	}
);

/**
 * DeleteDetailLinkBtn
 */
export const DeleteDetailLinkBtn = ({ isHide, onClick }) => {
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
