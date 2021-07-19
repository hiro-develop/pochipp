import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';

const { apiVersion, name, title, supports } = metadata;

registerBlockType(name, {
	apiVersion,
	title,
	supports,
	attributes: metadata.attributes,
	edit: () => {
		return null;
	},
	save: () => {
		return null;
	},
});
