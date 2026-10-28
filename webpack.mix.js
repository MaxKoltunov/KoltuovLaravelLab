const mix = require('laravel-mix');

mix.js('resources/js/web02-app.js', 'public/js/bundle.js')
   .sass('resources/scss/app.scss', 'public/css/styles.css');
