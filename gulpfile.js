var elixir = require('laravel-elixir');


//elixir.config.assetsPath = './Modules/Schedule/node_modules/';
//elixir.config.css.outputFolder = 'css';
//elixir.config.js.outputFolder  = 'js';
//    assetsPath: 'resources/assets',
//elixir.config.publicPath = './Modules/Schedule/Assets',
//    appPath: 'app',
//    viewPath: 'resources/views',

//elixir.config.imgOutput = 'public/images';            // normally not an elixir configuration property, but used by gulp-elixir-modules
//elixir.config.bowerDir  = 'vendor/bower_components';

/**
 * Set Elixir Source Maps
 *
 * @type {boolean}
 */
elixir.config.sourcemaps = true;

/**
 * Remove all CSS comments
 *
 * @type {{discardComments: {removeAll: boolean}}}
 */
elixir.config.css.minifier.pluginOptions = {
    discardComments: {
        removeAll: true
    }
};

/**
 * Directory for bower source files.
 * If changing this, please also see .bowerrc
 *
 * @type {string}
 */
//var bowerDir = '../bower';

elixir(function(mix) {

    /**
     * CSS configuration
     */
    mix.copy(
        'node_modules/bootstrap/dist/css/bootstrap.css'
    , 'Assets/css/bootstrap.css');


    /**
     * JS configuration
     */

    mix.copy(
        'node_modules/bootstrap/dist/js/bootstrap.js'
    , 'Assets/js/bootstrap.js');



});
