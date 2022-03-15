const webpack = require('webpack');
const {
    merge
} = require('webpack-merge');
const common = require('./webpack.common.js');
const path = require('path');

module.exports = merge(common, {
    mode: 'development',
    devtool: 'inline-source-map',
    devServer: {
        static: './dist',
        compress: true,
        hot: false,
        client: false,
        port: 3000,
    },
    plugins: [
       // Plugin for hot module replacement
       new webpack.HotModuleReplacementPlugin(),
      ],
});