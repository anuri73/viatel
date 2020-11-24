/*
 * Created by Urmat Zhenaliev
 * urmat.zhenaliev@gmail.com
 */

// import plugins
const path = require('path');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default;

/**
 * Base webpack configuration
 *
 * @param env -> env parameters
 * @param argv -> CLI arguments, 'argv.mode' is the current webpack mode (development | production)
 * @returns object
 */
module.exports = (env, argv) => {
    let isProduction = (argv.mode === 'production');

    let config = {
        // development server with hot-reload
        devServer: {
            publicPath: './../../dist/',
            watchContentBase: true,
            compress: true,
        },

        // entry files to compile (relative to the base dir)
        entry: {
            "setting": "./src/settings/index.js",
            "widget": "./src/widget/index.js",
            "logo": "./src/viatel-logga.png",
        },

        // enable development source maps
        // * will be overwritten by 'source-maps' in production mode
        devtool: "inline-source-map",

        // path to store compiled JS bundle
        output: {
            // bundle relative name
            filename: "[name].js",
            // base build directory
            path: path.resolve(__dirname, "dist"),
            // path to build relative asset links
            publicPath: "../"
        },

        // plugins configurations
        plugins: [
            // save compiled SCSS into separated CSS file
            new MiniCssExtractPlugin({
                filename: "[name].css"
            }),

            // image optimization
            new ImageminPlugin({
                // disable for dev builds
                disable: !isProduction,
                test: /\.(jpe?g|png|gif)$/i,
                pngquant: {quality: '70-85'},
                optipng: {optimizationLevel: 9}
            }),

            // provide jQuery and Popper.js dependencies
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                jquery: 'jquery',
                'window.jQuery': 'jquery',
                Popper: ['popper.js', 'default']
            }),
        ],

        // production mode optimization
        optimization: {
            minimizer: [
                // CSS optimizer
                new OptimizeCSSAssetsPlugin(),
                // JS optimizer by default
                new TerserPlugin(),
            ],
        },

        // custom loaders configuration
        module: {
            rules: [
                // styles loader
                {
                    test: /\.(sa|sc|c)ss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        "css-loader",
                        "sass-loader"
                    ],
                },

                // images loader
                {
                    test: /\.(png|jpe?g|gif)$/,
                    loaders: [
                        {
                            loader: "file-loader",
                            options: {
                                name: "img/[name].[ext]"
                            }
                        },
                        {
                            loader: 'image-webpack-loader',
                            options: {
                                disable: !isProduction,
                                mozjpeg: {
                                    progressive: true,
                                    quality: 65
                                },
                                pngquant: {
                                    quality: '65-90',
                                    speed: 4
                                },
                                optipng: {enabled: false},
                                gifsicle: {interlaced: false},
                                webp: {quality: 75}
                            }
                        },
                    ],
                },

                // fonts loader
                {
                    test: /\.(woff|woff2|eot|ttf|otf)$/,
                    use: [
                        {
                            loader: "file-loader",
                            options: {
                                name: "fonts/[name].[ext]"
                            }
                        },
                    ],
                },

                // svg inline 'data:image' loader
                {
                    test: /\.svg$/,
                    loader: "svg-url-loader"
                },
            ]
        },
    };

    // PRODUCTION ONLY configuration
    if (isProduction) {
        config.plugins.push(
            // clean 'dist' directory
            new CleanWebpackPlugin()
        );
    }

    return config;
};