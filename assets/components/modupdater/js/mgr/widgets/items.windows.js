modUpdater.window.CreateItem = function (config) {
    config = config || {};
    if (!config.id) {
		config.id = 'modupdater-item-window-create';
	}
	Ext.applyIf(config, {
		title: _('modupdater_item_create'),
		width: 550,
		autoHeight: true,
		url: modUpdater.config.connector_url,
		action: 'mgr/link/create',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	modUpdater.window.CreateItem.superclass.constructor.call(this, config);
};
Ext.extend(modUpdater.window.CreateItem, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'textfield',
			fieldLabel: _('modupdater_item_page'),
			name: 'page',
			id: config.id + '-page',
			anchor: '99%',
			allowBlank: false,
		}, /*{
            xtype: 'modx-combo-resource',
            fieldLabel: _('modupdater_item_resource'),
            name: 'resource',
            hiddenName: 'resource',
            id: config.id + '-resource',
			anchor: '99%',
			allowBlank: true,
        }, */{
            xtype: 'textfield',
            fieldLabel: _('modupdater_item_url'),
            name: 'url',
            id: config.id + '-url',
			anchor: '99%',
			allowBlank: false,
        }, /*{
            xtype: 'modx-combo-resource',
            fieldLabel: _('modupdater_item_target'),
            name: 'target',
            hiddenName: 'target',
            id: config.id + '-target',
			anchor: '99%',
			allowBlank: true,
        }, */{
			xtype: 'textfield',
			fieldLabel: _('modupdater_item_anchor'),
			name: 'anchor',
			id: config.id + '-anchor',
			anchor: '99%'
		}, /*{
            xtype: 'textfield',
            fieldLabel: _('modupdater_item_position'),
            name: 'position',
            id: config.id + '-position',
			anchor: '99%',
			allowBlank: true,
        },*/ {
			xtype: 'xcheckbox',
			boxLabel: _('modupdater_item_active'),
			name: 'active',
			id: config.id + '-active',
			checked: true,
		}];
	}

});
Ext.reg('modupdater-item-window-create', modUpdater.window.CreateItem);


modUpdater.window.CreatePosition = function (config) {
    config = config || {};
    if (!config.id) {
		config.id = 'modupdater-position-window-create';
	}
	Ext.applyIf(config, {
		title: _('modupdater_item_create'),
		width: 550,
		autoHeight: true,
		url: modUpdater.config.connector_url,
		action: 'mgr/link/create',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	modUpdater.window.CreatePosition.superclass.constructor.call(this, config);
};
Ext.extend(modUpdater.window.CreatePosition, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'textfield',
			fieldLabel: _('modupdater_item_page'),
			name: 'page',
			id: config.id + '-page',
			anchor: '99%',
			allowBlank: false,
		}, /*{
            xtype: 'modx-combo-resource',
            fieldLabel: _('modupdater_item_resource'),
            name: 'resource',
            hiddenName: 'resource',
            id: config.id + '-resource',
			anchor: '99%',
			allowBlank: true,
        }, */{
            xtype: 'textfield',
            fieldLabel: _('modupdater_item_url'),
            name: 'url',
            id: config.id + '-url',
			anchor: '99%',
			allowBlank: false,
        }, /*{
            xtype: 'modx-combo-resource',
            fieldLabel: _('modupdater_item_target'),
            name: 'target',
            hiddenName: 'target',
            id: config.id + '-target',
			anchor: '99%',
			allowBlank: true,
        }, */{
			xtype: 'textfield',
			fieldLabel: _('modupdater_item_anchor'),
			name: 'anchor',
			id: config.id + '-anchor',
			anchor: '99%'
		}, /*{
            xtype: 'textfield',
            fieldLabel: _('modupdater_item_position'),
            name: 'position',
            id: config.id + '-position',
			anchor: '99%',
			allowBlank: true,
        },*/ {
			xtype: 'xcheckbox',
			boxLabel: _('modupdater_item_active'),
			name: 'active',
			id: config.id + '-active',
			checked: true,
		}];
	}

});
Ext.reg('modupdater-position-window-create', modUpdater.window.CreatePosition);


