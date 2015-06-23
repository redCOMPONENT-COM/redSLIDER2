var gulp = require('gulp'),

// ZIP compress files
zip = require('gulp-zip'),

// Utility functions for gulp plugins
gutil = require('gulp-util')

// File systems
fs          = require('fs'),
path        = require('path'),
merge       = require('merge-stream'),
parseString = require('xml2js').parseString,

// Gulp Configuration
config = require('./../../gulp-config.json')

// Extension Configuration file
extensionConfig = require('./package.json')

// Init manifest file JSON Object
manifest = {},

// Init component version - Set default as config version
component = {"version":'',"compatibility":'',"pluginVersion":''};

component.getFolders = function (dir) {
	return fs.readdirSync(dir)
		.filter(function(file) {
			return fs.statSync(path.join(dir, file)).isDirectory();
	});
};

component.showHelp = function (){
	gutil.log(
		gutil.colors.white.bold.bgMagenta(
			'\n\n\nFollowing tasks and switches are available:\n\n\t 1. gulp release:component \n\t\t Use this command to release component. Version and other information can be set in gulp-config.json file. \n\n\t 2. gulp release:extensions \n\t\t This command is to release the extensions.\n\t\t This command will read the base directory and create zip files for each of the folder. \n\t\t === Switches === \n\t --folder {source direcory}  Default: "./plugins" \n\t --suffix {text of suffix}   Default: "plg_"\n\n\t Example Usage: \n\t\t gulp release:extensions --folder ./modules --suffix ext_ \n\n\n'
		)
	);
};

component.readManifest = function (xml){
	return parseString(fs.readFileSync(xml, 'ascii'), function (err, result) {
		manifest = result;
	});
};

// Reading manifest file
component.readManifest('./' + config.name + '.xml');

// Update version from manifest file
component.version = manifest.extension.version[0];

/**
 * Create zip file of the extensions
 *
 * @param   {string}  srcFolder  Source folder path
 * @param   {string}  folder     Path to the folder directory
 * @param   {string}  plugin     Path of the final plugin folder
 *
 * @return  {object}  Gulp piped task
 */
component.zipper = function (srcFolder, folder, plugin){

	// Extension package name suffix
	var extSuffix  = gutil.env.suffix || '',

	pluginBasePath = path.join(srcFolder, folder, plugin),
	componentName  = config.name;

	// Reading manifest file
	component.readManifest(path.join(pluginBasePath, plugin + '.xml'));

	// Update version from manifest file
	component.pluginVersion = manifest.extension.version[0];
	component.compatibility = (manifest.extension.componentName) ? '_for_' + config.name + '_' + manifest.extension.componentName[0] : '';

	// Strip group name for modules
	var extGroupName = ('site' == folder || 'admin' == folder || '' == folder) ? '' : folder + '_',
	destFolderName = config.name + '-' + component.version + '-' + srcFolder.split(path.sep)[1];

	// Print current extension name
	gutil.log(gutil.colors.red.bold.italic('-' + plugin + ' v' + component.pluginVersion));

	return gulp.src(
			path.join(pluginBasePath, '**')
		)
		.pipe(
			zip(
				extSuffix + extGroupName + plugin + '_' + component.pluginVersion + component.compatibility + '.zip'
			)
		)
		.pipe(
			gulp.dest(
				path.join(config.releasesDir, destFolderName, folder)
			)
		);
};

/**
 * Create zip of the exensions
 *
 * @param   {string}  srcFolder  Source folder to read
 *
 * @return  {array}   Return the array of gulp tasks
 */
component.extensions = function (srcFolder){

	// Read all the folders in given source directory
	var folders = component.getFolders(srcFolder);

	// Display log
	gutil.log(gutil.colors.white.bgBlue(folders.length) + gutil.colors.blue.bold(' extensions are ready for release'));

	// Loop through the folders and create zip files for each of them.
	var tasks;

	folders.map(function(folder) {

		var plugins = component.getFolders(path.join(srcFolder, folder));

		// Display name of the folder
		gutil.log(gutil.colors.blue.bold.italic(folder + ' (' + plugins.length + ')'));

		tasks = plugins.map(function(plugin) {

			return component.zipper(srcFolder, folder, plugin);
		});
	});

	return tasks;
};

/**
 * Gulp task to release an extensions of Joomla.
 *
 *
 * @return  {object}     Gulp task
 */
gulp.task('release:extensions', function() {

	// Source directory for read and prepare for zip
	var srcFolder = gutil.env.folder || './plugins';

	return merge(
		component.extensions(srcFolder)
	);
});

/**
 * Release Joomla! Plugins
 *
 * @return  {Object}     Gulp Task
 */
gulp.task('release:plugins', function() {
	return merge(
		component.extensions('./plugins')
	);
});

/**
 * Release Joomla! Modules
 *
 * @return  {Object}     Gulp task
 */
gulp.task('release:modules', function() {
	return merge(
		component.extensions('./modules')
	);
});

/**
 * Release Joomla! Modules
 *
 * @return  {Object}     Gulp task
 */
gulp.task('release:packages', function() {

	// Source directory for read and prepare for zip
	var srcFolder = gutil.env.folder || './packages',

	packages = component.getFolders(path.join(srcFolder));

	// Display name of the folder
	gutil.log(gutil.colors.white.bgBlue(packages.length) + gutil.colors.blue.bold(' packages are ready for release'));

	var tasks = packages.map(function(plugin) {

		return component.zipper(srcFolder, '', plugin);
	});
});

/**
 * Create a release zip file for joomla! component
 *
 * @return  {object}     Gulp Task
 */
gulp.task('release:component', function() {

	if (!config.packageFiles || (config.packageFiles && config.packageFiles.length <= 0))
	{
		gutil.log(
			gutil.colors.white.bgRed(
				'ERROR: Please specify `packageFiles` in gulp-config.json or make sure you have added files list'
			)
		);

		return false;
	}

	// Start up log
	gutil.log(gutil.colors.white.bgGreen('Preparing release for version ' + component.version));

	gulp.src(config.packageFiles, {base: '.'})
		.pipe(zip(config.name + '_v' + component.version + '_' + config.joomlaVersion + '.zip'))
		.pipe(gulp.dest(config.releasesDir));

	gutil.log(gutil.colors.white.bgGreen('Component packages are ready at ' + config.releasesDir));
});

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
		'release:packages'
	],
	function() {
});

/**
 * Gulp default task to show help
 *
 * @return  {text}    Display help text in console.
 */
gulp.task('default', function() {
	component.showHelp();
});

/**
 * Export component variable to use by plugin
 *
 * @type  {Object}
 */
module.exports = {
	jGulpRelease: component
};
