const webpack = require('webpack');

module.exports = {

    devtool: 'inline-source-map', // TODO RENS: dit zou eigenlijk automatisch moeten werken in Elixer.
    entry: {

        app: './resources/assets/js/app.js',
        vendor: [

            // By grouping all vendor dependencies in a single file, we can utilize browser caching for them.

            'chart.js',
            'corejs-typeahead',
            'jquery',
            'moment',
            'numeral',
            'sortablejs',
            'vue',
            'vue-resource',
            'vue-router',
            'vuex',
            'vuex-router-sync',

            // Include the bootstrap file here to register some dependencies to the window object
            // that are needed before the main app is actually executed.

            './resources/assets/js/vendor.js'
        ]
    },
    output: {

        chunkFilename: 'chunks/[id].app.js', // [id].[name].js should work but somehow doesn't.
        //chunkFilename: 'chunks/[id].app.[chunkhash].js', // [id].[name].js should work but somehow doesn't.
        filename: '[name].js',
        publicPath: 'js/' // We ned to specify where content that can be loaded on-demand is located.
        //path: './public/js'
    },
    plugins: [

        new webpack.optimize.CommonsChunkPlugin({

            names: ['vendor', 'manifest']
        })
    ]
};