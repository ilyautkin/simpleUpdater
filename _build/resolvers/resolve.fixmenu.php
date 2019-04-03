<?php
/**
 * Resolve fix menu
 *
 * @var xPDOObject $object
 * @var array $options
 */

if ($object->xpdo) {
	/* @var modX $modx */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
			break;

		case xPDOTransport::ACTION_UPGRADE:
			if ($menu = $modx->getObject('modMenu', [
			        'text' => 'simpleupdater',
				    'namespace' => 'core'
				])) {
				$data = [
					'namespace' => 'simpleupdater',
					'action' => 'index'
				];
				$menu->fromArray($data);
				$menu->save();
			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;