var config = {
    // The viewports to test each component at
    viewports: [
        {
            name: 'phone',
            width: 320,
            height: 480
        },
        {
            name: 'tablet_v',
            width: 568,
            height: 1024
        },
        {
            name: 'tablet_h',
            width: 1024,
            height: 768
        }
    ],

    // phantomjs/slimerjs
    engine: 'phantomjs',

    // Runs a CLI and Browser based test report
    report: ['CLI', 'browser'],

    cliExitOnFail: false,
    casperFlags: [],
    debug: false,
    port: 3967,

    /**
     * ----------------------------------------------------------------
     * DON'T EDIT BELOW THIS POINT UNLESS YOU KNOW WHAT YOU'RE DOING!
     * ----------------------------------------------------------------
     */

    scenarios: [],
    paths: {
        bitmaps_reference: '../../backstop_data/bitmaps_reference',
        bitmaps_test: '../../backstop_data/bitmaps_test',
        compare_data: '../../backstop_data/bitmaps_test/compare.json',
        casper_scripts: '../../backstop_data/casper_scripts'
    }
};

var fs = require('fs');

// Build a list of the patterns
var basePath = '../../../patterns/';
var patterns = [];

['elements', 'components'].forEach(function(type) {
    fs.readdirSync(basePath + type).forEach(function(group) {
        if (group.substring(0, 1) !== '.') {
            fs.readdirSync(basePath + type + '/' + group).forEach(function(name) {
                if (name.substring(0, 1) !== '.') {
                    patterns.push(type + '/' + group + '/' + name);
                }
            });
        }
    });
});

patterns.forEach(function(pattern) {
    config.scenarios.push({
      'label': pattern,
      'url': 'http://localhost:8080/patterns/' + pattern + '?minimal',
      'hideSelectors': [],
      'readyEvent': null,
      'delay': 500,
      'misMatchThreshold' : 0.1,
      'onBeforeScript': 'onBefore.js',
      'onReadyScript': 'onReady.js'
    });
});

module.exports = config;
