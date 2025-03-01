const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

// Get the production mode flag
const isProduction = process.env.NODE_ENV === 'production' || process.argv.includes('--mode=production');

// Configure paths
const PATHS = {
  src: path.resolve(__dirname, '../assets/js/src'),
  dist: path.resolve(__dirname, '../assets/js/dist'),
  css: path.resolve(__dirname, '../assets/css')
};

// List of entry points
const entry = {
  admin: path.resolve(PATHS.src, 'admin.js')
};

module.exports = {
  mode: isProduction ? 'production' : 'development',
  entry,
  output: {
    path: PATHS.dist,
    filename: '[name].js',
    clean: true
  },
  devtool: isProduction ? false : 'source-map',
  module: {
    rules: [
      // JavaScript
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              ['@babel/preset-env', { targets: 'defaults' }],
              '@babel/preset-react'
            ]
          }
        }
      },
      // SCSS/CSS
      {
        test: /\.(scss|css)$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins: [
                  'autoprefixer'
                ]
              }
            }
          },
          'sass-loader'
        ]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '../css/[name].css'
    })
  ],
  optimization: {
    minimizer: [
      new TerserPlugin({
        terserOptions: {
          format: {
            comments: false
          }
        },
        extractComments: false
      }),
      new CssMinimizerPlugin()
    ]
  },
  externals: {
    // WordPress dependencies
    '@wordpress/element': 'wp.element',
    '@wordpress/components': 'wp.components',
    '@wordpress/api-fetch': 'wp.apiFetch',
    '@wordpress/i18n': 'wp.i18n',
    // jQuery
    jquery: 'jQuery'
  }
};