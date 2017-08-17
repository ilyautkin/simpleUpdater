simpleUpdater.panel.Export = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'simpleupdater-export-panel';
    }
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        url: simpleUpdater.config.connector_url,
        config: config,
        layout: 'anchor',
        hideMode: 'offsets',
		fileUpload: true,
		baseParams: {
			action: 'mgr/prices/export'
		},

		items: [{
            xtype: 'button',
            text: _('simpleupdater_update_start'),
            fieldLabel: _('simpleupdater_update_start'),
            name: 'start-export',
            id: config.id + '-start-export',
            cls: 'primary-button',
			listeners: {
				click: {fn: this._startexport, scope: this}
			}
        }, {
            xtype: 'modx-panel',
            id: config.id + '-export-log',
            anchor: '100%',
            autoHeight: true,
            cls: 'panel-desc',
            style: {display: 'none', 'max-height': '250px', overflow: 'auto'}
        }]
	});
	simpleUpdater.panel.Export.superclass.constructor.call(this, config);
};
Ext.extend(simpleUpdater.panel.Export, MODx.FormPanel, {
    _selectCSV: function() {
        document.getElementById(this.config.id + '-csv-file-file').click();
    },
    
    _fileInputAfterRender: function() {
        document.getElementById(this.config.id + '-csv-file-file').addEventListener('change', this._showFileName, false);
        document.getElementById(this.config.id + '-csv-file-file').style.display = "none";
        document.getElementById(this.config.id + '-csv-file-file').nextSibling.style.display = "none";
    },
    
    _showFileName: function(e) {
        document.getElementById(e.target.id + 'name-holder').innerHTML = this.files[0].name;
        Ext.getCmp('simpleupdater-export-panel-export-log').body.dom.innerHTML = "";
        document.getElementById('simpleupdater-export-panel-export-log').style.display = "none";
        /*document.getElementById(e.target.config.id + '-csv-file-btn').classList.add('x-item-disabled');
        e.target.setAttribute("disabled", "disabled");*/
    },
    
    _startexport: function() {
        Ext.getCmp(this.config.id).form.submit({
            url: simpleUpdater.config.connector_url,
            success: function(form, response){
                //console.log(form);
                var panel = Ext.getCmp(form.config.id);
                panel._processexport(response.result);
            },
            failure: function(form, response){
                for (i=0;i<response.result.errors.length;i++) {
                    //console.log(response.result.errors[i]);
                    if (response.result.errors[i].id == 'csv-file-btn') {
                        document.getElementById(form.config.id + '-csv-file-filename-holder').innerHTML =
                            '<span class="red">' + response.result.errors[i].msg + '</span>';
                        document.getElementById(form.config.id + '-csv-file-file-btn').classList.remove('x-item-disabled');
                        document.getElementById(form.config.id + '-csv-file-file').removeAttribute("disabled");
                    }
                }
                //Ext.MessageBox.alert('Ошибка авторизации. ',response.result.message);
            }
        });
    },
    
    _logProcess: function(response) {
        var lineSeparator = '<br />';
        var logcontainer = document.getElementById(this.config.id + '-export-log');
        var currentlog = Ext.getCmp(this.config.id + '-export-log').body.dom.innerHTML;
        var exportlog = currentlog ? currentlog.split(lineSeparator) : [];
        if (logcontainer.style.display == "none") {
            logcontainer.style.display = "block";
        }
        exportlog = exportlog.concat(response.object.log);
	    Ext.getCmp(this.config.id + '-export-log').update(exportlog.join(lineSeparator));
        logcontainer.scrollTop = logcontainer.scrollHeight;
    },
    
    _processexport: function(response) {
        this._logProcess(response);
        if (!response.object.complete) {
            MODx.Ajax.request({
            	url: simpleUpdater.config.connector_url
            	,params: {
            		action: 'mgr/prices/export',
            		parsed: true,
            		step:   response.object.step || 0,
					filename: response.object.filename || '',
					exported: response.object.exported || ''
            	}
            	,listeners: {
            		success: {fn: function(response) {
						var indicator = document.getElementById('creating-xls');
						if (indicator) {
							indicator.setAttribute('class', '');
						}
            			this._processexport(response);
            		}, scope: this}
            	}
            });
        } else {
            if (response.object.filepath) {
			    document.location.href = response.object.filepath;
            }
            //this._logProcess(response);
		}
    }
    
});
Ext.reg('simpleupdater-export-panel', simpleUpdater.panel.Export);