modUpdater.window.UpdateItem = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'modupdater-item-window-update';
	}
	Ext.applyIf(config, {
		title: _('modupdater_item_update'),
		width: 550,
		autoHeight: true,
		url: modUpdater.config.connector_url,
		action: 'mgr/link/update',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	modUpdater.window.UpdateItem.superclass.constructor.call(this, config);
};
Ext.extend(modUpdater.window.UpdateItem, MODx.Window, {

	getFields: function (config) {
        //console.log(config.record.object);
        var windowFields = [{
    		xtype: 'hidden',
			name: 'id',
			id: config.id + '-id',
		}];
        if (config.record.object.resource == 0) {
            windowFields.push({
                xtype: 'textfield',
        		fieldLabel: _('modupdater_item_page'),
    			name: 'page',
    			id: config.id + '-page',
    			anchor: '99%',
    			allowBlank: false,
    		});
        } else {
            windowFields.push({
                xtype: 'modx-combo-resource',
                fieldLabel: _('modupdater_item_resource'),
                name: 'resource',
                hiddenName: 'resource',
                id: config.id + '-resource',
        		anchor: '99%',
    			allowBlank: false,
            });
        }
        if (config.record.object.target == 0) {
            windowFields.push({
                xtype: 'textfield',
                fieldLabel: _('modupdater_item_url'),
                name: 'url',
                id: config.id + '-url',
        		anchor: '99%',
    			allowBlank: false,
            });
        } else {
            windowFields.push({
                xtype: 'modx-combo-resource',
                fieldLabel: _('modupdater_item_target'),
                name: 'target',
                hiddenName: 'target',
                id: config.id + '-target',
        		anchor: '99%',
    			allowBlank: false,
            });
        }
        windowFields.push({
    		xtype: 'textfield',
			fieldLabel: _('modupdater_item_anchor'),
			name: 'anchor',
			id: config.id + '-anchor',
			anchor: '99%'
		}, {
            xtype: 'textfield',
            fieldLabel: _('modupdater_item_position'),
            name: 'position',
            id: config.id + '-position',
			anchor: '99%',
			allowBlank: false,
        }, {
			xtype: 'xcheckbox',
			boxLabel: _('modupdater_item_active'),
			name: 'active',
			id: config.id + '-active',
			checked: true,
		});
		return windowFields;
	}

});
Ext.reg('modupdater-item-window-update', modUpdater.window.UpdateItem);


modUpdater.window.UpdatePosition = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'modupdater-position-window-update';
	}
	Ext.applyIf(config, {
		title: _('modupdater_item_update'),
		width: 550,
		autoHeight: true,
		url: modUpdater.config.connector_url,
		action: 'mgr/link/update',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	modUpdater.window.UpdatePosition.superclass.constructor.call(this, config);
};
Ext.extend(modUpdater.window.UpdatePosition, MODx.Window, {

	getFields: function (config) {
        //console.log(config.record.object);
        var windowFields = [{
    		xtype: 'hidden',
			name: 'id',
			id: config.id + '-id',
		}];
        if (config.record.object.resource == 0) {
            windowFields.push({
                xtype: 'textfield',
        		fieldLabel: _('modupdater_item_page'),
    			name: 'page',
    			id: config.id + '-page',
    			anchor: '99%',
    			allowBlank: false,
    		});
        } else {
            windowFields.push({
                xtype: 'modx-combo-resource',
                fieldLabel: _('modupdater_item_resource'),
                name: 'resource',
                hiddenName: 'resource',
                id: config.id + '-resource',
        		anchor: '99%',
    			allowBlank: false,
            });
        }
        if (config.record.object.target == 0) {
            windowFields.push({
                xtype: 'textfield',
                fieldLabel: _('modupdater_item_url'),
                name: 'url',
                id: config.id + '-url',
        		anchor: '99%',
    			allowBlank: false,
            });
        } else {
            windowFields.push({
                xtype: 'modx-combo-resource',
                fieldLabel: _('modupdater_item_target'),
                name: 'target',
                hiddenName: 'target',
                id: config.id + '-target',
        		anchor: '99%',
    			allowBlank: false,
            });
        }
        windowFields.push({
    		xtype: 'textfield',
			fieldLabel: _('modupdater_item_anchor'),
			name: 'anchor',
			id: config.id + '-anchor',
			anchor: '99%'
		}, {
            xtype: 'textfield',
            fieldLabel: _('modupdater_item_position'),
            name: 'position',
            id: config.id + '-position',
			anchor: '99%',
			allowBlank: false,
        }, {
			xtype: 'xcheckbox',
			boxLabel: _('modupdater_item_active'),
			name: 'active',
			id: config.id + '-active',
			checked: true,
		});
		return windowFields;
	}

});
Ext.reg('modupdater-position-window-update', modUpdater.window.UpdatePosition);

MODx.combo.Resource = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        valueField: 'id'
        ,displayField: 'pagetitle'
        ,fields: ['id','pagetitle']
        ,url: MODx.config.connectors_url+'resource/index.php'
        ,baseParams: {
                action: 'resource/getlist'
                ,parent:0
                ,limit:0
        }
        ,tpl: new Ext.XTemplate('<tpl for=".">'
            ,'<div class="x-combo-list-item">'
            ,'<h4 class="modx-combo-title">{pagetitle} ({id})</h4>'
            ,'</div></tpl>')
    });
    MODx.combo.Resource.superclass.constructor.call(this,config);
};
Ext.extend(MODx.combo.Resource,MODx.combo.ComboBox);
Ext.reg('modx-combo-resource',MODx.combo.Resource);