simpleUpdater.panel.Home = function (config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'simpleupdater-panel-home',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offsets',
		items: [{
			html: '<h2>' + _('simpleupdater') + '</h2>',
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
					html: _('simpleupdater_intro_msg'),
					cls: 'panel-desc',
				}, {
					xtype: 'simpleupdater-updater-panel',
					cls: 'main-wrapper',
				}]
			}]
		}]
	});
	simpleUpdater.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(simpleUpdater.panel.Home, MODx.Panel);
Ext.reg('simpleupdater-panel-home', simpleUpdater.panel.Home);
