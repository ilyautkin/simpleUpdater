<?php
/**
 * simpleUpdater connector
 *
 * @package cursus
 * @subpackage connector
 *
 * @var modX $modx
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('simpleupdater.core_path', null, $modx->getOption('core_path') . 'components/simpleupdater/');
/** @var simpleUpdater $simpleUpdater */
$simpleUpdater = $modx->getService('simpleupdater', 'simpleUpdater', $corePath . 'model/simpleupdater/', array(
    'core_path' => $corePath
));

// Handle request
$modx->request->handleRequest(array(
    'processors_path' => $simpleUpdater->getOption('processorsPath'),
    'location' => ''
));
