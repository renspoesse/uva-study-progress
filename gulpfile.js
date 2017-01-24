const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {

    mix.sass('app.scss')
        .less('./resources/assets/toolkit/toolkit-inverse.less', 'public/css/toolkit-inverse.css')
        //.less('./resources/assets/toolkit/toolkit-light.less', 'public/css/toolkit-light.css')
        .webpack('app.js');
});