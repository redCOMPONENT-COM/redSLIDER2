var gulp = require('gulp');

// Load config
var config = require('../../../gulp-config.json');

var watchInterval = 500;
if (typeof config.watchInterval !== 'undefined') {
	watchInterval = config.watchInterval;
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');
var fs          = require('fs');
var path        = require('path');
var minifyCSS   = require('gulp-minify-css');
var less        = require('gulp-less');
var rename      = require('gulp-rename');

var group = 'redslider_sections';
var name = 'section_redevent';

var baseTask   = 'plugins.' + group + '.' + name;
var extPath    = '../extensions/plugins/' + group + '/' + name;
var mediaPath = './media/plugins/' + group + '/' + name;

var wwwExtPath = config.wwwDir + '/plugins/' + group + '/' + name;
var wwwMediaExtPath = config.wwwDir + '/media/' + group + '/' + name;

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':plugin',
		'clean:' + baseTask + ':media',
		'clean:' + baseTask + ':css',
	],
	function() {
});

// Clean: plugin
gulp.task('clean:' + baseTask + ':plugin', function() {
	return del(wwwExtPath, {force : true});
});

// Clean: Media
gulp.task('clean:' + baseTask + ':media', function() {
	return del([
		wwwMediaExtPath,
		'!' + wwwMediaExtPath + '/css',
		'!' + wwwMediaExtPath + '/images'
	], {force: true});
});

// Clean: CSS
gulp.task('clean:' + baseTask + ':css', function() {
	return del(wwwMediaExtPath + '/css', {force: true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':plugin',
		'copy:' + baseTask + ':media',
		'copy:' + baseTask + ':css'
	],
	function() {
});

// Copy Plugin
gulp.task('copy:' + baseTask + ':plugin', ['clean:' + baseTask], function() {
	return gulp.src( extPath + '/**')
		.pipe(gulp.dest(wwwExtPath));
});

// Copy: Media
gulp.task('copy:' + baseTask + ':media', ['clean:' + baseTask + ':media'], function() {
	return gulp.src([
			mediaPath + '/**',
			'!' + mediaPath + '/css',
			'!' + mediaPath + '/css/**',
			'!' + mediaPath + '/less',
			'!' + mediaPath + '/less/**'
		])
		.pipe(gulp.dest(wwwMediaExtPath));
});

// Copy: CSS
gulp.task('copy:' + baseTask + ':css', ['less:' + baseTask], function(){
	return gulp.src(mediaPath + '/css/**/*')
		.pipe(gulp.dest(wwwMediaExtPath + '/css'));
});

// LESS compiler
gulp.task('less:' + baseTask, ['clean:' + baseTask + ':css'], function() {
	return gulp.src(mediaPath + '/less/**/*.less')
		.pipe(less())
		.pipe(gulp.dest(wwwMediaExtPath + '/css'))
		.pipe(minifyCSS())
		.pipe(rename(function (path) { path.extname = '.min.' + path.extname; }))
		.pipe(gulp.dest(wwwMediaExtPath + '/css'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':plugin',
		'watch:' + baseTask + ':media',
		'watch:' + baseTask + ':css',
		'watch:' + baseTask + ':less'
	],
	function() {
});

// Watch: plugin
gulp.task('watch:' + baseTask + ':plugin', function() {
	gulp.watch(
		extPath + '/**/*',
		{ interval: watchInterval },
		 ['copy:' + baseTask, browserSync.reload]
	);
});

// Watch: Media
gulp.task('watch:' + baseTask + ':media', function() {
	gulp.watch([
		mediaPath + '/**',
		'!' + mediaPath + '/css',
		'!' + mediaPath + '/css/**',
		'!' + mediaPath + '/less',
		'!' + mediaPath + '/less/**'
	],
	{ interval: watchInterval },
	['copy:' + baseTask + ':media', browserSync.reload]);
});

// Watch: CSS (3rd library)
gulp.task('watch:' + baseTask + ':css', function() {
	gulp.watch([
		mediaPath + '/css/**/*.css'
	],
	{ interval: watchInterval },
	['copy:' + baseTask + ':css', browserSync.reload]);
});

// Watch: LESS
gulp.task('watch:' + baseTask + ':less', function() {
	gulp.watch([
		mediaPath + '/less/**/*.less'
	],
	{ interval: watchInterval },
	['less:' + baseTask, browserSync.reload]);
});