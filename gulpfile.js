var elixir = require('laravel-elixir');

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
    .copy('node_modules/jquery/dist/jquery.min.js', 'public/js')
    .copy('node_modules/bootstrap-sass/assets/fonts/bootstrap/**', 'public/fonts/bootstrap')
    .copy('node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js', 'public/js');

    mix.copy('node_modules/font-awesome/css/font-awesome.css.map', 'public/css')
    .copy('node_modules/font-awesome/css/font-awesome.min.css', 'public/css')
    .copy('node_modules/font-awesome/fonts/**', 'public/fonts');
});
