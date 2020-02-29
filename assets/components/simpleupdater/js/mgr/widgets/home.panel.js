simpleUpdater.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        cls: 'container home-panel',
        defaults: {
            collapsible: false,
            autoHeight: true
        },
        items: [{
            html: '<h2>' + _('simpleupdater') + '</h2>',
            border: false,
            cls: 'modx-page-header'
        }, {
            defaults: {
                autoHeight: true
            },
            border: true,
                items: [{
                    xtype: 'modx-tabs',
                    autoScroll: true,
                    deferredRender: false,
                    forceLayout: true,
                    defaults: {
                        layout: 'form',
                        autoHeight: true,
                        hideMode: 'offsets'
                    },
                    items: [{
                        title: _('update'),
                        items: [{
                            html: '<p>' + _('simpleupdater_intro_msg') + '</p>',
                            border: false,
                            cls: 'panel-desc'
                        }, {
                            layout: 'form',
                            cls: 'x-form-label-left main-wrapper',
                            defaults: {
                                autoHeight: true
                            },
                            border: true,
                            items: [{
                                xtype: 'simpleupdater-updater-panel'
                            }]
                        }]
                    }]
                }]
        }]
    });
    simpleUpdater.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(simpleUpdater.panel.Home, MODx.Panel);
Ext.reg('simpleupdater-panel-home', simpleUpdater.panel.Home);
