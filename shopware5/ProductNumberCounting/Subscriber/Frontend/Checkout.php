<?php
namespace ProductNumberCounting\Subscriber\Frontend;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Shopware\Components\BasketSignature\Basket;
use Shopware\Bundle\SearchBundle\Criteria;

class Checkout implements SubscriberInterface
{
    private $container;
    private $service;
    private $pluginConfig;
    private $discountStreamProducts;
    private $shippingStreamProducts;
    private $discountPrice;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->service = $container->get('shopware_attribute.crud_service');
        try {
            $shopId = $this->container->get('shopware_storefront.context_service')->getShopContext()->getShop()->getId();
            $this->pluginConfig = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName(
                'ProductNumberCounting',
                Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->find($shopId)
            );
 
            $discountStreamId = $this->pluginConfig['selectDiscountProductStream'];

            $conditions = Shopware()->Container()->get('dbal_connection')->createQueryBuilder()
                    ->select('conditions')
                    ->from('s_product_streams')
                    ->where('id = :discountStreamId')
                    ->setParameter('discountStreamId', $discountStreamId)
                    ->execute()->fetchAll(\PDO::FETCH_ASSOC)[0]['conditions'];
            
            $product_ids = array();

            if(isset($conditions) && !empty($conditions)) {
                $streamRepo = $container->get('shopware_product_stream.repository');
                $criteria = new Criteria();
                $conditions = json_decode($conditions, true);
                $conditions = $streamRepo->unserialize($conditions);
                foreach ($conditions as $condition) { 
                    $criteria->addCondition($condition);
                }
                $context = $container->get('shopware_storefront.context_service')->getShopContext();
                $service = $container->get('shopware_search.product_number_search');
                $result = $service->search($criteria, $context);
                foreach($result->getProducts() as $product) {
                    $product_ids[] = $product->getId();
                }
            } else {
                $results = Shopware()->Container()->get('dbal_connection')->createQueryBuilder()
                            ->select('article_id')
                            ->from('s_product_streams_selection')
                            ->where('stream_id = :discountStreamId')
                            ->setParameter('discountStreamId', $discountStreamId)
                            ->execute()->fetchAll(\PDO::FETCH_ASSOC);
                foreach($results as $result) {
                    $product_ids[] = $result['article_id']; 
                }
            }     

            $this->discountStreamProducts = $product_ids;
            
            $shippingStreamId = $this->pluginConfig['selectShippingProductStream'];
            
            $conditions = Shopware()->Container()->get('dbal_connection')->createQueryBuilder()
                    ->select('conditions')
                    ->from('s_product_streams')
                    ->where('id = :shippingStreamId')
                    ->setParameter('shippingStreamId', $shippingStreamId)
                    ->execute()->fetchAll(\PDO::FETCH_ASSOC)[0]['conditions'];
            
            $product_ids = array();

            if(isset($conditions) && !empty($conditions)) {
                $streamRepo = $container->get('shopware_product_stream.repository');
                $criteria = new Criteria();
                $conditions = json_decode($conditions, true);
                $conditions = $streamRepo->unserialize($conditions);
                foreach ($conditions as $condition) { 
                    $criteria->addCondition($condition);
                }
                $context = $container->get('shopware_storefront.context_service')->getShopContext();
                $service = $container->get('shopware_search.product_number_search');
                $result = $service->search($criteria, $context);
                foreach($result->getProducts() as $product) {
                    $product_ids[] = $product->getId();
                }
            } else {
                $results = Shopware()->Container()->get('dbal_connection')->createQueryBuilder()
                    ->select('article_id')
                    ->from('s_product_streams_selection')
                    ->where('stream_id = :shippingStreamId')
                    ->setParameter('shippingStreamId', $shippingStreamId)
                    ->execute()->fetchAll(\PDO::FETCH_ASSOC);

                foreach($results as $result) {
                    $product_ids[] = $result['article_id']; 
                }
            }

            $this->shippingStreamProducts = $product_ids;

        } catch (\Exception $exception) {
            //service shop does not exist
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_Frontend_Checkout_AjaxCart' => 'checkAjaxCartAction',
            'Shopware_Modules_Basket_GetBasket_FilterResult' => 'modifyGetBasket',
            'Shopware_Controllers_Frontend_Checkout::getBasket::after' => 'afterGetBasket',
            'Shopware_Modules_Order_SendMail_Filter' => 'modifySOrderMail',
            'Enlight_Controller_Action_Frontend_Checkout_Finish' => 'checkFinishAction',
            'Shopware_Modules_Order_SendMail_FilterVariables' => 'saveDiscountProduct'
        ];
    }

    public function modifySOrderMail(\Enlight_Event_EventArgs $args) {
        $context = $args->get('context');
        $controller = $args->get('subject');
        
        if($this->discountPrice > 0) {
            $context['sDiscountCost'] = $controller->sSYSTEM->sMODULES['sArticles']->sFormatPrice($this->discountPrice). ' ' . $controller->sBasketData['sCurrencyName'];
            $context['sDiscountCost'] = '-'.$context['sDiscountCost'];
        }
        
        $mail = Shopware()->TemplateMail()->createMail('sORDER', $context);

        $config = $controller->getConfig();
        $mail->addTo($controller->sUserData["additional"]["user"]["email"]);
        if(!$config->get("sNO_ORDER_MAIL")) {
            $mail->addBcc($config->get('sMAIL'));
        }
        return $mail;
    }

    public function checkAjaxCartAction(\Enlight_Event_EventArgs $args) {
        $controller = $args->getSubject();
        $view = $controller->View();
        $request = $controller->Request();
        $response = $controller->Response();
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $data   = $this->getDiscountData($basket);
        $sub_total_price = str_replace(',', '.', $basket['Amount']);
        $total_price = (float)$sub_total_price - $data['discount_price']; 

        $view->assign([
            'TotalPrice'     => $total_price
        ]);
    }

    public function modifyGetBasket(\Enlight_Event_EventArgs $args) 
    {
        $basket = $args->getReturn();
        $data   = $this->getDiscountData($basket);
        $basket['AmountNumeric'] = $basket['AmountNumeric'] - $data['discount_price'];
        $basket['AmountNetNumeric'] = $basket['AmountNetNumeric'] - $data['discount_price'];
        $basket['DiscountPrice'] = $data['discount_price'];
        
        $sessionID = $basket['content'][0]['sessionID'];
        $free_shipping_flag = $data['free_shipping_flag'] | $this->is_contain_product_shipping_free($basket['content']);
        $this->setShippingFree($sessionID, $free_shipping_flag);
        
        return $basket;
    }

    public function afterGetBasket(\Enlight_Event_EventArgs $args) {
        $basket = $args->getReturn();
        $data   = $this->getDiscountData($basket);
        
        if($data['free_shipping_flag']) {
            $basket['AmountNumeric'] = $basket['AmountNumeric'] - $basket['sShippingcosts'];
            $basket['AmountNetNumeric'] = $basket['AmountNetNumeric'] - $basket['sShippingcosts'];
            $basket['sShippingcosts'] = 0;
            $basket['sShippingcostsNet'] = 0;
            $basket['sShippingcostsWithTax'] = 0;
            $basket['shippingfree'] = 1;
        }        
        $basket['DiscountPrice'] = $data['discount_price'];
        $this->discountPrice = $data['discount_price'];
        $args->setReturn($basket);
    }
    
    public function checkFinishAction(\Enlight_Event_EventArgs $args) {
        $controller = $args->getSubject();
        $view = $controller->View();
        
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $data   = $this->getDiscountData($basket);
        
        if(isset($data['discount_price']) && $data['discount_price'] > 0) {
            $this->service->update('s_order_attributes', 'vmgutschrift', 'integer', [], null, false, $data['discount_price']);
        }
        $view->assign([
            'sDiscountPrice'     => $data['discount_price']
        ]);
    }
    
    public function saveDiscountProduct(\Enlight_Event_EventArgs $args)
    {
        $variables = $args->getReturn();
         
        $orderData = Shopware()->Db()->fetchAll("SELECT * FROM `s_order_details` WHERE ordernumber = ".$variables['ordernumber']);
        
        if($this->discountPrice > 0) {
            Shopware()->Db()->query("INSERT INTO `s_order_details` (`orderID`, `ordernumber`, `articleID`, `articleordernumber`, `price`, `quantity`, `name`, `releasedate`, `modus`, `esdarticle`, `taxID`, `tax_rate`, config ) values(".$orderData[0]['orderID'].", ".$orderData[0]['ordernumber'].", 0, 'DISCOUNT', -".$this->discountPrice.", 1, 'Herbstkatalog 2020', '0000-00-00', 2, 0, 0, ".$orderData[0]['tax_rate'].", '')");
        }

        $args->setReturn($variables);
    }

    public function getDiscountData($basket) {
        $total_discount_quantity = 0;
        $total_shipping_quantity = 0;

        foreach($basket['content'] as $item) {
            if(in_array($item['articleID'], $this->discountStreamProducts)) {
                $attr34 = 0;
                $attr30 = 0;
                $item_core = (array)$item['additional_details']['attributes']['core'];
                foreach($item_core as $core) {
                    $attr30 = $core['attr30'];
                    $attr34 = $core['attr34'];
                }
                
                $quantity = ($attr34 == 1) ? $attr30*$item['quantity'] : $item['quantity'];
                $total_discount_quantity += $quantity;
            }

            if(in_array($item['articleID'], $this->shippingStreamProducts)) {
                $attr34 = 0;
                $attr30 = 0;
                $item_core = (array)$item['additional_details']['attributes']['core'];
                foreach($item_core as $core) {
                    $attr30 = $core['attr30'];
                    $attr34 = $core['attr34'];
                }
                
                $quantity = ($attr34 == 1) ? $attr30*$item['quantity'] : $item['quantity'];
                $total_shipping_quantity += $quantity;
            }
        }

        $discount_from_value1   = $this->pluginConfig['discount_from_value1'];
        $discount_to_value1     = $this->pluginConfig['discount_to_value1'];
        $discount_price1        = $this->pluginConfig['discount_price1'];
        $discount_from_value2   = $this->pluginConfig['discount_from_value2'];
        $discount_to_value2     = $this->pluginConfig['discount_to_value2'];
        $discount_price2        = $this->pluginConfig['discount_price2'];
        $discount_from_value3   = $this->pluginConfig['discount_from_value3'];
        $discount_to_value3     = $this->pluginConfig['discount_to_value3'];
        $discount_price3        = $this->pluginConfig['discount_price3'];
        $shipping_free_from_value = $this->pluginConfig['shipping_free_from_value'];

        $discount_price         = $this->getDiscountPrice($total_discount_quantity, $discount_from_value1, $discount_to_value1, $discount_price1, $discount_from_value2, $discount_to_value2, $discount_price2, $discount_from_value3, $discount_to_value3, $discount_price3);
        $free_shipping_flag     = $this->checkShippingFree($total_shipping_quantity, $shipping_free_from_value);

        $data = array(
            'discount_price'     => $discount_price,
            'free_shipping_flag' => $free_shipping_flag
        );

        return $data;
    }

    public function getDiscountPrice($quantity, $from_val1, $to_val1, $price1, $from_val2, $to_val2, $price2, $from_val3, $to_val3, $price3) {
        if(isset($from_val3) && isset($price3)) {
            if($quantity >= $from_val3) {
                if(!isset($to_val3)) {
                    return $price3;
                } else if($quantity <= $to_val3) {
                    return $price3;
                }
            }
        }

        if(isset($from_val2) && isset($price2)) {
            if($quantity >= $from_val2) {
                if(!isset($to_val2)) {
                    return $price2;
                } else if($quantity <= $to_val2) {
                    return $price2;
                }
            }
        }

        if(isset($from_val1) && isset($price1)) {
            if($quantity >= $from_val1) {
                if(!isset($to_val1)) {
                    return $price1;
                } else if($quantity <= $to_val1) {
                    return $price1;
                }
            }
        }

        return 0;
    }

    public function checkShippingFree($quantity, $from_val) {
        if(isset($from_val) && $quantity >= $from_val) {
            return true;
        } else {
            return false;
        }
    }

    public function setShippingFree($sessionID, $free_shipping_flag) {
        $attributes = array(
            'shippingfree' => $free_shipping_flag
        );
        Shopware()->Db()->update('s_order_basket', $attributes, array('sessionID = ?' => $sessionID));
        return 0;
    }

    public function is_contain_product_shipping_free($data) {
        $temp = false;
        foreach($data as $item) {
            if ($item['additional_details']['shippingfree'] ) $temp = true;
        }
        return $temp;
    }
}   
?>