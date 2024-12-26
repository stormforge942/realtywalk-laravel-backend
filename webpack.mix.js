const mix = require('laravel-mix')


mix
  .webpackConfig({
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'resources/js'),
      },
    },
  })
  // Mix options
  .options({
    cleanCss: {
      level: {
        1: {
          specialComments: 'none'
        }
      }
    },
    postCss: [
      require('postcss-discard-comments')({
        removeAll: true
      })
    ],
    purifyCss: false,
    processCssUrls: false
  })

mix.sass('resources/sass/app.scss', 'public/css')
mix.sass('resources/sass/error.scss', 'public/css')
mix.sass('resources/sass/backend.scss', 'public/css')
mix.sass('resources/sass/general.scss', 'public/css')
mix.sass('resources/sass/property.scss', 'public/css')

mix.autoload({
    jquery: ["$", "window.jQuery", "jQuery"],
    vue: ["Vue", "window.Vue", "index.vue"],
    moment: ["moment", "window.moment"]
});

mix.js('resources/js/app.js', 'public/js')

mix.js('resources/js/backend.js', 'public/js')

if (mix.inProduction()) {
    mix.version()
}

mix.copy('node_modules/@coreui/icons/fonts/*', 'public/fonts')
   .copy('node_modules/@coreui/icons/sprites/*', 'public/icons/coreui')
   .copy('node_modules/font-awesome/fonts/*', 'public/fonts')
