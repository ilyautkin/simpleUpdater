<?php

$settings = array();

$tmp = array(
    'http_handler' => array(
        'xtype' => 'textfield',
        'value' => 'file_get_contents',
        'area' => 'system',
    ),
    'github_user' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'system',
    ),
    'github_token' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'system',
    )
);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => 'simpleupdater.' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
