var gulp       = require('gulp');
var requireDir = require('require-dir');
var argv       = require('yargs').argv;
var xml2js     = require('xml2js');
var fs         = require('fs');
var path       = require('path');
var minifyCSS  = require('gulp-minify-css');
var less       = require('gulp-less');
var rename     = require('gulp-rename');
var zip        = require('gulp-zip');
var del        = require('del');

var config    = require('./gulp-config.json');
var extension = require('./package.json');

var joomlaGulp = requireDir('./node_modules/joomla-gulp', {recurse: true});
var redcoregulp      = requireDir('./redCORE/build/gulp-redcore', {recurse: true});
var jgulp      = requireDir('./jgulp', {recurse: true});

var parser     = new xml2js.Parser();

/**
 * Function for read list folder
 *
 * @param  string dir Path of folder
 *
 * @return array      Subfolder list.
 */
function getFolders(dir){
	return fs.readdirSync(dir)
		.filter(function(file){
			return fs.statSync(path.join(dir, file)).isDirectory();
		}
	);
}

// Clean test site
gulp.task(
	'clean',
	[
		'clean:components',
		'clean:libraries',
		'clean:media',
		'clean:modules',
		'clean:packages',
		'clean:plugins',
		'clean:templates',
	], function() {
		return true;
});

// Copy to test site
gulp.task('copy', [
		'copy:components',
		'copy:libraries',
		'copy:media',
		'copy:modules',
		'copy:packages',
		'copy:plugins',
		'copy:templates',
	], function() {
		return true;
});

// Watch for file changes
gulp.task('watch', [
		'watch:components',
		'watch:libraries',
		'watch:media',
		'watch:modules',
		'watch:packages',
		'watch:plugins',
		'watch:templates',
	], function() {
		return true;
});

gulp.task('release',
	[
		'release:redslider',
		'release:plugins'
	]
);

gulp.task('release:redslider:media', ['release:redslider:media:less'], function(cb){
	return gulp.src([
			'media/components/com_redslider/**/*',
			'!media/components/com_redslider/**/.gitkeep',
			'!media/components/com_redslider/less',
			'!media/components/com_redslider/less/**'
		])
		.pipe(gulp.dest('../extensions/media/com_redslider'));
});

gulp.task('release:redslider:redCORE', function(cb){
	return gulp.src([
			'./redCORE/extensions/**'
		])
		.pipe(gulp.dest('../extensions/redCORE/extensions'));
});

gulp.task('release:redslider:media:less', function() {
	return gulp.src('media/components/com_redslider/less/**/*.less')
		.pipe(less())
		.pipe(gulp.dest('../extensions/media/com_redslider/css'))
		.pipe(minifyCSS())
		.pipe(rename(function (path) { path.extname = '.min' + path.extname; }))
		.pipe(gulp.dest('../extensions/media/com_redslider/css'));
});

gulp.task('release:redslider:modules', ['release:redslider:modules:less'], function() {
	var modules = getFolders('media/modules/site');

	for (var i = 0; i < modules.length; i++) {
		gulp.src([
			'media/modules/site/' + modules[i] + '/**',
			'media/modules/site/' + modules[i] + '/.gitkeep',
			'!media/modules/site/' + modules[i] + '/less',
			'!media/modules/site/' + modules[i] + '/less/**'
		])
		.pipe(gulp.dest('../extensions/modules/site/' + modules[i] + '/media/' + modules[i]));
	};
});

gulp.task('release:redslider:modules:less', function() {
	var modules = getFolders('media/modules/site');

	for (var i = 0; i < modules.length; i++) {
		gulp.src('media/modules/site/' + modules[i] + '/less/**/*.less')
			.pipe(less())
			.pipe(gulp.dest('../extensions/modules/site/' + modules[i] + '/media/' + modules[i] + '/css'))
			.pipe(minifyCSS())
			.pipe(rename(function (path) { path.extname = '.min' + path.extname; }))
			.pipe(gulp.dest('../extensions/modules/site/' + modules[i] + '/media/' + modules[i] + '/css'));
	};
});

gulp.task('release:redslider:plugins', ['release:redslider:plugins:less'], function() {
	var pluginGroups = getFolders('media/plugins');

	for (var i = 0; i < pluginGroups.length; i++) {
		var pluginName = getFolders('media/plugins/' + pluginGroups[i]);

		for (var j = 0; j < pluginName.length; j++){
			gulp.src([
				'media/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/**',
				'media/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/.gitkeep',
				'!media/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/less',
				'!media/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/less/**'
			])
			.pipe(gulp.dest('../extensions/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/media'));
		}
	};
});

