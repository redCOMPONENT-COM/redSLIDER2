var gulp = require('gulp');

var config = require('../../gulp-config.json');

var watchInterval = 500;
if (typeof config.watchInterval !== 'undefined') {
	watchInterval = config.watchInterval;
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'libraries.mpdf';
var extPath   = './../extensions/libraries/mpdf';

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':library',
		'clean:' + baseTask + ':manifest'
	],
	function() {
});

// Clean: library
gulp.task('clean:' + baseTask + ':library', function(cb) {
	return del(config.wwwDir + '/libraries/mpdf', {force : true});
});

// Clean: manifest
gulp.task('clean:' + baseTask + ':manifest', function(cb) {
	return del(config.wwwDir + '/administrator/manifests/libraries/mpdf.xml', {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':library',
		'copy:' + baseTask + ':manifest'
	],
	function() {
});

// Copy: library
gulp.task('copy:' + baseTask + ':library',
	['clean:' + baseTask + ':library'], function() {
	return gulp.src([
		extPath + '/**',
		'!' + extPath + '/mpdf.xml',
		'!' + extPath + '/**/docs',
		'!' + extPath + '/**/docs/**',
		'!' + extPath + '/vendor/**/sample',
		'!' + extPath + '/vendor/**/sample/**',
		'!' + extPath + '/vendor/**/tests',
		'!' + extPath + '/vendor/**/tests/**',
		'!' + extPath + '/vendor/**/composer.json',
		'!' + extPath + '/vendor/**/*.md',
		'!' + extPath + '/vendor/**/*.sh',
		'!' + extPath + '/vendor/**/build.xml',
		'!' + extPath + '/vendor/**/phpunit*.xml',
		'!' + extPath + '/vendor/**/.*.yml',
		'!' + extPath + '/vendor/**/.editorconfig'
	])
	.pipe(gulp.dest(config.wwwDir + '/libraries/mpdf'));
});

// Copy: manifest
gulp.task('copy:' + baseTask + ':manifest', ['clean:' + baseTask + ':manifest'], function() {
	return gulp.src(extPath + '/mpdf.xml')
		.pipe(gulp.dest(config.wwwDir + '/administrator/manifests/libraries'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':library',
		'watch:' + baseTask + ':manifest'
	],
	function() {
});

// Watch: library
gulp.task('watch:' +  baseTask + ':library', function() {
	gulp.watch([
			extPath + '/**/*',
			'!' + extPath + '/mpdf.xml'
		],
		{ interval: watchInterval },
		['copy:' + baseTask + ':library', browserSync.reload]);
});

// Watch: manifest
gulp.task('watch:' +  baseTask + ':manifest', function() {
	gulp.watch(extPath + '/mpdf.xml',
	{ interval: watchInterval },
	['copy:' + baseTask + ':manifest', browserSync.reload]);
});
