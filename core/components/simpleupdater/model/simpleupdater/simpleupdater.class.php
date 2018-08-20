<?php

/**
 * The base class for simpleUpdater.
 */
class simpleUpdater {
	/* @var modX $modx */
	public $modx;


	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('simpleupdater_core_path', $config, $this->modx->getOption('core_path') . 'components/simpleupdater/');
		$assetsUrl = $this->modx->getOption('simpleupdater_assets_url', $config, $this->modx->getOption('assets_url') . 'components/simpleupdater/');
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

		$this->modx->addPackage('simpleupdater', $this->config['modelPath']);
		$this->modx->lexicon->load('simpleupdater:default');
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
		$properties = $resource->getProperties('simpleupdater');

		$controller->addLexiconTopic('simpleupdater:default');
		$controller->addJavascript($jsUrl . 'simpleupdater.js');
    	$controller->addHtml('
		<script type="text/javascript">
			MODx.modx23 = ' . $modx23 . ';
			simpleUpdater.config = ' . $this->modx->toJSON($this->config) . ';
			simpleUpdater.config.resID = ' . $resource->id . ';
            simpleUpdater.config.connector_url = "' . $this->config['connectorUrl'] . '";
		</script>', true);
	}

}