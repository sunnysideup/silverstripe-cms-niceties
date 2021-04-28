/*
 * Development assets generation
 */

const path = require('path');
//const autoprefixer = require('autoprefixer');
const webpack = require('webpack');
const { merge } = require('webpack-merge');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');

const common = require('./webpack.config.common.js');
const commonVariables = require('./webpack.configuration');
const conf = commonVariables.configuration;

const IP = process.env.IP || conf.HOSTNAME;
const PORT = process.env.PORT || conf.PORT;

const UIInfo = require('./package.json');

const NODE_ENV = 'development'; //conf.NODE_ENV || process.env.NODE_ENV;
const COMPRESS = NODE_ENV === 'production' ? true : false;

console.log('NODE_ENV: ' + NODE_ENV);
console.log('COMPRESS: ' + COMPRESS);
console.log('WebP images: ' + conf['webp']);

const config = merge(common, {
  mode: 'development',

  entry: {
    /*hot: [
      'react-hot-loader/patch',
      'webpack-dev-server/?https://' + conf.HOSTNAME + ':' + conf.PORT,
      'webpack/hot/only-dev-server',
    ],*/
  },

  output: {
    path: path.join(__dirname),
    filename: '[name].js',
    // necessary for HMR to know where to load the hot update chunks
    publicPath: 'https://' + conf.HOSTNAME + ':' + conf.PORT + '/',
  },

  module: {
    rules: [
      {
        test: /\.jsx?$/,
        //exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              '@babel/preset-env',
              '@babel/react',
              {
                plugins: ['@babel/plugin-proposal-class-properties'],
              },
            ], //Preset used for env setup
            plugins: [['@babel/transform-react-jsx']],
            cacheDirectory: true,
            cacheCompression: true,
          },
        },
      },
      {
        test: /\.s?css$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          {
            loader: 'css-loader',
            options: {
              sourceMap: !COMPRESS,
            },
          },
          {
            loader: 'resolve-url-loader',
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: false,
            },
          },
        ],
      },
      {
        test: /fontawesome([^.]+).(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
        use: [
          {
            loader: 'url-loader',
          },
        ],
      },
      {
        test: /\.(gif|png|jpg|jpeg|ttf|otf|eot|svg|webp|woff(2)?)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name(file) {
                return 'public/[path][name].[ext]';
              },
            },
          },
        ],
      },
    ],
  },
  plugins: [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
      Popper: ['popper.js', 'default'],
      Util: 'exports-loader?Util!bootstrap/js/dist/util',
      Alert: 'exports-loader?Alert!bootstrap/js/dist/alert',
      Button: 'exports-loader?Button!bootstrap/js/dist/button',
      Carousel: 'exports-loader?Carousel!bootstrap/js/dist/carousel',
      Collapse: 'exports-loader?Collapse!bootstrap/js/dist/collapse',
      Dropdown: 'exports-loader?Dropdown!bootstrap/js/dist/dropdown',
      Modal: 'exports-loader?Modal!bootstrap/js/dist/modal',
      Tooltip: 'exports-loader?Tooltip!bootstrap/js/dist/tooltip',
      Popover: 'exports-loader?Popover!bootstrap/js/dist/popover',
      Scrollspy: 'exports-loader?Scrollspy!bootstrap/js/dist/scrollspy',
      Tab: 'exports-loader?Tab!bootstrap/js/dist/tab',
    }),
    new webpack.DefinePlugin({
      UINAME: JSON.stringify(UIInfo.name),
      UIVERSION: JSON.stringify(UIInfo.version),
      UIAUTHOR: JSON.stringify(UIInfo.author),
    }),
    //new webpack.HotModuleReplacementPlugin(),
    new MiniCssExtractPlugin(),
    new HtmlWebpackPlugin({
      publicPath: '',
      template: path.join(conf.APPDIR, conf.SRC, 'index.html'),
      templateParameters: {
        NODE_ENV: NODE_ENV,
        REACT_SCRIPTS:
          NODE_ENV === 'production'
            ? '<script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script><script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>'
            : '<script crossorigin src="https://unpkg.com/react@17/umd/react.development.js"></script><script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.development.js"></script>',
      },
    }),
  ],

  devServer: {
    host: IP,
    port: PORT,
    historyApiFallback: false,
    //hot: true,
    /*clientLogLevel: 'info',
    disableHostCheck: true,
    contentBase: [
      path.resolve(__dirname, 'public'),
      path.resolve(__dirname, 'public', 'resources'),
      path.resolve(__dirname, 'public', 'resources', conf.APPDIR, conf.DIST),
      'node_modules',
    ],*/
    //watchContentBase: true,
    overlay: {
      warnings: true,
      errors: true,
    },
    headers: {
      'Access-Control-Allow-Origin': '*',
    },
  },
});

module.exports = config;
