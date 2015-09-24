<?php

/**
 * The base class for modUpdater.
 */
class modUpdater {
	/* @var modX $modx */
	public $modx;


	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('modupdater_core_path', $config, $this->modx->getOption('core_path') . 'components/modupdater/');
		$assetsUrl = $this->modx->getOption('modupdater_assets_url', $config, $this->modx->getOption('assets_url') . 'components/modupdater/');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'imagesUrl' => $assetsUrl . 'images/',
			'connectorUrl' => $connectorUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/'
		), $config);

		$this->modx->addPackage('modupdater', $this->config['modelPath']);
		$this->modx->lexicon->load('modupdater:default');
	}
    
    public function findResource($url) {
        $urlArr = parse_url($url);
        if (substr($urlArr['path'],0,1) == '/') {
            $urlArr['path'] = substr($urlArr['path'],1);
        }
        $site_url = $urlArr['scheme'].'://'.$urlArr['host'].'/';
        if ($ctxObject = $this->modx->getObject('modContextSetting', array('key' => 'site_url', 'value' => $site_url))) {
            $ctx = $ctxObject->get('context_key');
            if ($url == $site_url) {
                if ($site_start = $this->modx->getObject('modContextSetting', array('context_key' => $ctx, 'key' => 'site_start'))) {
                    $resourceId = $site_start->get('value');
                }
            }
        } else {
            $ctx = 'web';
            if ($url == $site_url) {
                if ($site_url == $this->modx->getOption('site_url')) {
                    $resourceId = $this->modx->getOption('site_start');
                }
            }
        }
        if (!$resourceId) {
            $resourceId = $this->modx->findResource($urlArr['path'], $ctx);
        }
        if ($resourceId === false) {
            $resourceId = 0;
        }
        return $resourceId;
    }

    /**
	 * Compares MODX version
	 *
	 * @param string $version
	 * @param string $dir
	 *
	 * @return bool
	 */
	public function systemVersion($version = '2.3.0', $dir = '>=') {
		$this->modx->getVersionData();
		return !empty($this->modx->version) && version_compare($this->modx->version['full_version'], $version, $dir);
	}


    /**
	 * @param modManagerController $controller
	 * @param modResource $resource
	 */
	public function loadManagerFiles(modManagerController $controller, modResource $resource) {
		$modx23 = (int)$this->systemVersion();
		$cssUrl = $this->config['cssUrl'] . 'mgr/';
		$jsUrl = $this->config['jsUrl'] . 'mgr/';
		$properties = $resource->getProperties('modupdater');

		$controller->addLexiconTopic('modupdater:default');
		$controller->addJavascript($jsUrl . 'modupdater.js');
		$controller->addLastJavascript($jsUrl . 'misc/utils.js');
		$controller->addCss($cssUrl . 'main.css');
		if (!$modx23) {
			$controller->addCss($cssUrl . 'font-awesome.min.css');
		}
    	$controller->addHtml('
		<script type="text/javascript">
			MODx.modx23 = ' . $modx23 . ';
			modUpdater.config = ' . $this->modx->toJSON($this->config) . ';
			modUpdater.config.resID = ' . $resource->id . ';
            modUpdater.config.connector_url = "' . $this->config['connectorUrl'] . '";
		</script>', true);
		$controller->addLastJavascript($jsUrl . 'widgets/items.windows.js');
	}

}