gulp.task('release:redslider:plugins:less', function() {
	var pluginGroups = getFolders('media/plugins');

	for (var i = 0; i < pluginGroups.length; i++) {
		var pluginName = getFolders('media/plugins/' + pluginGroups[i]);

		for (var j = 0; j < pluginName.length; j++){
			gulp.src('media/plugins/' + pluginGroups[i] + '/' + pluginName[j]+ '/less/**/*.less')
				.pipe(less())
				.pipe(gulp.dest('../extensions/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/media/css'))
				.pipe(minifyCSS())
				.pipe(rename(function (path) { path.extname = '.min' + path.extname; }))
				.pipe(gulp.dest('../extensions/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/media/css'));
		}
	};
});

gulp.task('release:redslider:core-plugins', ['clean:plugins', 'release:redslider:core-plugins:less'], function() {
	var pluginGroups = getFolders('media/plugins');

	for (var i = 0; i < pluginGroups.length; i++) {
		var pluginName = getFolders('media/plugins/' + pluginGroups[i]);

		for (var j = 0; j < pluginName.length; j++){
			if (pluginName[j] == 'section_article' || pluginName[j] == 'section_standard' || pluginName[j] == 'section_video')
			{
				gulp.src([
					'media/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/**',
					'media/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/.gitkeep',
					'!media/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/less',
					'!media/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/less/**'
				])
				.pipe(gulp.dest('../extensions/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/media'));
				pluginRelease(pluginGroups[i], pluginName[j]);
			}
		}
	};
});

gulp.task('release:redslider:core-plugins:less', function() {
	var pluginGroups = getFolders('media/plugins');

	for (var i = 0; i < pluginGroups.length; i++) {
		var pluginName = getFolders('media/plugins/' + pluginGroups[i]);

		for (var j = 0; j < pluginName.length; j++){
			if (pluginName[j] == 'section_article' || pluginName[j] == 'section_standard' || pluginName[j] == 'section_video')
			{
				gulp.src('media/plugins/' + pluginGroups[i] + '/' + pluginName[j]+ '/less/**/*.less')
					.pipe(less())
					.pipe(gulp.dest('../extensions/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/media/css'))
					.pipe(minifyCSS())
					.pipe(rename(function (path) { path.extname = '.min' + path.extname; }))
					.pipe(gulp.dest('../extensions/plugins/' + pluginGroups[i] + '/' + pluginName[j] + '/media/css'));
			}
		}
	};
});

gulp.task('release:redslider:package',
	['release:redslider:media', 'release:redslider:modules', 'release:redslider:redCORE', 'release:redslider:plugins'],
	function (cb) {
	fs.readFile('../extensions/redslider.xml', function(err, data) {
		parser.parseString(data, function (err, result) {
			var version = result.extension.version[0];
			var fileName = argv.skipVersion ? extension.name + '.zip' : extension.name + '-v' + version + '.zip';

			// We will output where release package is going so it is easier to find
			console.log('Creating new redSLIDER release file in: ' + path.join(config.release_dir, fileName));

			return gulp.src([
					'../extensions/**/*',
					'!../extensions/**/.gitkeep',
					'!../extensions/components/com_redslider/**/.gitkeep',
					'!../extensions/libraries/**/.gitkeep',
					'!../extensions/libraries/redslider/vendor/**/tests/**/*',
					'!../extensions/libraries/redslider/vendor/**/tests',
					'!../extensions/libraries/redslider/vendor/**/Tests/**/*',
					'!../extensions/libraries/redslider/vendor/**/Tests',
					'!../extensions/libraries/redslider/vendor/**/docs/**/*',
					'!../extensions/libraries/redslider/vendor/**/docs',
					'!../extensions/libraries/redslider/vendor/**/doc/**/*',
					'!../extensions/libraries/redslider/vendor/**/doc',
					'!../extensions/libraries/redslider/vendor/**/composer.*',
					'!../extensions/libraries/redslider/vendor/**/phpunit*',
					'!../extensions/libraries/redslider/vendor/**/Vagrantfile',
					'!../extensions/modules/**/.gitkeep',
					'!../extensions/plugins/**/.gitkeep',
					'!../extensions/**/composer.lock',
					'!../extensions/**/composer.json'
				])
				.pipe(zip(fileName))
				.pipe(gulp.dest(config.release_dir))
				.on('end', cb);
		});
	});
});

