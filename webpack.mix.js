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

const production = process.env.NODE_ENV === 'production';

mix.js('resources/js/app.js', 'public/js')
    .extract(['axios', 'vue', 'datatables.net'])
    .sass('resources/sass/app.scss', 'public/css');
if (production) {
    mix.version();
}

mix.copyDirectory('node_modules/tinymce/plugins', 'public/js/plugins')
    .copyDirectory('node_modules/tinymce/skins', 'public/js/skins')
    .copyDirectory('node_modules/tinymce/themes', 'public/js/themes');
