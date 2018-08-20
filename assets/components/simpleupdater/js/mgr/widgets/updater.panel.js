simpleUpdater.panel.Updater = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'simpleupdater-updater-panel';
    }
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        url: simpleUpdater.config.connector_url,
        config: config,
        layout: 'anchor',
        hideMode: 'offsets',
		baseParams: {
			action: 'mgr/version/update'
		},

		items: [{
            xtype: 'button',
            text: _('simpleupdater_update_start'),
            fieldLabel: _('simpleupdater_update_start'),
            name: 'start-update',
            id: config.id + '-start-update',
            cls: 'primary-button',
			listeners: {
				click: {fn: this._startUpdate, scope: this}
			}
        }, {
            xtype: 'modx-panel',
            id: config.id + '-update-log',
            anchor: '100%',
            autoHeight: true,
            cls: 'panel-desc',
            style: {display: 'none', 'max-height': '250px', overflow: 'auto'}
        }]
	});
	simpleUpdater.panel.Updater.superclass.constructor.call(this, config);
};
Ext.extend(simpleUpdater.panel.Updater, MODx.FormPanel, {
    
    _startUpdate: function() {
        document.getElementById('simpleupdater-updater-panel-update-log').style.display = "block";
        document.getElementById('simpleupdater-updater-panel-update-log').innerHTML = '<div class="loading-indicator">' + _('loading') + '</div>';
        Ext.getCmp(this.config.id).form.submit({
            url: simpleUpdater.config.connector_url,
            success: function(form, response){
                document.location.href = '/setup/';
            },
            failure: function(form, response){
                document.getElementById('simpleupdater-updater-panel-update-log').innerHTML = response.result.message;
            }
        });
    }
    
});
Ext.reg('simpleupdater-updater-panel', simpleUpdater.panel.Updater);