const { src, dest } = require('gulp');

// エラー時処理
const plumber = require('gulp-plumber'); // 続行
const notify = require('gulp-notify'); // 通知

// sass・css系
const sass = require('gulp-sass'); // sassコンパイル
const sassGlob = require('gulp-sass-glob'); // glob (@importの/*を可能に)
const autoprefixer = require('gulp-autoprefixer'); // プレフィックス付与
const gcmq = require('gulp-group-css-media-queries'); // media query整理
const cleanCSS = require('gulp-clean-css');

/**
 * パス
 */
const path = {
	src: {
		scss: 'src/scss/**/*.scss',
	},
	dest: {
		css: 'dist/css',
	},
};

/**
 * SCSSコンパイル
 */
const compileScss = () => {
	return (
		src(path.src.scss)
			.pipe(
				plumber({
					errorHandler: notify.onError('<%= error.message %>'),
				})
			)
			.pipe(sassGlob())
			.pipe(sass())
			.pipe(
				autoprefixer({
					cascade: false,
				})
			)
			.pipe(gcmq())
			// .pipe(sass({ outputStyle: 'compressed' }))  //gcmqでnestedスタイルに展開されてしまうので再度compact化。
			.pipe(cleanCSS())
			.pipe(dest(path.dest.css))
	);
};

exports.compileScss = compileScss;
