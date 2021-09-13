<?php

namespace AboSchuler;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class AboSchuler extends Plugin
{
    public function install(InstallContext $context)
    {
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->update('s_articles_attributes', 'is_abo_article', 'boolean', [
            'label' 			=> 'Is ABO article',
            'supportText' 	    => 'This checkbox is for the ABO product.',
            'helpText' 			=> 'If checked, this will be a ABO product.',
            'translatable' 		=> true,
            'displayInBackend'  => true,
            'position' 			=> 1
        ]);
    }
    
    public function uninstall(UninstallContext $context)
    {
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->delete('s_articles_attributes', 'is_abo_article');
    }
}
