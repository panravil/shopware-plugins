<?php

class Shopware_Controllers_Frontend_AboSchuler extends Enlight_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        $this->View()->addTemplateDir(__DIR__ . '/../../Resources/views/');
    }

    public function indexAction()
	{
		die('');
	}

    public function shippingAction()
    {
        $controllerName = $this->Request()->getParam('ctlName');
        $countries = Shopware()->Container()->get('dbal_connection')->createQueryBuilder()
                    ->select('*')
                    ->from('s_core_countries')
                    ->where('active = 1')
                    ->execute()->fetchAll(\PDO::FETCH_ASSOC);
        $countryArray = [];
        foreach($countries as $ct) {
            $countryArray[$ct['id']] = $ct['countryname'];
        }

        $this->View()->assign([
            'countryList'     => $countryArray,
            'aboGiftData'     => Shopware()->Session()->offsetGet('abo_gift_data') ? Shopware()->Session()->offsetGet('abo_gift_data') : null,
            'controllerName'  => $controllerName
        ]);
    }
}