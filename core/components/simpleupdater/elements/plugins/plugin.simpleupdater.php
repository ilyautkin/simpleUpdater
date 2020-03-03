<?php
/**
 * simpleUpdater
 *
 * @package simpleupdater
 * @subpackage plugin
 *
 * @event OnManagerPageBeforeRender
 *
 * @var modX $modx
 */

$eventName = $modx->event->name;

$corePath = $modx->getOption('simpleupdater.core_path', null, $modx->getOption('core_path') . 'components/simpleupdater/');
/** @var simpleUpdater $simpleupdater */
$simpleupdater = $modx->getService('simpleupdater', 'simpleUpdater', $corePath . 'model/simpleupdater/', array(
    'core_path' => $corePath
));

switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        if ($modx->user->isMember('Administrator')) {
            $modx->controller->addLexiconTopic('simpleupdater:default');
            $modx->controller->addJavascript($simpleupdater->getOption('assetsUrl') . 'js/mgr/widgets/update.button.js?v=' . $simpleupdater->version);
            $response = $modx->runProcessor('mgr/version/check', array(), array(
                'processors_path' => $simpleupdater->getOption('processorsPath')
            ));
            $html = "<script>var simpleUpdateConfig = " . json_encode($response->getObject(), JSON_PRETTY_PRINT) . ";</script>";
            $modx->controller->addHtml($html);
        }
        break;
}
