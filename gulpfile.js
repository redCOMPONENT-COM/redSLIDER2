var gulp  = require('gulp');
var jgulp = require('joomla-gulp-release');

/**
 * Release full Joomla! package
 *
 * @todo  Still not complete needs to create zip manually
 *
 * @return  {object}     Gulp tasks
 */
gulp.task(
	'release',
	[
		'release:component',
		'release:modules',
		'release:plugins',
	],
	function() {}
);
