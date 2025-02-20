const webpack = require('webpack');
const {
    merge
} = require('webpack-merge');
const common = require('./webpack.common.js');
const path = require('path');

module.exports = merge(common, {
    mode: 'development',
    devtool: 'inline-source-map',
    watch: true,
    devServer: {
        port: 3000
    }
});