<?php

namespace ProductNumberCounting\Subscriber;

use Enlight_Template_Manager;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Plugin\ConfigReader;

class RouteSubscriber implements SubscriberInterface
{
    private $pluginDirectory;
    private $templateManager;
    private $config;

    public function __construct($pluginName, $pluginDirectory, \Enlight_Template_Manager $templateManager, ConfigReader $configReader)
    {
        $this->pluginName = $pluginName;
        $this->pluginDirectory = $pluginDirectory;
        $this->templateManager = $templateManager;
        $this->config = $configReader->getByPluginName($pluginName);
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch' => 'onPreDispatch'
        ];
    }

    public function onPreDispatch(\Enlight_Event_EventArgs $args)
    {
        $shop = false;
        if (Shopware()->container()->initialized('shop')) {
            $shop = Shopware()->container()->get('shop');
        }

        if (!$shop) {
            $shop = Shopware()->container()->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault();
        }

        $config = Shopware()->container()->get('shopware.plugin.cached_config_reader')->getByPluginName($this->pluginName, $shop);

        $controller = $args->getSubject();
        $view = $controller->View();
     
        $view->assign(
            [
                'discount_from_value1'      => $config['discount_from_value1'],
                'discount_to_value1'        => $config['discount_to_value1'],
                'discount_price1'           => $config['discount_price1'],
                'discount_from_value2'      => $config['discount_from_value2'],
                'discount_to_value2'        => $config['discount_to_value2'],
                'discount_price2'           => $config['discount_price2'],
                'discount_from_value3'      => $config['discount_from_value3'],
                'discount_to_value3'        => $config['discount_to_value3'],
                'discount_price3'           => $config['discount_price3'],
                'shipping_free_from_value'  => $config['shipping_free_from_value'],
                'shipping_free_to_value'    => $config['shipping_free_to_value'],
            ]
        );
     
        $view->addTemplateDir($this->pluginDirectory . '/Resources/views/');
    }
}