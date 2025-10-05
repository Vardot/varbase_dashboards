const path = require('path');
const isDev = (process.env.NODE_ENV !== 'production');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const StylelintPlugin = require('stylelint-webpack-plugin');
const autoprefixer = require('autoprefixer');
const postcssSorting = require('postcss-sorting');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

const stylelintConfig = require('./.stylelintrc.json');

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
              url: {
                filter: (url) => {
                  // Don't handle sprite svg or web-dashboard svg
                  if (url.includes('sprite.svg') || url.includes('web-dashboard.svg')) {
                    return false;
                  }

                  return true;
                },
              },
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: isDev,
              postcssOptions: {
                plugins: [
                  autoprefixer(),
                  postcssSorting({
                    'properties-order': stylelintConfig.rules['order/properties-order'],
                  }),
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
    new MiniCssExtractPlugin(),
    new StylelintPlugin({
      files: 'css/**/*.css',
      fix: true,
      lintDirtyModulesOnly: false,
    }),
  ],
  watchOptions: {
    aggregateTimeout: 300,
    ignored: ['**/*.woff', '**/*.json', '**/*.woff2', '**/*.jpg', '**/*.png', '**/*.svg', 'node_modules', 'images'],
  }
};
