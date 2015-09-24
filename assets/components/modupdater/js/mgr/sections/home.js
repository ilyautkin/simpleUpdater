modUpdater.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'modupdater-panel-home', renderTo: 'modupdater-panel-home-div'
		}]
	});
	modUpdater.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(modUpdater.page.Home, MODx.Component);
Ext.reg('modupdater-page-home', modUpdater.page.Home);