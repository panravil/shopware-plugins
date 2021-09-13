<?php

namespace ShopOrderGifts;

use Doctrine\ORM\Tools\SchemaTool;
use ShopOrderGifts\Models\Gift;
use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\Context\UninstallContext;

/**
 * Shopware-Plugin ShopOrderGifts.
 */
class ShopOrderGifts extends Plugin
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('shop_order_gifts.plugin_dir', $this->getPath());
        parent::build($container);
    }

    /**
	 * Registers plugin-models
	 *
	 * @package      TrendPoint
	 * @author       TrendPoint GmbH
	 * @see          Shopware_Components_Plugin_Bootstrap::install()
	 *
	 * @param void
	 *
	 * @return void
	 *
	 */
	public function registerModels(){
		$sMediaTables = $this->sCheckGiftTables();
		// if(!$sMediaTables){
			$tool = new SchemaTool($this->container->get('models'));
			try {
				$tool->createSchema($this->getModelMetaData());
			} catch (\Doctrine\ORM\Tools\ToolsException $e){
			}
		// }
	}

	/**
	 * removes plugin-models
	 *
	 * @package      TrendPoint
	 * @author       TrendPoint GmbH
	 * @see          Shopware_Components_Plugin_Bootstrap::install()
	 *
	 * @param void
	 *
	 * @return void
	 *
	 */
	public function removeModels(){
		$sMediaTables = $this->sCheckGiftTables();
		// if(!$sMediaTables){
			$tool = new SchemaTool($this->container->get('models'));
			$tool->dropSchema($this->getModelMetaData());
		// }
	}

	/**
	 * Helper function
	 * returns the meta-data for our model-classes to use in
	 * install and uninstall function
	 *
	 * @package      TrendPoint
	 * @author       TrendPoint GmbH
	 * @see          Shopware_Components_Plugin_Bootstrap::install()
	 *
	 * @param void
	 *
	 * @return void
	 */
	public function getModelMetaData(){
		$models = $this->container->get('models');
		$classes = array();
		$classes[] = $models->getClassMetadata(Gift::class);
		return $classes;
	}

	/**
	 * Count total Active FAQ
	 *
	 * @package      TrendPoint Plugins
	 * @author       TrendPoint GmbH
	 * @see          Shopware_Components_Plugin_Bootstrap::install()
	 *
	 */
	private function sCheckGiftTables()
	{
		$db = Shopware()->Db();
		$sCount = 0;
		$sTables = array('s_plugin_order_gifts');
		$sTableExist = $db->fetchAll("SELECT table_name FROM information_schema.tables WHERE table_name = ?", $sTables);
		foreach ($sTableExist as $sTable) {
			foreach ($sTables as $table) {
				if($sTable['table_name'] == $table){
					$sCount++;
					break;
				}
			}
		}
		if(count($sTables) != $sCount){
			return 0;
		}
		return 1;
	}

	/**
	 * Installs the plugin
	 *
	 * @package     TrendPoint
	 *
	 * @param void
	 *
	 * @return array with status and cache instructions
	 *
	 */
	public function install(InstallContext $context){
		try {
            $service = $this->container->get('shopware_attribute.crud_service');
            $service->update('s_order_basket_attributes', 'gift_article', 'string');
            $service->update('s_order_basket_attributes', 'gift_article_qty_from', 'string');
            $service->update('s_order_basket_attributes', 'gift_article_qty_to', 'string');
        	parent::install($context);
		} catch (Exception $e) {
			return array(
				'success' => false,
				'message' => $e->getMessage()
			);
		}
		return array('success' => true, 'invalidateCache' => array('frontend'));
	}

	/**
	 * Update the plugin
	 *
	 * @package     TrendPoint
	 *
	 * @param void
	 *
	 * @return array with status and cache instructions
	 *
	 */
	public function update(UpdateContext $context){
		try {
        	// $this->registerModels();
        	parent::update($context);
		} catch (Exception $e) {
			return array(
				'success' => false,
				'message' => $e->getMessage()
			);
		}
		return array('success' => true, 'invalidateCache' => array('frontend'));
	}

	/**
	 * this derived method is called automatically each time the plugin will be uninstalled
	 *
	 * On uninstall remove attributes and re-generate customer-attribute models
	 * @return bool | if false is return the installation will failed
	 */
 
	public function uninstall(UninstallContext $context){
		try {
            $service = $this->container->get('shopware_attribute.crud_service');
            $service->delete('s_order_basket_attributes', 'gift_article');
            $service->delete('s_order_basket_attributes', 'gift_article_qty_from');
            $service->delete('s_order_basket_attributes', 'gift_article_qty_to');
        	parent::uninstall($context);
		} catch (\Exception $e) {
			// noting to do here.
		}
		return array('success' => true, 'invalidateCache' => array('frontend'));
	}
}