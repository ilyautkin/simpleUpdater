modUpdater.panel.Home = function (config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'modupdater-panel-home',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offsets',
		items: [{
			html: '<h2>' + _('modupdater') + '</h2>',
			cls: '',
			style: {margin: '15px 0'}
		}, {
			xtype: 'modx-tabs',
			defaults: {border: false, autoHeight: true},
			border: true,
			hideMode: 'offsets',
			items: [{
                title: _('update'),
				layout: 'anchor',
				items: [{
					html: _('modupdater_intro_msg'),
					cls: 'panel-desc',
				}, {
					xtype: 'modupdater-export-panel',
					cls: 'main-wrapper',
				}]
			}]
		}]
	});
	modUpdater.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(modUpdater.panel.Home, MODx.Panel);
Ext.reg('modupdater-panel-home', modUpdater.panel.Home);
