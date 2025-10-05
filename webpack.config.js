const path = require('path');
const isDev = (process.env.NODE_ENV !== 'production');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const autoprefixer = require('autoprefixer');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = {
  mode: 'production',
  entry: {
    // ################################################
    // SCSS
    // ################################################
    // Theme
    'theme/varbase-dashboards.admin-navigation.theme': ['./scss/theme/varbase-dashboards.admin-navigation.theme.scss'],
    'theme/varbase-dashboards.admin-toolbar.theme': ['./scss/theme/varbase-dashboards.admin-toolbar.theme.scss'],
    'theme/varbase-dashboards.theme': ['./scss/theme/varbase-dashboards.theme.scss'],
  },
  output: {
    path: path.resolve(__dirname, 'css'),
    pathinfo: false,
    publicPath: '',
  },
  module: {
    rules: [
      {
        test: /\.(png|jpe?g|gif|svg)$/,
        exclude: /sprite\.svg$/,
        type: 'asset/resource',
        generator: {
          filename: '../../images/[name][ext]',
          emit: false,
        },
      },
      {
        test: /\.(css|scss)$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          {
            loader: 'css-loader',
            options: {
              sourceMap: isDev,
              importLoaders: 2,
              url: false,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: isDev,
              postcssOptions: {
                plugins: [
                  autoprefixer(),
                ],
              },
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: isDev,
              // Global SCSS imports:
              additionalData: `
                @use "sass:color";
                @use "sass:math";
              `,
            },
          },
        ],
      },
    ],
  },
  resolve: {
    modules: [
      path.join(__dirname, 'node_modules'),
    ],
    extensions: ['.js', '.json'],
  },
  plugins: [
    new RemoveEmptyScriptsPlugin(),
    new CleanWebpackPlugin({
      cleanStaleWebpackAssets: false
    }),
    new MiniCssExtractPlugin({
      ignoreOrder: true,
    }),
  ],
  optimization: {
    minimize: true,
    minimizer: [
      '...',
    ],
  },
  watchOptions: {
    aggregateTimeout: 300,
    ignored: ['**/*.woff', '**/*.json', '**/*.woff2', '**/*.jpg', '**/*.png', '**/*.svg', 'node_modules', 'images'],
  }
};
