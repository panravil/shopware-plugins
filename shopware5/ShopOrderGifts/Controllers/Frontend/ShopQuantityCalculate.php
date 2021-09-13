<?php

class Shopware_Controllers_Frontend_ShopQuantityCalculate extends Shopware_Controllers_Frontend_Checkout
{


    public function cartItemsAction()
    {
        Enlight()->Plugins()->Controller()->Json()->setPadding();
        if ($this->Request()->getParam('sArticle') && $this->Request()->getParam('sQuantity')) {
           Shopware()->Modules()->Basket()->sUpdateArticle($this->Request()->getParam('sArticle'), $this->Request()->getParam('sQuantity'));
        }
        Shopware()->Session()->offsetSet('sUpdateBasketAttribute', 1);
        echo json_encode($this->getBasket());
        die();
    }


    protected function getPlugin()
    {
        return Shopware()->Container()->get('kernel')->getPlugins()['ShopQuantityCalculate'];
    }
}