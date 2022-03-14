const webpack = require('webpack');
const path = require('path');
const dotenv = require('dotenv').config({
    path: __dirname + '/.env'
})
const {
    basePath,
    htmlPages
} = require("./src/config/pages");
const CopyPlugin = require("copy-webpack-plugin");
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
let multipleHtmlPlugins = htmlPages.map(page => {
    return new HtmlWebpackPlugin({
        title: page.title, // output HTML files
        filename: path.join(page.path, '/', page.name), // output HTML files
        template: path.join(basePath, '/', page.path, '/', page.name), // relative path to the HTML files
    })
});
module.exports = {
    entry: './src/js/index.js',
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'static/js/bundle.js',
        clean: true,
        publicPath: '/',
    },
    resolve: {
        extensions: ['*', '.js']
    },
    plugins: [
        new webpack.DefinePlugin({
            'process.env': JSON.stringify(dotenv.parsed),
        }),
        new MiniCssExtractPlugin({
            filename: 'static/css/app.css'
        }),
        new CopyPlugin({
            patterns: [{
                from: "./public",
                filter: async (resourcePath) => {
                    if (path.extname(resourcePath) == ".html") {
                        return false
                    }
                    return true
                },
            }, ],
        }),
    ].concat(multipleHtmlPlugins),
    module: {
        rules: [{
                test: /\.js$/,
                exclude: /node_modules/,
                use: ["babel-loader"]
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            esModule: false
                        }
                    },
                    'sass-loader'
                ],
            },
            {
                test: /\.(eot|woff|ttf)$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        outputPath: 'static/fonts',
                    },
                }]
            },
            {
                test: /\.(svg|gif|png|jpg|jpeg)$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        outputPath: 'static/assets',
                    },
                }]
            }
        ],
    },
};