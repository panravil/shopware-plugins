<?php

namespace AboSchuler\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FrontendSubscriber implements SubscriberInterface
{
    private $container;
    private $service;
    private $countries;

    public function __construct(ContainerInterface $container)
    {
        $this->container    = $container;
        $this->service      = $container->get('shopware_attribute.crud_service');

        $countries = Shopware()->Container()->get('dbal_connection')->createQueryBuilder()
                    ->select('*')
                    ->from('s_core_countries')
                    ->where('active = 1')
                    ->execute()->fetchAll(\PDO::FETCH_ASSOC);
        $countryArray = [];
        foreach($countries as $ct) {
            $countryArray[$ct['id']] = $ct['countryname'];
        }
        $this->countries = $countryArray;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch'                 => 'onPreDispatch',
            'Enlight_Controller_Action_Frontend_Detail_Index'       => 'detailIndexAction',
            'Enlight_Controller_Action_Frontend_Checkout_AjaxCart'  => 'addAjaxCartAction',
            'Enlight_Controller_Action_Frontend_Checkout_Cart'      => 'cartAction',
            'Enlight_Controller_Action_Frontend_Checkout_Confirm'   => 'confirmAction',
            'sOrder::sSaveOrder::after'                             => 'onSaveOrder',
            'sBasket::sDeleteArticle::after'                        => 'deleteArticle',
            'Shopware_Modules_Order_SendMail_FilterVariables'       => 'aboOrderFilterVariant',
        ];
    }

    public function onPreDispatch(\Enlight_Event_EventArgs $args)
    {
        $controller = $args->getSubject();
        $view = $controller->View();
     
        $view->assign([
            'addBtnStatus'     => $_SESSION["is_abo_gift"] ? $_SESSION["is_abo_gift"] : false
        ]);

        $view->addTemplateDir($this->pluginDirectory . '/Resources/views');
    }
    
    public function detailIndexAction(\Enlight_Event_EventArgs $args)
    {
        $controller     = $args->getSubject();
        $view           = $controller->View();
        $request        = $controller->Request();

        $id             = $request->sArticle;
        $number         = $request->getParam('number');
        $selection      = $request->getParam('group', []);
        $categoryId     = $request->get('sCategory');

        try {
            $product = Shopware()->Modules()->Articles()->sGetArticleById(
                $id,
                $categoryId,
                $number,
                $selection
            );
        } catch (\Exception $e) {
            $product = null;
        }

        $orderNumbers = $product['abo_inhalt'];
        
        $aboPakets = [];
        
        if(trim($orderNumbers) != '') {
            $orderNumbers = explode(';', $orderNumbers);
            foreach($orderNumbers as $orderNumber){
                $article = Shopware()->Modules()->Articles()->sGetProductByOrdernumber($orderNumber);
                if ($article){
                    $aboPakets[] = $article;
                }
            }
        }

        $view->assign([
            'countryList'     => $this->countries,
            'aboPakets'       => $aboPakets,
            'exist_in_basket' => $_SESSION["is_article_exist_in_basket"] ? true : false,
            'aboGiftData'     => Shopware()->Session()->offsetGet('abo_gift_data') ? Shopware()->Session()->offsetGet('abo_gift_data') : null
        ]);
    }

    public function addAjaxCartAction(\Enlight_Event_EventArgs $args) 
    {
        $controller     = $args->getSubject();
        $view           = $controller->View();
        $request        = $controller->Request();
        $basket         = Shopware()->Modules()->Basket()->sGetBasket();
        $aboGiftData    = $request->getParam('aboGift') ? $request->getParam('aboGift') : null;

        $countries = $this->countries;
        if($aboGiftData) {
            $aboGiftData['country_id']  = $aboGiftData['country'];
            $aboGiftData['country']     = $countries[$aboGiftData['country']];
            Shopware()->Session()->offsetSet('abo_gift_data', $aboGiftData);
            $_SESSION["is_abo_gift"] = true;
        }
        
        $_SESSION["is_article_exist_in_basket"] = sizeof($basket) > 0 ? true : false;

        $view->assign([
            'aboGiftData'     => Shopware()->Session()->offsetGet('abo_gift_data') ? Shopware()->Session()->offsetGet('abo_gift_data') : null
        ]);
    }

    public function deleteArticle(\Enlight_Event_EventArgs $args) 
    {
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        
        if(sizeof($basket) == 0) {
            $this->sessionDestory();
        }

    }

    public function cartAction(\Enlight_Event_EventArgs $args)
    {
        $controller     = $args->getSubject();
        $request        = $controller->Request();
        $view           = $controller->View();
        $aboGiftData    = $request->getParam('aboGift') ? $request->getParam('aboGift') : null;
        
        if($aboGiftData) {
            $countries = $this->countries;
            $aboGiftData['country_id']  = $aboGiftData['country'];
            $aboGiftData['country']     = $countries[$aboGiftData['country']];
            Shopware()->Session()->offsetSet('abo_gift_data', $aboGiftData);
        }
        
        $view->assign([
            'aboGiftData'     => Shopware()->Session()->offsetGet('abo_gift_data') ? Shopware()->Session()->offsetGet('abo_gift_data') : null,
            'controlllerName' => 'cart'
        ]);
    }

    public function confirmAction(\Enlight_Event_EventArgs $args)
    {
        $controller = $args->getSubject();
        $request    = $controller->Request();
        $view       = $controller->View();
        
        $aboGiftData    = $request->getParam('aboGift') ? $request->getParam('aboGift') : null;

        if($aboGiftData) {
            $countries = $this->countries;
            $aboGiftData['country_id']  = $aboGiftData['country'];
            $aboGiftData['country']     = $countries[$aboGiftData['country']];
            Shopware()->Session()->offsetSet('abo_gift_data', $aboGiftData);
        }

        $view->assign([
            'aboGiftData'     => Shopware()->Session()->offsetGet('abo_gift_data') ? Shopware()->Session()->offsetGet('abo_gift_data') : null,
            'controlllerName' => 'confirm'
        ]);
    }

    public function onSaveOrder(\Enlight_Hook_HookArgs $args)
    {
        $orderNumber = $args->getReturn();
        
        if ($orderNumber === '') {
            return;
        }
        
        $sql = 'SELECT id, userID
                FROM s_order
                WHERE ordernumber = ? ';

        $order = Shopware()->Db()->fetchRow($sql, array($orderNumber));
        $aboGiftData = Shopware()->Session()->offsetGet('abo_gift_data') ? Shopware()->Session()->offsetGet('abo_gift_data') : null;
        if(!empty($order) && $aboGiftData) {
            $order_shippping = Shopware()->Db()->fetchRow("SELECT id 
                                FROM `s_order_shippingaddress` 
                                WHERE orderID = ".$order['id']." AND userID = ".$order['userID']);

            Shopware()->Db()->query("UPDATE `s_order_shippingaddress` 
                                    SET firstname   = '".$aboGiftData['firstname']."',
                                        lastname    = '".$aboGiftData['lastname']."',
                                        street      = '".$aboGiftData['street']."',
                                        zipcode     = '".$aboGiftData['zipcode']."',
                                        city        = '".$aboGiftData['city']."',
                                        countryID   = ".(int)$aboGiftData['country_id']."
                                    WHERE id = ".$order_shippping['id']);
        }
        
        $this->sessionDestory();
    } 

    public function aboOrderFilterVariant(\Enlight_Event_EventArgs $args)
    {
        $sOrder = $args->getSubject();
        $return = $args->getReturn();

        $aboGiftData = Shopware()->Session()->offsetGet('abo_gift_data') ? Shopware()->Session()->offsetGet('abo_gift_data') : null;
        if($aboGiftData) {
            $return['shippingaddress']['firstname']     = $aboGiftData['firstname'];
            $return['shippingaddress']['lastname']      = $aboGiftData['lastname'];
            $return['shippingaddress']['street']        = $aboGiftData['street'];
            $return['shippingaddress']['zipcode']       = $aboGiftData['zipcode'];
            $return['shippingaddress']['city']          = $aboGiftData['city'];
            $return['additional']['countryShipping']['countryname'] = $aboGiftData['country'];
        }

        return $return;
    }

    public function sessionDestory() {
        unset(Shopware()->Session()->abo_gift_data);
        unset($_SESSION["is_abo_gift"]);
        unset($_SESSION["is_article_exist_in_basket"]);
    }
}