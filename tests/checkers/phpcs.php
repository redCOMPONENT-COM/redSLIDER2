<?php
/**
 * Command line script for executing PHPCS during a Travis build.
 *
 * @copyright  Copyright (C) 2013 - 2020 redWEB.dk, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Only run on the CLI SAPI
(php_sapi_name() == 'cli' ?: die('CLI only'));

// Script defines
define('REPO_BASE', dirname(__DIR__));

// Require Composer autoloader
if (!file_exists(REPO_BASE . '/vendor/autoload.php'))
{
	fwrite(STDOUT, "\033[37;41mThis script requires Composer to be set up, please run 'composer install' first.\033[0m\n");
}

require REPO_BASE . '/vendor/autoload.php';

// Welcome message
fwrite(STDOUT, "\033[32;1mInitializing PHP_CodeSniffer checks.\033[0m\n");

// Ignored files
$ignored = array(
	REPO_BASE . '/../extensions/components/com_redslider/admin/views/*/tmpl/*',
	REPO_BASE . '/../extensions/components/com_redslider/admin/layouts/*',
	REPO_BASE . '/../extensions/components/com_redslider/admin/tables/*',
	REPO_BASE . '/../extensions/libraries/redslider/table/*',
	REPO_BASE . '/../extensions/libraries/redslider/helper/*',
	REPO_BASE . '/../extensions/libraries/redslider/form/*',
	REPO_BASE . '/../extensions/libraries/redslider/vendor/*',
	REPO_BASE . '/../extensions/modules/site/*/tmpl/*',
	REPO_BASE . '/../extensions/modules/site/*/language/*',
	REPO_BASE . '/../extensions/modules/site/*/css/*',
	REPO_BASE . '/../extensions/media/*/css/*',
	REPO_BASE . '/../extensions/media/*/js/*',
	REPO_BASE . '/../extensions/media/*/images/*',
	REPO_BASE . '/../extensions/plugins/*/*/css/*',
	REPO_BASE . '/../extensions/plugins/*/*/js/*',
	REPO_BASE . '/../extensions/plugins/*/*/tmpl/*',
	REPO_BASE . '*.js',
);

// Build the options for the sniffer
$options = array(
	'files'        => array(
		REPO_BASE . '/../extensions/plugins',
		REPO_BASE . '/../extensions/components',
		REPO_BASE . '/../extensions/modules',
		REPO_BASE . '/../extensions/libraries',
	),
	'standard'     => array(REPO_BASE . '/.travis/phpcs/joomla/Joomla'),
	'ignored'      => $ignored,
	'showProgress' => true,
	'verbosity'    => false
);

// Instantiate the sniffer
$phpcs = new PHP_CodeSniffer_CLI;

// Ensure PHPCS can run, will exit if requirements aren't met
$phpcs->checkRequirements();

// Run the sniffs
$numErrors = $phpcs->process($options);

// If there were errors, output the number and exit the app with a fail code
if ($numErrors)
{
	fwrite(STDOUT, sprintf("\033[37;41mThere were %d issues detected.\033[0m\n", $numErrors));
	// @todo: when all the codestyle issues will be fixed, please change the following line to exit(1)
	exit(0);
}
else
{
	fwrite(STDOUT, "\033[32;1mThere were no issues detected.\033[0m\n");
	exit(0);
}
