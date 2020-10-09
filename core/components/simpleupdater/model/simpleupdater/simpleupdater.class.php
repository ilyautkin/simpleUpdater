<?php
/**
 * simpleUpdater classfile
 *
 * Copyright 2015-2020 by Ilya Utkin <ilyautkin@mail.ru>
 *
 * @package simpleupdater
 * @subpackage classfile
 */

/**
 * class simpleUpdater
 */
class simpleUpdater
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'simpleupdater';

    /**
     * The version
     * @var string $version
     */
    public $version = '2.2.0';

    /**
     * The class config
     * @var array $config
     */
    public $config = array();

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $config, $this->namespace);

        $corePath = $this->modx->getOption('simpleupdater.core_path', $config, $this->modx->getOption('core_path') . 'components/simpleupdater/');
        $assetsUrl = $this->modx->getOption('simpleupdater.assets_url', $config, $this->modx->getOption('assets_url') . 'components/simpleupdater/');

        $this->config = array_merge(array(
            'namespace' => $this->namespace,
            'version' => $this->version,
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/'
        ), $config);

        $this->modx->lexicon->load($this->namespace . ':default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    /**
     * Request the content of an url with a possible gitHub token
     *
     * @param $url
     * @param bool $addToken
     * @return bool|false|string
     */
    public function requestUrl($url, $addToken = false)
    {
        $httpHandler = $this->getOption('http_handler');
        $githubUser = $this->getOption('github_user');
        $githubToken = $this->getOption('github_token');
        $url = ($githubUser && $githubToken && $addToken) ? str_replace('https://', 'https://' . $githubUser . ':' . $githubToken, $url) : $url;
        $proxyHost = $this->modx->getOption('proxy_host',null,'');
        $proxyPort = $this->modx->getOption('proxy_port',null,'');
        $proxyUserpwd = $this->modx->getOption('proxy_username',null,'');
        $proxyPassword = $this->modx->getOption('proxy_password',null,'');

        switch ($httpHandler) {
            case 'curl':
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, 'MODX simpleUpdater');
                curl_setopt($ch, CURLOPT_TIMEOUT,10);

                if (!empty($proxyHost)) {
                    curl_setopt($ch, CURLOPT_PROXY, $proxyHost);
                    if (!empty($proxyPort)) {
                        curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
                    }
                    if (!empty($proxyUserpwd)) {
                        $proxyAuthType = $this->modx->getOption('proxy_auth_type',null,'BASIC');
                        $proxyAuthType = $proxyAuthType == 'NTLM' ? CURLAUTH_NTLM : CURLAUTH_BASIC;
                        curl_setopt($ch, CURLOPT_PROXYAUTH, $proxyAuthType);

                        if (!empty($proxyPassword)) $proxyUserpwd .= ':'.$proxyPassword;
                        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUserpwd);
                    }
                }

                $contents = curl_exec($ch);
                curl_close($ch);
                $contents = utf8_encode($contents);

                break;
            case 'file_get_contents':
            default:
                $opts = [
                    'http' => array(
                        'method' => 'GET',
                        'header' => 'User-Agent: MODX simpleUpdater'
                    )
                ];
                if(!empty($proxyHost)) {
                    $opts['http']['proxy'] = $proxyHost;
                    $opts['http']['request_fulluri'] = true;
                    $opts['ssl'] = [
                        "verify_peer" => false,
                        "verify_peer_name" => false
                    ];
                    if (!empty($proxyPort)) {
                        $opts['http']['proxy'] .= (":".$proxyPort);
                    }
                    if (!empty($proxyUserpwd)) {
                        $proxyAuthType = $this->modx->getOption('proxy_auth_type',null,'BASIC');
                        $proxyAuth = base64_encode("$proxyUserpwd".(!empty($proxyPassword) ? (":".$proxyPassword) : ""));
                        $opts['http']['header'] .= "\r\nProxy-Authorization: $proxyAuthType $proxyAuth";
                    }
                }
                $context = stream_context_create($opts);
                $contents = file_get_contents($url, false, $context);
                $contents = utf8_encode($contents);

                break;
        }
        return $contents;
    }
}