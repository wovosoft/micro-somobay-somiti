const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .webpackConfig(require('./webpack.config'));

mix.options({
    hmrOptions: {
        host: 'localhost',
        port: '8079'
    },
});

mix.js("resources/js/admin.js", "public/js")
    .vue()
    .css('resources/css/admin.css', 'public/css')
    .webpackConfig(require('./webpack.config'));

if (mix.inProduction()) {
    mix.version();
}
