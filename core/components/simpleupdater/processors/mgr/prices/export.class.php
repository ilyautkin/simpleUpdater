<?php

class rldExportProcessor extends modProcessor {
    public $languageTopics = array('simpleupdater');

    public function checkPermissions() {
        return $this->modx->hasPermission('file_create');
    }
    
    public function process() {
        $object = array('log' => array());
        
        $this->modx->getVersionData();
        $currentVersion  = $this->modx->version['version'];
        $currentVersion .= '.'.$this->modx->version['major_version'];
        $currentVersion .= '.'.$this->modx->version['minor_version'];
        
        $lastVersion = file_get_contents('https://ilyaut.ru/download-modx/?api=getmodxlastversion');
        
        if ($currentVersion == $lastVersion) {
            $object['log'][] = 'Уже установлена последняя доступная версия MODX';
            $object['complete'] = true;
        } else {
            $installScript = MODX_BASE_PATH.'install.php';
            file_put_contents($installScript, file_get_contents('https://raw.githubusercontent.com/ilyautkin/installer/master/install.php'));
            $object['filepath'] = $this->modx->getOption('site_url').'install.php';
            $object['log'][] = 'Скрипт загружен. Скачиваем и распаковываем дистрибутив.';
            $object['complete'] = true;
        }
        
        if (!$object['complete']) {
            $o = $this->failure('', $object);
        } else {
            $o = $this->success('', $object);
        }
        return $o;
    }

}

return 'rldExportProcessor';