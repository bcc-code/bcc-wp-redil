
const mix = require('laravel-mix');

mix.autoload({})

    .js('admin/src/js/redil-admin.js'          , 'admin/dist/js/')
    .react()

    .js('admin/src/js/redil-admin-gutenberg.js', 'admin/dist/js/')
    .react()

    .sass('admin/src/scss/redil-admin.scss', 'admin/dist/css/')

    .webpackConfig({
        externals: {
            'react'    : 'React',
            'react-dom': 'ReactDOM'
        }
});