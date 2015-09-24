<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var modUpdater $modUpdater */
$modUpdater = $modx->getService('modupdater', 'modUpdater', $modx->getOption('modupdater_core_path', null, $modx->getOption('core_path') . 'components/modupdater/') . 'model/modupdater/');
$modx->lexicon->load('modupdater:default');

// handle request
$corePath = $modx->getOption('modupdater_core_path', null, $modx->getOption('core_path') . 'components/modupdater/');
$path = $modx->getOption('processorsPath', $modUpdater->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));