var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .addStyleEntry('css/app', './assets/css/styles.scss')
    .addEntry('images/favicon', './assets/images/favicon.ico')
    .addEntry('images/main-logo', './assets/images/main-logo.png')
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
