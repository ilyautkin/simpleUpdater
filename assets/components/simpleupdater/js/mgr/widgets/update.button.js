function updateMODX() {
    if (!window.simpleUpdaterWindow) {
        simpleUpdaterWindow = new MODx.Window({
            id: 'simpleupdater-window',
            height: 500,
            width: 850,
            cloaseAction: 'hide',
            title: _('simpleupdater'),
            stateful: false,
            buttonAlign: 'right',
            layout: 'anchor',
            items: [{
                html: '<br><h3>MODX Revolution ' + simpleUpdateConfig.version + '</h3>'
            }, {
                xtype: 'textarea',
                name: 'changelog',
                fieldLabel: 'Changelog',
                id: 'simpleupdater-changelog-content',
                value: simpleUpdateConfig.changelog ? simpleUpdateConfig.changelog : '',
                readOnly: true,
                height: 355,
                anchor: '100%',
                hidden: false
            }, {
                html: '<div class="loading-indicator" style="height: 355px; background-position: center center;"></div>',
                id: 'simpleupdater-update-loading',
                height: 355,
                anchor: '100%',
                hidden: true
            }],
            buttons: [{
                text: _('simpleupdater_update_start'),
                id: 'simpleupdater-update-start-btn',
                cls: 'primary-button',
                handler: function () {
                    simpleUpdaterWindow._startupdate();
                },
                scope: this
            }, {
                text: _('cancel'),
                handler: function () {
                    simpleUpdaterWindow.hide();
                },
                scope: this
            }],
            _startupdate: function () {
                Ext.get('simpleupdater-update-loading').show();
                MODx.Ajax.request({
                    url: simpleUpdateConfig.connector_url,
                    params: {
                        action: 'mgr/version/update',
                    },
                    listeners: {
                        success: {
                            fn: function () {
                                Ext.get('simpleupdater-update-loading').hide();
                                document.location.href = '/setup/';
                            }, scope: this
                        },
                        failure: {
                            fn: function (response) {
                                Ext.get('simpleupdater-update-loading').hide();
                                Ext.Msg.alert(_('error'), response.object.message);
                            }, scope: this
                        }
                    }
                });
            }
        });
    }
    simpleUpdaterWindow.show(Ext.EventObject.target);
}

Ext.onReady(function () {
    if (simpleUpdateConfig.show_button) {
        var usermenuUl = document.getElementById('modx-user-menu'),
            firstLi = usermenuUl.firstChild,
            simpleUpdaterLi = document.createElement('LI');

        simpleUpdaterLi.innerHTML = '<span id="simpleupdater-link" class="x-btn x-btn-small primary-button" onclick="updateMODX()" style="margin: 10px;">' + _('simpleupdater_update') + '</span>';
        usermenuUl.insertBefore(simpleUpdaterLi, firstLi);
    }
});
