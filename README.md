redSLIDER
==========

redSLIDER is a Joomla extension that allows you to create a carrussel of images or videos. This new version is a refactor of the old FoF based extension https://github.com/redCOMPONENT-COM/redSLIDER now based in redCORE framework.

# Releasing

## With PHING

Rename `build.properties.dist` into `build.properties` and modify it according to your server details

Add extension_packager.xml as PHING build file and execute it.

To generate the installable package with the core plugins run: `full_packager.xml`

To generate the special plugins (reform, redshop, redevent integrations) packages run:`plugins_packager.xml` 

## With Gulp

Before you can run any Gulp command you need to:

- `cd build`
- copy `gulp-config.dist.json` to `gulp-config.json
- download and install NodeJS: https://nodejs.org/download/
- install npm: `sudo npm install`
- install global Gulp: `npm install --global gulp`
- install Gulp in the project devDependencies: `npm install --save-dev gulp`
- install joomla-gulp-release: `sudo npm install --save-dev joomla-gulp-release`

To generate the installable package with the core plugins run:
- `gulp release:redslider:full_package`

To generate the special plugins (reform, redshop, redevent integrations) packages run:
- `gulp release:redslider:plugins` 
