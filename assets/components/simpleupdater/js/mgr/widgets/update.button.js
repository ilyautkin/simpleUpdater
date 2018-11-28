function updateMODX() {
	if (!window.simpleUpdaterWindow) {
		simpleUpdaterWindow = new MODx.Window({
			id: "simpleupdater-window",
			height: 500,
			width: 850,
			cloaseAction: 'hide',
			title: _("simpleupdater") ? _("simpleupdater") : 'Update MODX',
			stateful: false,
			buttonAlign: "right",
			items: [{
			    html: '<br><h3>MODX Revolution ' + simpleUpdateConfig.version + '</h3>'
			}, {
				xtype: "textarea",
				name: "changelog",
				fieldLabel: "Changelog",
				id: "simpleupdater-changelog-content",
				value: simpleUpdateConfig.changelog ? simpleUpdateConfig.changelog : '',
				readOnly: true,
				height: "85%",
				width: "99%"
			}, {
			    html: '<div class="loading-indicator" style="height: 410px; background-position: center center;"></div>',
			    style: {
			        position: 'absolute',
			        top: 0,
			        bottom: '55px',
			        left: 0,
			        right: 0,
			        display: 'none'
			    },
			    id: 'simpleupdater-update-loading'
			}],
			buttons: [{
				text: _("simpleupdater_update_start") ? _("simpleupdater_update_start") : 'Update',
				id: "simpleupdater-update-start-btn",
				cls: 'primary-button',
				handler: function (w) {
					simpleUpdaterWindow._startupdate();
				},
				scope: this
			}, {
				text: _("cancel") ? _("cancel") : 'Cancel',
				handler: function (w) {
					simpleUpdaterWindow.hide();
				},
				scope: this
			}],
			_startupdate: function(){
			    Ext.get('simpleupdater-update-loading').show();
                MODx.Ajax.request({
                	url: simpleUpdateConfig.connector_url
                	,params: {
                		action: 'mgr/version/update',
                	}
                	,listeners: {
                		success: {fn: function(response) {
                		    Ext.get('simpleupdater-update-loading').hide();
    						document.location.href = '/setup/';
                		}, scope: this},
                		failure: {fn: function(response) {
                		    Ext.get('simpleupdater-update-loading').hide();
    						Ext.Msg.alert(_('error'), response.object.message);
                		}, scope: this}
                	}
                });
			}
		});
	}
	simpleUpdaterWindow.show(Ext.EventObject.target);
}

Ext.onReady(function() {
    if (simpleUpdateConfig.show_button) {
    	var usermenuUl = document.getElementById("modx-user-menu"),
    		firstLi = usermenuUl.firstChild,
    		simpleUpdaterLi = document.createElement("LI");
    
    	simpleUpdaterLi.innerHTML = "<span id=\"simpleupdater-link\" class=\"x-btn x-btn-small primary-button\" onclick=\"updateMODX()\" style=\"margin: 10px;\">"+_('simpleupdater_update')+"</span>";
    	usermenuUl.insertBefore(simpleUpdaterLi, firstLi);
    }
});
