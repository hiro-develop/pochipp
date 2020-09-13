/**
 * @WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { registerBlockType } from '@wordpress/blocks';
import {
	RichText,
	// InnerBlocks,
	InspectorControls,
	BlockControls,
	// BlockIcon,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	BaseControl,
	Button,
	ButtonGroup,
	// Tooltip,
	TextareaControl,
	ToolbarButton,
	ToolbarGroup,
	Popover,
} from '@wordpress/components';

import { RawHTML } from '@wordpress/element';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Pochipp
 */
registerBlockType('pochipp/linkbox', {
	title: 'ポチップ',
	icon: 'external',
	category: 'design',
	keywords: ['pochipp', 'linkbox'],
	// parent: ['arkhe-block/header-bar'],
	supports: {
		className: false,
		customClassName: false,
		multiple: false,
		reusable: false,
		html: false,
	},
	attributes: {
		metadata: {
			type: 'string',
			default: '',
		},
		// 編集可能箇所ごとに attributes 持たせる
	},
	edit: (props) => {
		const { attributes, clientId } = props;

		return <>フロント用ブロック</>;
	},

	save: (props) => {
		null;
	},
});
