<?php

/**
 * The home manager controller for modUpdater.
 *
 */
class modUpdaterHomeManagerController extends modUpdaterMainController {
	/* @var modUpdater $modUpdater */
	public $modUpdater;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('modupdater');
	}


	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->addCss($this->modUpdater->config['cssUrl'] . 'mgr/main.css');
		$this->addCss($this->modUpdater->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		$this->addJavascript($this->modUpdater->config['jsUrl'] . 'mgr/misc/utils.js');
		$this->addJavascript($this->modUpdater->config['jsUrl'] . 'mgr/widgets/export.panel.js');
		$this->addJavascript($this->modUpdater->config['jsUrl'] . 'mgr/widgets/items.windows.js');
		$this->addJavascript($this->modUpdater->config['jsUrl'] . 'mgr/widgets/import.panel.js');
		$this->addJavascript($this->modUpdater->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->modUpdater->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "modupdater-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->modUpdater->config['templatesPath'] . 'home.tpl';
	}
}