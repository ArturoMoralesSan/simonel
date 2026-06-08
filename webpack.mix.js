const mix = require('laravel-mix');

mix.options({
    processCssUrls: false,
    uglify: {
        uglifyOptions: {
            compress: { pure_funcs: ['console.log', 'console.info'] },
            output: { comments: '/^!/' }
        }
    }
});


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

 mix.js('resources/js/admin', 'public/js').vue();
 mix.js('resources/js/main', 'public/js').vue();
 mix.js('resources/js/vendor', 'public/js').vue();
 //mix.js('resources/js/tinymce', 'public/js');
 
 mix.sass('resources/sass/main.scss', 'public/css', {
    implementation: require('node-sass')
});
mix.sass('resources/sass/dashboard.scss', 'public/css', {
    implementation: require('node-sass')
});
 
