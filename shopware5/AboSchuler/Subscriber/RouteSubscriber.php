<?php

namespace AboSchuler\Subscriber;

use Enlight_Template_Manager;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Plugin\ConfigReader;
use Shopware\Components\Theme\LessDefinition;
use Doctrine\Common\Collections\ArrayCollection;

class RouteSubscriber implements SubscriberInterface
{
    private $pluginDirectory;
    private $templateManager;

    public function __construct($pluginName, $pluginDirectory, \Enlight_Template_Manager $templateManager, ConfigReader $configReader)
    {
        $this->pluginName       = $pluginName;
        $this->pluginDirectory  = $pluginDirectory;
        $this->templateManager  = $templateManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail'      => 'onPostDispatch',
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_AboSchuler'  => 'registerController',
            'Theme_Compiler_Collect_Plugin_Less'                                => 'addLessFiles',
            'Theme_Compiler_Collect_Plugin_Javascript'                          => 'addJSFiles',
        ];
    }

    public function registerController(\Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controllers/Frontend/AboSchuler.php';
    }

    public function addLessFiles(\Enlight_Event_EventArgs $args)
    {
        $less = new LessDefinition(
            array(),
            array(__DIR__ . '/../Resources/frontend/less/all.less'),
            __DIR__
        );
        
        return new ArrayCollection(array($less));
    }

    public function addJsFiles(\Enlight_Event_EventArgs $args)
    {
        $jsFiles = array(
            __DIR__ . '/../Resources/frontend/js/abo_schuler.js'
        );

        return new ArrayCollection($jsFiles);
    }

    public function onPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        $controller = $args->get('subject');
        $view = $controller->View();

        $view->addTemplateDir($this->pluginDirectory . '/Resources/views');
    }
}