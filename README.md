redSLIDER2
==========

[![Build Status](https://magnum.travis-ci.com/redCOMPONENT-COM/redSLIDER2.svg?token=vxVVpxnq2ZPuMp3yebRz)](https://magnum.travis-ci.com/redCOMPONENT-COM/redSLIDER2)

redSLIDER 2.0 is a Joomla extension that allows you to create a carrussel of images or videos. This new version is a refactor of the old FoF based extension https://github.com/redCOMPONENT-COM/redSLIDER now based in redCORE framework.

# Releasing

## With PHING

Rename `build.properties.dist` into `build.properties` and modify it according to your server details

Add extension_packager.xml as PHING build file and execute it.

## With Gulp

Rename `gulp-config.dist.json` into `gulp-config.json` and modify if needed (rarely needed)


Before you can run any Gulp command you need to:

- download and install NodeJS. See https://nodejs.org/download/
- type `gulp` to see instructions to execute