<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var simpleUpdater $simpleUpdater */
$simpleUpdater = $modx->getService('simpleupdater', 'simpleUpdater', $modx->getOption('simpleupdater_core_path', null, $modx->getOption('core_path') . 'components/simpleupdater/') . 'model/simpleupdater/');
$modx->lexicon->load('simpleupdater:default');

// handle request
$corePath = $modx->getOption('simpleupdater_core_path', null, $modx->getOption('core_path') . 'components/simpleupdater/');
$path = $modx->getOption('processorsPath', $simpleUpdater->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));