gulp.task('release:redslider:full_package',
	['release:redslider:media', 'release:redslider:modules', 'release:redslider:redCORE', 'release:redslider:core-plugins'],
	function (cb) {
	fs.readFile('../extensions/redslider.xml', function(err, data) {
		parser.parseString(data, function (err, result) {
			var version = result.extension.version[0];
			var fileName = argv.skipVersion ? extension.name + '.zip' : extension.name + '-v' + version + '.zip';

			// We will output where release package is going so it is easier to find
			console.log('Creating new redSLIDER release file in: ' + path.join(config.release_dir, fileName));

			return gulp.src([
					'../extensions/**/*',
					'!../extensions/**/.gitkeep',
					'!../extensions/components/com_redslider/**/.gitkeep',
					'!../extensions/libraries/**/.gitkeep',
					'!../extensions/libraries/redslider/vendor/**/tests/**/*',
					'!../extensions/libraries/redslider/vendor/**/tests',
					'!../extensions/libraries/redslider/vendor/**/Tests/**/*',
					'!../extensions/libraries/redslider/vendor/**/Tests',
					'!../extensions/libraries/redslider/vendor/**/docs/**/*',
					'!../extensions/libraries/redslider/vendor/**/docs',
					'!../extensions/libraries/redslider/vendor/**/doc/**/*',
					'!../extensions/libraries/redslider/vendor/**/doc',
					'!../extensions/libraries/redslider/vendor/**/composer.*',
					'!../extensions/libraries/redslider/vendor/**/phpunit*',
					'!../extensions/libraries/redslider/vendor/**/Vagrantfile',
					'!../extensions/modules/**/.gitkeep',
					'!../extensions/plugins/**/.gitkeep',
					'!../extensions/plugins/**/section_red*',
					'!../extensions/plugins/**/section_red*/**/*',
					'!../extensions/**/composer.lock',
					'!../extensions/**/composer.json'
				])
				.pipe(zip(fileName))
				.pipe(gulp.dest(config.release_dir))
				.on('end', cb);
		});
	});
});

// Override of the release script
gulp.task('release:redslider', ['release:redslider:package'], function() {
	// Clean up temporary files
	return del([
		'../extensions/media',
		'../extensions/modules/site/*/media',
		'../extensions/plugins/*/*/media',
		'!../extensions/plugins/redslider_addons/aesir_dam/media',
		'../extensions/redCORE'
		],
		{ force: true }
	);
});

// Override of the release script
gulp.task('release:full_package', ['release:redslider:full_package'], function() {
	// Clean up temporary files
	return del([
		'../extensions/media',
		'../extensions/modules/site/*/media',
		'../extensions/plugins/*/*/media',
		'!../extensions/plugins/redslider_addons/aesir/dam/media',
		'../extensions/redCORE'
		],
		{ force: true }
	);
});

function pluginRelease(group, name) {
	var fileName = 'plg_' + group + '_' + name;

	if (!argv.skipVersion) {
		fs.readFile('../extensions/plugins/' + group + '/' + name + '/' + name + '.xml', function(err, data) {
			parser.parseString(data, function (err, result) {
				fileName += '-v' + result.extension.version[0] + '.zip';

				return gulp.src('../extensions/plugins/' + group + '/' + name + '/**')
					.pipe(zip(fileName))
					.pipe(gulp.dest(config.release_dir + '/plugins'));
			});
		});
	}
	else {
		return gulp.src('../extensions/plugins/' + group + '/' + name + '/**')
			.pipe(zip(fileName + '.zip'))
			.pipe(gulp.dest(config.release_dir + '/plugins'));
	}
}

// Task for release plugins
gulp.task('release:plugins', ['release:redslider:plugins'],
	function(cb) {
	var basePath = '../extensions/plugins';
	var plgGroup = argv.group ? argv.group : false;
	var plgName  = argv.name ? argv.name : false;

	// No group specific, release all of them.
	if (!plgGroup) {
		var groups = getFolders(basePath);

		for (var i = 0; i < groups.length; i++) {
			var plugins = getFolders(basePath + '/' + groups[i]);

			for (j = 0; j < plugins.length; j++) {
				pluginRelease(groups[i], plugins[j]);
			}
		};
	}
	else if (plgGroup && !plgName) {
		try {
			fs.statSync('../extensions/plugins/' + plgGroup);
		}
		catch (e) {
			console.error("Folder not exist: " + basePath + '/' + plgGroup);
			return;
		}

		var plugins = getFolders(basePath + '/' + plgGroup);

		for (i = 0; i < plugins.length; i++) {
			pluginRelease(plgGroup, plugins[i]);
		}
	}
	else
	{
		try {
			fs.statSync('../extensions/plugins/' + plgGroup + '/' + plgName);
		}
		catch (e) {
			console.error("Folder not exist: " + basePath + '/' + plgGroup + '/' + plgName);
			return;
		}

		pluginRelease(plgGroup, plgName);
	}
});
