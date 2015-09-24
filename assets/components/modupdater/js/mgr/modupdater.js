var modUpdater = function (config) {
	config = config || {};
	modUpdater.superclass.constructor.call(this, config);
};
Ext.extend(modUpdater, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('modupdater', modUpdater);

modUpdater = new modUpdater();