const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );


module.exports = {
	...defaultConfig, //@wordpress/scriptを引き継ぐ

	mode: 'production', // npm start でも圧縮させる

	//エントリーポイント
	entry: {
		'linkbox': path.resolve( __dirname, 'src/blocks/linkbox/index.js' ),
		'setting': path.resolve( __dirname, 'src/blocks/setting/index.js' ),
	},

	//アウトプット先
	output: {
		path: path.resolve( __dirname, 'dist/blocks' ),
		filename: '[name]/index.js',
	},

	resolve: {
		alias: {
			'@blocks': path.resolve( __dirname, 'src/blocks/' ),
		},
	},
};
