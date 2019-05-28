const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/vendor.js', 'public/js/vendor-load.js')
    .extract([

        'axios',
        'bootstrap-sass',
        'chart.js',
        'jquery',
        'lodash',
        'medium-editor',
        'moment',
        'vue',
        'vue-analytics',
        'vue-router',
        'vuex',
    ])
    .autoload({

        jquery: ['$', 'jQuery'],
    })
    .sass('resources/assets/sass/app.scss', 'public/css')
    .less('resources/assets/toolkit/toolkit-light.less', 'public/css/toolkit.css');

if (mix.config.inProduction) {

    mix.version();
}
else
    mix.sourceMaps();
