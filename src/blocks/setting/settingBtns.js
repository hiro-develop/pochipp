/**
 * @WordPress dependencies
 */
import { memo } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { Icon, search, rotateLeft } from '@wordpress/icons';

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
