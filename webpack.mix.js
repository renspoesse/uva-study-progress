let mix = require('laravel-mix');
let path = require('path');

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

        'bootstrap-sass',
        'chart.js',
        'jquery',
        'lodash',
        'medium-editor',
        'moment',
        'numeral',
        'vue',
        'vue-resource',
        'vue-router',
        'vuex',
        'vuex-router-sync'
    ])
    .autoload({

        jquery: ['$', 'jQuery']
    })
    .sass('resources/assets/sass/app.scss', 'public/css')
    .less('resources/assets/toolkit/toolkit-light.less', 'public/css/toolkit.css');

mix.webpackConfig({

    module: {

        rules: [

            {
                test: /\.modernizrrc.js$/,
                use: ['modernizr-loader']
            },
            {
                test: /\.modernizrrc(\.json)?$/,
                use: ['modernizr-loader', 'json-loader']
            }
        ]
    },
    resolve: {

        alias: {

            modernizr$: path.resolve(__dirname, '.modernizrrc')
        }
    }
});

if (mix.config.inProduction) {

    mix.version();
}
else {

    mix.sourceMaps();
}