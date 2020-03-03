<?php
/**
 * The home manager controller for simpleUpdater.
 *
 * @package simpleupdater
 * @subpackage controller
 */

/**
 * Class simpleUpdaterHomeManagerController
 */
class simpleUpdaterHomeManagerController extends modExtraManagerController
{
    /* @var simpleUpdater $simpleUpdater */
    public $simpleUpdater;

    public function initialize()
    {
        $corePath = $this->modx->getOption('simpleupdater.core_path', null, $this->modx->getOption('core_path') . 'components/simpleupdater/');
        $this->simpleUpdater = $this->modx->getService('simpleupdater', 'simpleUpdater', $corePath . 'model/simpleupdater/', array(
            'core_path' => $corePath
        ));

        parent::initialize();
    }

    public function loadCustomCssJs()
    {
        if ($this->modx->user->isMember('Administrator')) {
            $this->addJavascript($this->simpleUpdater->getOption('jsUrl') . 'mgr/simpleupdater.js?v=' . $this->simpleUpdater->version);
            $this->addJavascript($this->simpleUpdater->getOption('jsUrl') . 'mgr/widgets/updater.panel.js');
            $this->addJavascript($this->simpleUpdater->getOption('jsUrl') . 'mgr/widgets/home.panel.js');
            $this->addJavascript($this->simpleUpdater->getOption('jsUrl') . 'mgr/sections/home.js');
            $this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			simpleUpdater.config = ' . json_encode($this->simpleUpdater->config, JSON_PRETTY_PRINT) . ';
	        MODx.load({xtype: "simpleupdater-page-home"});
		});
		</script>');
        }
    }

    public function getLanguageTopics()
    {
        return array('simpleupdater:default');
    }

    public function process(array $scriptProperties = array())
    {
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('simpleupdater');
    }

    public function getTemplateFile()
    {
        return $this->simpleUpdater->getOption('templatesPath') . 'home.tpl';
    }
}
