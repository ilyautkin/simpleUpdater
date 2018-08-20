<?php

/**
 * The home manager controller for simpleUpdater.
 *
 */
class simpleUpdaterHomeManagerController extends simpleUpdaterMainController {
	/* @var simpleUpdater $simpleUpdater */
	public $simpleUpdater;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('simpleupdater');
	}


	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->addJavascript($this->simpleUpdater->config['jsUrl'] . 'mgr/widgets/updater.panel.js');
		$this->addJavascript($this->simpleUpdater->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->simpleUpdater->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "simpleupdater-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->simpleUpdater->config['templatesPath'] . 'home.tpl';
	}
}