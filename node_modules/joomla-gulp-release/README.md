Gulp for Joomla! Components release
============
Gulp release builder for Standard Joomla! Components, Modules, Plugins and Libraries.

### Install
#### To install all the dependencies,

```
sudo npm install --save-dev gulp
```

#### After installing `gulp` now you need to install `joomla-gulp-release`

```
sudo npm install --save-dev joomla-gulp-release
```

### Using Gulp build system

#### Create `gulpfile.js` in your component root repository.

You only need to add following line in file and then execute below commands.

```
var jrelease = require('joomla-gulp-release');
```

### Configuration file `gulp-config.json`


> Copy and change default information given in sample config file.


### Following tasks and switches are available:

#### To Release `component` and create `.zip` file

> Use this command to release component. Version and other information can be set in `gulp-config.json` file.

    gulp release:component

#### To Release `modules` and create `.zip` file

    gulp release:modules

#### To Release `plugins` and create `.zip` file

    gulp release:plugins

#### To Release `packages` and create `.zip` file

    gulp release:packages

_or_

    gulp release:packages --folder ./individual_package_dir


#### To release an extension - Alternative of individual 'plugins' and 'modules' commands

    gulp release:extensions


> This command will read the base directory and create zip files for each of the folder.

#### === Switches ===
> Pass an argument to choose different folder

    --folder {source direcory}  Default: "./plugins"

> Pass an argument to change suffix for extension

    --suffix {text of suffix}   Default: "plg_"

#### Example Usage:

    gulp release:extensions --folder ./modules --suffix ext_


#### Help
> Gulp Default task is set to show you help.

    gulp
