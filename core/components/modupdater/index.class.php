<?php

/**
 * Class modUpdaterMainController
 */
abstract class modUpdaterMainController extends modExtraManagerController {
	/** @var modUpdater $modUpdater */
	public $modUpdater;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('modupdater_core_path', null, $this->modx->getOption('core_path') . 'components/modupdater/');
		require_once $corePath . 'model/modupdater/modupdater.class.php';

		$this->modUpdater = new modUpdater($this->modx);
		$this->addCss($this->modUpdater->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->modUpdater->config['jsUrl'] . 'mgr/modupdater.js');
		$this->addHtml('
		<script type="text/javascript">
			modUpdater.config = ' . $this->modx->toJSON($this->modUpdater->config) . ';
			modUpdater.config.connector_url = "' . $this->modUpdater->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('modupdater:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends modUpdaterMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}