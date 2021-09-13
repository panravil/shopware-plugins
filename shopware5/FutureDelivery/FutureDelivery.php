<?php

namespace FutureDelivery;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class FutureDelivery extends Plugin
{
    public function install(InstallContext $installContext)
    {
		// create a new attribute using the attribute crud service
        $service = $this->container->get('shopware_attribute.crud_service');

		$service->update('s_articles_attributes', 'delivery_date_2', TypeMapping::TYPE_STRING, [
			'label' => 'Liefertermin 1',
            //'helpText' => 'Faktor für Berechnung eingeben',
			//attribute will be displayed in the backend module
			'displayInBackend' => true,
			//in case of multi_selection or single_selection type, article entities can be selected,
			'entity' => 'Shopware\Models\Article\Article',
			//numeric position for the backend view, sorted ascending
			'position' => 110,
			//user can modify the attribute in the free text field module
			'custom' => true,
        ], null, false, '1');
        
        $service->update('s_articles_attributes', 'delivery_date_3', TypeMapping::TYPE_STRING, [
			'label' => 'Liefertermin 2',
            //'helpText' => 'Faktor für Berechnung eingeben',
			//attribute will be displayed in the backend module
			'displayInBackend' => true,
			//in case of multi_selection or single_selection type, article entities can be selected,
			'entity' => 'Shopware\Models\Article\Article',
			//numeric position for the backend view, sorted ascending
			'position' => 111,
			//user can modify the attribute in the free text field module
			'custom' => true,
        ], null, false, '1');
        
        $service->update('s_order_details_attributes', 'order_quantity_2',TypeMapping::TYPE_INTEGER);
        $service->update('s_order_details_attributes', 'order_quantity_3',TypeMapping::TYPE_INTEGER);
        $service->update('s_order_basket_attributes', 'basket_quantity_2',TypeMapping::TYPE_INTEGER);
        $service->update('s_order_basket_attributes', 'basket_quantity_3',TypeMapping::TYPE_INTEGER);
		
		$service->update('s_order_attributes', 'total_detail_1',TypeMapping::TYPE_STRING);
        $service->update('s_order_attributes', 'total_detail_2',TypeMapping::TYPE_STRING);
        $service->update('s_order_attributes', 'total_detail_3',TypeMapping::TYPE_STRING);

        $service->update('s_order_attributes', 'order_splitted',TypeMapping::TYPE_INTEGER);

        //Shopware()->Db()->query("ALTER TABLE `s_order_attributes` DROP CONSTRAINT `s_order_attributes_ibfk_1`");
        //Shopware()->Db()->query("ALTER TABLE `s_order_billingaddress_attributes` DROP CONSTRAINT `s_order_billingaddress_attributes_ibfk_2`");
        //Shopware()->Db()->query("ALTER TABLE `s_order_details_attributes` DROP CONSTRAINT `s_order_details_attributes_ibfk_1`");
        //Shopware()->Db()->query("ALTER TABLE `s_order_documents_attributes` DROP CONSTRAINT `s_order_documents_attributes_ibfk_1`");
        //Shopware()->Db()->query("ALTER TABLE `s_order_shippingaddress_attributes` DROP CONSTRAINT `s_order_shippingaddress_attributes_ibfk_1`");
    }

    public function uninstall(UninstallContext $uninstallContext)
    {
        $service = $this->container->get('shopware_attribute.crud_service');
		$service->delete('s_articles_attributes', 'delivery_date_2');
		$service->delete('s_articles_attributes', 'delivery_date_3');
		$service->delete('s_order_details_attributes', 'order_quantity_2');
        $service->delete('s_order_details_attributes', 'order_quantity_3');
        $service->delete('s_order_basket_attributes', 'basket_quantity_2');
		$service->delete('s_order_basket_attributes', 'basket_quantity_3');
		
		$service->delete('s_order_attributes', 'total_detail_1');
		$service->delete('s_order_attributes', 'total_detail_2');
		$service->delete('s_order_attributes', 'total_detail_3');
		$service->delete('s_order_attributes', 'order_splitted');
		
		$metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
		$metaDataCache->deleteAll();
		Shopware()->Models()->generateAttributeModels(['s_articles_attributes']);
		Shopware()->Models()->generateAttributeModels(['s_order_details_attributes']);
		Shopware()->Models()->generateAttributeModels(['s_order_basket_attributes']);
		Shopware()->Models()->generateAttributeModels(['s_order_attributes']);
        Shopware()->Models()->generateAttributeModels(['s_order_attributes']);
        
        // clear cache
        $uninstallContext->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
    }

}




