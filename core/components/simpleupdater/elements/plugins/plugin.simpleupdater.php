<?php
switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        $modx->controller->addLexiconTopic('simpleupdater:default');
        $modx->controller->addCss($modx->getOption('assets_url').'components/simpleupdater/css/mgr/main.css');
        $modx->controller->addJavascript($modx->getOption('assets_url').'components/simpleupdater/js/mgr/widgets/update.button.js');
        $response = $modx->runProcessor('mgr/version/check', array(), array('processors_path' => $modx->getOption('core_path') . 'components/simpleupdater/processors/'));
        $resObj = $response->getObject();
        $_html = "<script>	var simpleUpdateConfig = " . $modx->toJSON($resObj) . ";</script>";
        $modx->controller->addHtml($_html);
        break;
}