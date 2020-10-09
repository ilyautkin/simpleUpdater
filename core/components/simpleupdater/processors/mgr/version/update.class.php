<?php

class simpleUpdaterUpdateProcessor extends modProcessor
{
    public $languageTopics = array('simpleupdater');

    public function checkPermissions()
    {
        return $this->modx->user->isMember('Administrator');
    }

    public function process()
    {
        $corePath = $this->modx->getOption('simpleupdater.core_path', null, $this->modx->getOption('core_path') . 'components/simpleupdater/');
        /** @var simpleUpdater $simpleupdater */
        $simpleupdater = $this->modx->getService('simpleupdater', 'simpleUpdater', $corePath . 'model/simpleupdater/', array(
            'core_path' => $corePath
        ));

        $object = array(
            'success' => false
        );
        $response = $this->modx->runProcessor('mgr/version/check', array(), array(
            'processors_path' => $simpleupdater->getOption('processorsPath')
        ));
        $resObj = $response->getObject();
        if (!$resObj['version']) {
            $this->modx->getVersionData();
            $currentVersion  = $this->modx->version['version'];
            $currentVersion .= '.'.$this->modx->version['major_version'];
            $currentVersion .= '.'.$this->modx->version['minor_version'];
            $currentVersion = 'v'.$currentVersion.'-pl';
            $resObj = array('version' => $currentVersion);
        }
        $version = str_replace('v','',$resObj['version']);
        $link = 'https://modx.com/download/direct?id=modx-'.$version.'-advanced.zip';

        error_reporting(0);
        ini_set('display_errors', 0);
        set_time_limit(0);
        ini_set('max_execution_time',0);
        header('Content-Type: text/html; charset=utf-8');
        
        if(extension_loaded('xdebug')){
            ini_set('xdebug.max_nesting_level', 100000);
        }

        //proxy settings
        $opts = null;
        $proxyHost = $this->modx->getOption('proxy_host',null,'');
        if(!empty($proxyHost)) {
            $opts = [
                'http' => [
                    'proxy' => $proxyHost,
                    'request_fulluri' => true
                ],
                'ssl' => [
                    "verify_peer" => false,
                    "verify_peer_name" => false
                ]
            ];
            $proxyPort = $this->modx->getOption('proxy_port',null,'');
            if (!empty($proxyPort)) {
                $opts['http']['proxy'] .= (":".$proxyPort);
            }
            $proxyUserpwd = $this->modx->getOption('proxy_username',null,'');
            if (!empty($proxyUserpwd)) {
                $proxyAuthType = $this->modx->getOption('proxy_auth_type',null,'BASIC');
                $proxyPassword = $this->modx->getOption('proxy_password',null,'');
                $proxyAuth = base64_encode("$proxyUserpwd".(!empty($proxyPassword) ? (":".$proxyPassword) : ""));
                $opts['http']['header'] = "Proxy-Authorization: $proxyAuthType $proxyAuth";
            }
        }

        //run unzip and install
        ModxInstaller::downloadFile($link, $this->modx->getOption('base_path') . "modx.zip", $opts);
        $zip = new ZipArchive;
        $res = $zip->open($this->modx->getOption('base_path') ."modx.zip");
        $zip->extractTo($this->modx->getOption('base_path').'temp' );
        $zip->close();
        unlink($this->modx->getOption('base_path').'modx.zip');
        
        if ($handle = opendir($this->modx->getOption('base_path').'temp')) {
        	while (false !== ($name = readdir($handle))) if ($name != "." && $name != "..") $dir = $name;
        	closedir($handle);
        }
        $object['success'] = true;
        ModxInstaller::copyFolder($this->modx->getOption('base_path').'temp/'.$dir, $this->modx->getOption('base_path'));
        ModxInstaller::removeFolder($this->modx->getOption('base_path').'temp');
        unlink(basename(__FILE__));
        if (!$object['success']) {
            $o = $this->failure('Error', array());
        } else {
            $o = $this->success('', array());
        }
        return $o;
    }

}

class ModxInstaller
{
    static public function downloadFile($url, $path, $opts)
    {
        $newfname = $path;
        try {
            $file = fopen($url, "rb", false, $opts ? stream_context_create($opts) : null);
            if ($file) {
                $newf = fopen($newfname, "wb");
                if ($newf)
                    while (!feof($file)) {
                        fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                    }
            }
        } catch (Exception $e) {
            return false;
        }
        if ($file) fclose($file);
        if ($newf) fclose($newf);
        return true;
    }

    static public function removeFolder($path)
    {
        $dir = realpath($path);
        if (!is_dir($dir)) return;
        $it = new RecursiveDirectoryIterator($dir);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    static public function copyFolder($src, $dest)
    {
        $path = realpath($src);
        $dest = realpath($dest);
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($objects as $name => $object) {
            $startsAt = substr(dirname($name), strlen($path));
            self::mmkDir($dest . $startsAt);
            if ($object->isDir()) {
                self::mmkDir($dest . substr($name, strlen($path)));
            }

            if (is_writable($dest . $startsAt) and $object->isFile()) {
                copy((string)$name, $dest . $startsAt . DIRECTORY_SEPARATOR . basename($name));
            }
        }
    }

    static public function mmkDir($folder, $perm = 0777)
    {
        if (!is_dir($folder)) {
            mkdir($folder, $perm);
        }
    }
}

return 'simpleUpdaterUpdateProcessor';