const path = require('path');

module.exports = {
	mode: 'production',
	entry: {
		search: path.resolve(__dirname, 'src/js/search.js'),
		setting: path.resolve(__dirname, 'src/js/setting.js'),
		colorpicker: path.resolve(__dirname, 'src/js/colorpicker.js'),
		datepicker: path.resolve(__dirname, 'src/js/datepicker.js'),
		// 'media': path.resolve( __dirname, 'src/js/media.js' ),
	},

	output: {
		path: path.resolve(__dirname, 'dist/js'),
		filename: '[name].js',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: [
					{
						// Babel
						loader: 'babel-loader',
						options: {
							presets: [
								[
									'@babel/preset-env',
									{
										modules: false,
										useBuiltIns: 'usage',
										corejs: 3,
									},
								],
							],
						},
					},
				],
			},
		],
	},
	resolve: {
		alias: {
			'@blocks': path.resolve(__dirname, 'src/blocks/'),
		},
	},
	performance: { hints: false },
};
