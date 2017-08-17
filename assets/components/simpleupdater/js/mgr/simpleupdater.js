var simpleUpdater = function (config) {
	config = config || {};
	simpleUpdater.superclass.constructor.call(this, config);
};
Ext.extend(simpleUpdater, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('simpleupdater', simpleUpdater);

simpleUpdater = new simpleUpdater();