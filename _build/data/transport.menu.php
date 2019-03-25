<?php

$menus = array();

$tmp = array(
	'simpleupdater' => array(
		'description' => 'simpleupdater_menu_desc',
		'action' => 'index',
	),
);

$i = 0;
foreach ($tmp as $k => $v) {

	/* @var modMenu $menu */
	$menu = $modx->newObject('modMenu');
	$menu->fromArray(array_merge(
		array(
			'text' => $k,
			'parent' => 'components',
			'icon' => 'images/icons/plugin.gif',
			'menuindex' => $i,
			'params' => '',
			'handler' => '',
			'namespace' => $k,
		), $v
	), '', true, true);

	$menus[] = $menu;
	$i++;
}

unset($menu, $i);
return $menus;