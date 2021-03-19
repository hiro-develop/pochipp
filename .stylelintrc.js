module.exports = {
	plugins: ['stylelint-scss'],
	extends: [
		'stylelint-config-wordpress',
		'stylelint-config-rational-order',
	],
	ignoreFiles: ['./src/scss/inc/bass/**/*.scss', './**/*.js', './assets/**'],
	rules: {
		'max-line-length': null, //max文字数を無視
		'selector-class-pattern': null,
		'selector-id-pattern': null, //idの命名規則
		'function-url-quotes': 'never', //不必要なクォーテーションを禁止( 自動Fixできないので注意 )
		'no-descending-specificity': null, //セレクタの詳細度に関する警告を出さない
		'font-weight-notation': null, //font-weightの指定は自由
		'font-family-no-missing-generic-family-keyword': null, //sans-serif / serifを必須にするか。object-fitでエラーださないようにする。
		'at-rule-no-unknown': null, //scssで使える @include などにエラーがでないように
		'scss/at-rule-no-unknown': true, //scssでもサポートしていない @ルールにはエラーを出す
	},
};
