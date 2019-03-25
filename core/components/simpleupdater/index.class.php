<?php

/**
 * Class simpleUpdaterIndexManagerController
 */
class simpleUpdaterIndexManagerController extends modExtraManagerController {
	/** @var simpleUpdater $simpleUpdater */
	public $simpleUpdater;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('simpleupdater_core_path', null, $this->modx->getOption('core_path') . 'components/simpleupdater/');
		require_once $corePath . 'model/simpleupdater/simpleupdater.class.php';

		$this->simpleUpdater = new simpleUpdater($this->modx);
		$this->addJavascript($this->simpleUpdater->config['jsUrl'] . 'mgr/simpleupdater.js');
		$this->addHtml('
		<script type="text/javascript">
			simpleUpdater.config = ' . $this->modx->toJSON($this->simpleUpdater->config) . ';
			simpleUpdater.config.connector_url = "' . $this->simpleUpdater->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('simpleupdater:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
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
