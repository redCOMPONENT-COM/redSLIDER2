var gulp = require('gulp');

var config = require('../../gulp-config.json');

var watchInterval = 500;
if (typeof config.watchInterval !== 'undefined') {
	watchInterval = config.watchInterval;
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');
var composer    = require('gulp-composer');
var fs          = require('fs');

var baseTask  = 'libraries.redslider';
var extPath   = '../extensions/libraries/redslider';

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':library',
		'clean:' + baseTask + ':manifest'
	],
	function() {
});

// Clean: library
gulp.task('clean:' + baseTask + ':library', function() {
	return del(config.wwwDir + '/libraries/redslider', {force : true});
});

// Clean: manifest
gulp.task('clean:' + baseTask + ':manifest', function() {
	return del(config.wwwDir + '/administrator/manifests/libraries/redslider.xml', {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':library',
		'copy:' + baseTask + ':manifest'
	],
	function() {
});

// Copy: manifest
gulp.task('copy:' + baseTask + ':manifest', ['clean:' + baseTask + ':manifest'], function() {
	return gulp.src(extPath + '/redslider.xml')
		.pipe(gulp.dest(config.wwwDir + '/administrator/manifests/libraries'));
});

// Composer
gulp.task('composer:' + baseTask, function(cb) {
	// We will use a composer file to see if composer has been executed
	var composerCheckFile = extPath + '/vendor/imagine/imagine/composer.json';

	fs.stat(composerCheckFile, function(err, stat){
		if (null !== err) {
			composer({ cwd: extPath, bin: 'php ./composer.phar'}).on('end', cb);
		} else {
			cb();
		}
	});
});

// Copy: redSLIDER2 Library
gulp.task('copy:' + baseTask + ':library', ['composer:' + baseTask], function() {
	return gulp.src([
		extPath + '/**',
		'!' + extPath + '/redslider.xml',
		'!' + extPath + '/**/docs',
		'!' + extPath + '/**/docs/**',
		'!' + extPath + '/vendor/**/sample',
		'!' + extPath + '/vendor/**/sample/**',
		'!' + extPath + '/vendor/**/tests',
		'!' + extPath + '/vendor/**/tests/**',
		'!' + extPath + '/vendor/**/Tests',
		'!' + extPath + '/vendor/**/Tests/**',
		'!' + extPath + '/vendor/**/doc',
		'!' + extPath + '/vendor/**/doc/**',
		'!' + extPath + '/vendor/**/docs',
		'!' + extPath + '/vendor/**/docs/**',
		'!' + extPath + '/**/composer.*',
		'!' + extPath + '/vendor/**/*.sh',
		'!' + extPath + '/vendor/**/build.xml',
		'!' + extPath + '/**/phpunit*',
		'!' + extPath + '/**/Vagrant*',
		'!' + extPath + '/vendor/**/.*.yml',
		'!' + extPath + '/vendor/**/.editorconfig',
	])
	.pipe(gulp.dest(config.wwwDir + '/libraries/redslider'));
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
			'!' + extPath + '/redslider.xml'
		],
		{ interval: watchInterval },
		['copy:' + baseTask + ':library', browserSync.reload]);
});

// Watch: manifest
gulp.task('watch:' +  baseTask + ':manifest', function() {
	gulp.watch(extPath + '/redslider.xml',
	{ interval: watchInterval },
	['copy:' + baseTask + ':manifest', browserSync.reload]);
});
