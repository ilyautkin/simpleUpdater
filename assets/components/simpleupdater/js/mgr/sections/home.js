simpleUpdater.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        formpanel: 'simpleupdater-panel-home',
        components: [{
            xtype: 'simpleupdater-panel-home'
        }]
    });
    simpleUpdater.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(simpleUpdater.page.Home, MODx.Component);
Ext.reg('simpleupdater-page-home', simpleUpdater.page.Home);
