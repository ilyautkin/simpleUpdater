<?php

class simpleUpdaterCheckProcessor extends modProcessor {
    public $languageTopics = array('simpleupdater');

    public function checkPermissions() {
        return $this->modx->hasPermission('file_create');
    }
    
    public function process() {
        $object = array(
            'success' => true,
            'show_button' => false,
            'connector_url' => $this->modx->getOption('assets_url') . 'components/simpleupdater/connector.php'
        );
        $ttl = 6 * 60 * 60;
        $registry = $this->modx->getService('registry', 'registry.modRegistry');
        $registry = $registry->getRegister('user', 'registry.modDbRegister');
        $registry->connect();
        $topic = '/simpleUpdater/';
        $registry->subscribe($topic . 'version');
        $maxVersion = array_shift($registry->read(array('poll_limit' => 1, 'remove_read' => false)));
        if (empty($maxVersion)) {
            $context = stream_context_create(array('http' => array('method' => 'GET', 'header' => 'User-Agent: MODX simpleUpdater')));
            $contents = file_get_contents('https://api.github.com/repos/modxcms/revolution/tags', false, $context);
            $contents = utf8_encode($contents);
            $contents = $this->modx->fromJSON($contents);
            if (empty($contents)) {
                $object['success'] = false;
                return $this->failure('', $object);
            } else {
                foreach ($contents as $key => $content) {
                    $name = substr($content['name'], 1);
                    if (strpos($name, 'pl') === false) {
                        unset($contents[$key]);
                        continue;
                    }
                }
                $contents = array_values($contents);
                $maxVersion = 0;
                foreach ($contents as $version) {
                    if (!$maxVersion || version_compare($maxVersion, $version['name']) < 0) {
                        $maxVersion = $version['name'];
                    }
                }
                $changelog = trim(file_get_contents('https://raw.githubusercontent.com/modxcms/revolution/'.$maxVersion.'/core/docs/changelog.txt'));
                $registry->subscribe($topic);
                $registry->send(
                	$topic,
                	array('version' => $maxVersion, 'changelog' => $changelog),
                	array('ttl' => $ttl)
                );
            }
        }
        $this->modx->getVersionData();
        $currentVersion  = $this->modx->version['version'];
        $currentVersion .= '.'.$this->modx->version['major_version'];
        $currentVersion .= '.'.$this->modx->version['minor_version'];
        $currentVersion = 'v'.$currentVersion.'-pl';
        if (version_compare($currentVersion, $maxVersion, '<')) {
            $registry->subscribe($topic . 'changelog');
            $changelog = array_shift($registry->read(array('poll_limit' => 1, 'remove_read' => false)));
            if (empty($changelog)) {
                $changelog = trim(file_get_contents('https://raw.githubusercontent.com/modxcms/revolution/'.$maxVersion.'/core/docs/changelog.txt'));
            }
            $object['show_button'] = true;
            $object['version'] = $maxVersion;
            $object['changelog'] = $changelog;
        }
        if (!$object['success']) {
            $o = $this->failure('', $object);
        } else {
            $o = $this->success('', $object);
        }
        return $o;
    }

}

return 'simpleUpdaterCheckProcessor';