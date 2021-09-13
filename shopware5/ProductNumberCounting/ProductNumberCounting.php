<?php

namespace ProductNumberCounting;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class ProductNumberCounting extends Plugin
{
    public function install(InstallContext $context)
    {
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->update('s_articles_attributes', 'product_number_counting', 'boolean', [
            'label' 			=> 'Gutschriften Flaschenanzahl',
            'supportText' 		=> 'This plugin count number of product for discount.',
            'helpText' 			=> 'If select the product stream and set the value for discounting of product.',
            'translatable' 		=> true,
            'displayInBackend' 	=> true,
            'position' 			=> 4
        ]);
    }
    
    public function uninstall(UninstallContext $context)
    {
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->delete('s_articles_attributes', 'product_number_counting');
    }
}
