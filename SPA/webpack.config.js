var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var HtmlWebpackPlugin = require('html-webpack-plugin');
var autoprefixer = require('autoprefixer')({browsers: 'last 5 versions'});
var path = require('path');

const extractMediaCss = new ExtractTextPlugin('style/[name].media.css');
const extractAllCss = new ExtractTextPlugin('style/[name].css');

module.exports = {
    entry: {
        'polyfills': './src/polyfills.ts',
        'vendor': './src/vendor.ts',
        'app': './src/app/main.ts',
    },
    output: {
        path: path.resolve('dist'),
        // publicPath: '',
        filename: '[name].bundle.js'
    },
    resolve: {
        extensions: ['.ts', '.js']
    },
    module: {
        rules: [
            {
                test: /\.ts$/,
                loaders: [{
                    loader: 'awesome-typescript-loader',
                    options: {configFileName: path.resolve('tsconfig.json')}
                }, 'angular2-template-loader']
            },
            {
                test: /\.html$/,
                loader: 'html-loader'
            },
            {
                test: /\.(png|jpe?g|gif|svg|woff|woff2|ttf|eot|ico)$/,
                loader: 'file-loader?name=[name].[ext]&publicPath=assets/img/&outputPath=assets/img/',
                query: {
                    useRelativePath: process.env.NODE_ENV === "production"
                }
            },
            {
                test: /\.sass$/,
                exclude: [/\.global\.sass$/],
                loaders: extractAllCss.extract({
                    fallbackLoader: "style-loader",
                    loader: ['raw-loader', 'postcss-loader', 'sass-loader']
                })

            },
            {
                test: /\.global\.sass$/,
                loaders: extractMediaCss.extract({
                    fallbackLoader: "style-loader",
                    loader: ['css-loader', 'postcss-loader', 'sass-loader']
                })
            },
        ]
    },

    plugins: [
        new webpack.ContextReplacementPlugin(
            /angular(\|\/)core(\|\/)(esm(\|\/)src|src)(\|\/)linker/,
            path.resolve('./src'),
            {}
        ),

        new webpack.optimize.CommonsChunkPlugin({
            name: ['app', 'vendor', 'polyfills']
        }),

        new HtmlWebpackPlugin({
            template: 'src/index.html'
        }),

        new webpack.ProvidePlugin({
            jQuery: 'jquery',
            $: 'jquery',
            jquery: 'jquery'
        }),


        extractAllCss,
        extractMediaCss,


        new webpack.LoaderOptionsPlugin({
            minimize: false,
            options: {
                postcss: [autoprefixer]
            }
        }),

        new webpack.NoEmitOnErrorsPlugin(),

        new webpack.optimize.UglifyJsPlugin({
            mangle: {
                keep_fnames: true
            }
        }),

        new webpack.LoaderOptionsPlugin({
            htmlLoader: {
                minimize: false
            }
        })
    ]
};