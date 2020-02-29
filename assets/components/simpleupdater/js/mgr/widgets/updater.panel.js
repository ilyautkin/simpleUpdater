simpleUpdater.panel.Updater = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'simpleupdater-updater-panel';
    }
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        url: simpleUpdater.config.connectorUrl,
        layout: 'anchor',
        hideMode: 'offsets',
        baseParams: {
            action: 'mgr/version/update'
        },
        listeners: {
            afterrender: function () {
                MODx.Ajax.request({
                    url: simpleUpdater.config.connectorUrl,
                    params: {
                        action: 'mgr/version/check',
                    },
                    listeners: {
                        success: {
                            fn: function (response) {
                                if (response.object.show_button) {
                                    Ext.get(config.id + '-version_info').dom.innerHTML = response.object.changelog;
                                    Ext.getCmp(config.id + '-update-available').show().setTitle('MODX Revolution ' + response.object.version);
                                } else {
                                    Ext.getCmp(config.id + '-no-update-available').show();
                                }
                            }, scope: this
                        }
                    }
                });
            }
        },
        items: [{
            anchor: '100%',
            layout: 'anchor',
            id: config.id + '-update-available',
            hidden: true,
            title: 'TEST',
            items: [{
                xtype: 'textarea',
                id: config.id + '-version_info',
                anchor: '100%',
                style: {
                    height: '400px',
                    marginBottom: '10px'
                },
                readOnly: true,
                focusClass: ''
            }, {
                xtype: 'button',
                text: _('simpleupdater_update_start'),
                fieldLabel: _('simpleupdater_update_start'),
                name: 'start-update',
                id: config.id + '-start-update',
                cls: 'primary-button',
                listeners: {
                    click: {
                        fn: this._startUpdate,
                        scope: this
                    }
                }
            }]
        }, {
            html: '<span>' + _('simpleupdater_no_update_available') + '</span>',
            id: config.id + '-no-update-available',
            anchor: '100%',
            autoHeight: true,
            hidden: true,
        }, {
            html: '<div class="loading-indicator">' + _('loading') + '</div>',
            id: config.id + '-update-log',
            anchor: '100%',
            autoHeight: true,
            cls: 'panel-desc',
            hidden: true,
        }]
    });
    simpleUpdater.panel.Updater.superclass.constructor.call(this, config);
};
Ext.extend(simpleUpdater.panel.Updater, MODx.FormPanel, {
    _startUpdate: function () {
        Ext.getCmp(this.config.id + '-update-log').show();
        Ext.getCmp(this.config.id).form.submit({
            url: simpleUpdater.config.connectorUrl,
            success: function () {
                document.location.href = '/setup/';
            },
            failure: function (form, response) {
                Ext.get(config.id + '-update-log').dom.innerHTML = response.result.message;
            }
        });
    }
});
Ext.reg('simpleupdater-updater-panel', simpleUpdater.panel.Updater);