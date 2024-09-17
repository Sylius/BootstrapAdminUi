var Encore = require('@symfony/webpack-encore');
const WebpackRTLPlugin = require('webpack-rtl-plugin');

Encore
  .setOutputPath('./public/')
  .setPublicPath('./')
  .setManifestKeyPrefix('bundles/SyliusBootstrapAdminUi')

  .cleanupOutputBeforeBuild()
  .enableSassLoader()
  .enableSourceMaps(false)
  .enableVersioning(false)
  .disableSingleRuntimeChunk()

  .copyFiles({
    from: './node_modules/@fortawesome/fontawesome-free/webfonts/',
    // relative to the output dir
    to: 'fonts/[name].[hash].[ext]'
  })

  .copyFiles({
       from: './assets/images',
       to: 'images/[path][name].[ext]',
  })

  .addPlugin(new WebpackRTLPlugin())

  .addEntry('app', './assets/entrypoint.js')
  .enableSassLoader()
  .autoProvidejQuery()

module.exports = Encore.getWebpackConfig();
