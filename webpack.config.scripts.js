const path = require( 'path' );

module.exports = {
	mode: 'production', // npm start でも圧縮させる

	//エントリーポイント
	entry: {
		'search': path.resolve( __dirname, 'src/js/search.js' ),
		// 'media': path.resolve( __dirname, 'src/js/media.js' ),
	},

	//アウトプット先
	output: {
		path: path.resolve( __dirname, 'dist/js' ),
		filename: '[name].js',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				// query: {
				//     presets: ['react', 'es2015']
				// },
				use: [
					{
						// Babel を利用する
						loader: 'babel-loader',
						// Babel のオプションを指定する
						options: {
							presets: [
								[
									'@babel/preset-env',
									{
										modules: false,
										useBuiltIns: 'usage', //core-js@3から必要なpolyfillだけを読み込む
										corejs: 3,
										// targets: {
										//     esmodules: true,
										// },
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
			'@blocks': path.resolve( __dirname, 'src/blocks/' ),
		},
	},
	performance: { hints: false },
};
