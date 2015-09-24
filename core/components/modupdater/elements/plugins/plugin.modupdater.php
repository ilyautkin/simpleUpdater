<?php
if (in_array($modx->event->name, $resourceEvents)) {
    foreach($scriptProperties as & $object){
        if(
            is_object($object)
            AND $object instanceof modResource
            AND $original = $modx->getObject('modResource', $object->id)
        ){
            $resource = $object;
            break;
        }
    }
}
switch ($modx->event->name) {
    case 'OnWebPagePrerender':

        /** @var array $scriptProperties */
        /** @var modUpdater $modUpdater */
        if (!$modUpdater = $modx->getService('modUpdater', 'modUpdater', $modx->getOption('modupdater_core_path', null, $modx->getOption('core_path') . 'components/modupdater/') . 'model/modupdater/', $scriptProperties)) {
            return 'Could not load modUpdater class!';
        }
        if ($links = $modx->getCollection('rldLink', array('resource' => $modx->resource->get('id')))) {
            $content = explode('</p>', $modx->resource->get('content'));
            $positions = array();
            foreach ($links as $link) {
                $positions[$link->get('position')][] = $link;
            }
            $templates = $modx->resource->getProperty('positions', 'modupdater');
            if (!$templates) {
                $templates = array();
            }
            foreach ($positions as $pos => $position) {
                $block = array();
                foreach($position as $link) {
                    $href = $link->get('target') ? $modx->makeURL($link->get('target')) : $link->get('url');
                    if (!$templates[$pos]) {
                        $templates[$pos] = rand(1, 3);
                    }
                    switch ($templates[$pos]) {
                        case 3:
                        case 2:
                            $block[] = '<li><a href="'.$href.'">'.$link->get('anchor').'</a></li>';
                            break;
                        default:
                            $block[] = '<a href="'.$href.'">'.$link->get('anchor').'</a>';
                            break;
                    }
                }
                switch ($templates[$pos]) {
                    case 3:
                        $row = '<ul>'.implode(PHP_EOL, $block).'</ul>';
                        break;
                    case 2:
                        $row = '<ol>'.implode(PHP_EOL, $block).'</ol>';
                        break;
                    default:
                        $row = '<p>'.implode(', ', $block).'</p>';
                        break;
                }
                if (isset($content[$pos])) {
                    $content[$pos] = $row . $content[$pos];
                } else {
                    $content[] = $row;
                }
            }
            $modx->resource->setProperty('positions', $templates, 'modupdater');
            $modx->resource->save();
            
            $chunk = $modx->newObject('modChunk');
            $chunk->set('content', $modx->resource->get('content'));
            $chunk->set('name', 'chunk');
            $replace = $chunk->process();
            
            $chunk2 = $modx->newObject('modChunk');
            $chunk2->set('name', 'chunk2');
            $chunk2->set('content', implode('</p>', $content));
            $replace2 = $chunk2->process();
            
            $modx->resource->_output = str_replace($replace, $replace2, $modx->resource->_output);
        }
        break;
    case 'OnDocFormRender':
		/** @var modResource $resource */
        /** @var modUpdater $modUpdater */
		if ($mode != 'new' && $modUpdater = $modx->getService('modUpdater', 'modUpdater', $modx->getOption('modupdater_core_path', null, $modx->getOption('core_path') . 'components/modupdater/') . 'model/modupdater/')) {
            $modUpdater->loadManagerFiles($modx->controller, $resource);
        }
		break;
    default:
        break;
}
return '';