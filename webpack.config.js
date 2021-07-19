const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
	...defaultConfig,

	entry: {
		linkbox: path.resolve(__dirname, 'src/blocks/linkbox/index.js'),
		setting: path.resolve(__dirname, 'src/blocks/setting/index.js'),
		'setting-preview': path.resolve(__dirname, 'src/blocks/setting-preview/index.js'),
	},

	output: {
		path: path.resolve(__dirname, 'dist/blocks'),
		filename: '[name]/index.js',
	},

	resolve: {
		alias: {
			'@blocks': path.resolve(__dirname, 'src/blocks/'),
		},
	},
};
