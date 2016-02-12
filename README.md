# Primer Backstop
A module for [Primer](https://github.com/Rareloop/primer) that provides simple visual regression testing using [BackstopJS](https://github.com/garris/BackstopJS).

## Pre-requisites
BackstopJS requires a few NPM modules to be installed globally:

````
sudo npm install -g gulp
sudo npm install -g phantomjs
sudo npm install -g casperjs
````

*This module assumes you're using a Unix based system (e.g. Linux, Mac OS X). It hasn't been tested on Windows but almost certainly won't work without some tweaking!*

## Installation
This module isn't currently on Packagist so you'll need to add a custom repository to your `composer.json`.

````json
"repositories": [
    {
        "type": "vcs",
        "url": "git@gitlab.rareloop.com:php-packages/primer-backstop.git"
    }
]
````

Add the following to you `require` object:

````json
"rareloop/primer-backstop": "dev-master"
````

We also need to install some non PHP dependencies so add the following to your `composer.json`:

````json
"scripts": {
    "post-install-cmd": [
        "cd vendor/rareloop/primer-backstop && npm install"
    ],

    "post-update-cmd": [
        "cd vendor/rareloop/primer-backstop && npm install"
    ]
}
````

Update your dependencies:

````
composer update
````

## Usage
Once installed you'll need to add some commands to Primer. Edit your `bootstrap/start.php` and add the following:

````php
Event::listen('cli.init', function ($cli) {
    $cli->add(new \Rareloop\Primer\Backstop\Commands\ConfigCommand);
    $cli->add(new \Rareloop\Primer\Backstop\Commands\ReferenceCommand);
    $cli->add(new \Rareloop\Primer\Backstop\Commands\TestCommand);
});
````

This will add 3 commands to the Primer CLI.

### Configuration
````
php primer backstop:config
````

This will create a file in the root of your project called `backstop.config.js`. Edit this file to update the viewports you wish to test.

### Reference Images
````
php primer backstop:reference
````

This creates reference images for all elements and components at each viewport size and stores them in `backstop/bitmaps_reference`. These images are the baseline images that future tests will be run against.

By default reference images will be created for all your `elements` and `components`. You can change this by adding which sections you want to test to the CLI, e.g.

````
# Test templates and components but not elements
php primer backstop:reference --templates --components
````

### Run Test
````
php primer backstop:test
````

This creates reference images for all elements and components at each viewport size and compares them against the reference images already created. Depending on the settings in `backstop.config.js` this will present a report via the CLI and/or open a browser for more visual feedback.
