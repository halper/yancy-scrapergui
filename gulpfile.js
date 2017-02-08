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

elixir(mix => {
    mix.less('AdminLTE.less')
    .less('skins/skin-blue.less', 'public/css/skins/skin-blue.min.css')
    .sass('app.scss', 'public/css/bootstrap.min.css')
    .webpack(['app.js', 'adminlte.js', 'bootstrap.js'])
    .webpack('home.js')
    .webpack('site.js');

});
