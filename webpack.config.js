var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .addStyleEntry('css/app', './assets/css/styles.scss')
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
