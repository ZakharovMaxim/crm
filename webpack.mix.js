const mix = require('laravel-mix');
mix.disableNotifications();
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

mix.js('resources/src/app.js', 'public/js')
    .sass('resources/src/assets/sass/app.scss', 'public/css').webpackConfig({
        resolve: {
          alias: {
            '@': path.resolve('resources/src')
          }
        },
        
        module: {
          rules: [
            {
              enforce: 'pre',
              test: /\.(js|vue)$/,
              loader: 'eslint-loader',
              exclude: /node_modules/
            }
          ]
        }
      });
