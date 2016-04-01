var gulp = require('gulp');

var config = require('../../gulp-config.json');
var watchInterval = 500;
if (typeof config.watchInterval !== 'undefined') {
	watchInterval = config.watchInterval;
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'components.redslider';
var extPath   = '../';

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':frontend',
		'clean:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Clean: frontend
gulp.task('clean:' + baseTask + ':frontend', function(cb) {
	return del(config.wwwDir + '/components/com_redslider', {force : true});
});

// Clean: backend
gulp.task('clean:' + baseTask + ':backend', function(cb) {
	return del(config.wwwDir + '/administrator/components/com_redslider', {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':frontend',
		'copy:' + baseTask + ':backend'
	],
	function() {
});

// Copy: frontend
gulp.task('copy:' + baseTask + ':frontend', ['clean:' + baseTask + ':frontend'], function() {
	return gulp.src(extPath + 'extensions/components/com_redslider/site/**')
		.pipe(gulp.dest(config.wwwDir + '/components/com_redslider'));
});

// Copy: backend
gulp.task('copy:' + baseTask + ':backend', ['clean:' + baseTask + ':backend'], function(cb) {
	return (
		gulp.src([extPath + 'extensions/components/com_redslider/admin/**'])
			.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redslider')) &&
		gulp.src(extPath + 'extensions/redslider.xml')
			.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redslider')) &&
		gulp.src(extPath + 'extensions/install.php')
			.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redslider'))
	);
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':frontend',
		'watch:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Watch: frontend
gulp.task('watch:' + baseTask + ':frontend', function() {
	gulp.watch([
		extPath + 'extensions/components/com_redslider/site/**/*'
	], { interval: watchInterval },
	['copy:' + baseTask + ':frontend', browserSync.reload]);
});

// Watch: backend
gulp.task('watch:' + baseTask + ':backend', function() {
	gulp.watch([
		extPath + 'extensions/components/com_redslider/admin/**/*',
		extPath + 'extensions/redslider.xml',
		extPath + 'extensions/install.php'
	],
	{ interval: watchInterval },
	['copy:' + baseTask + ':backend', browserSync.reload]);
});
