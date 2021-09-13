<?php

class Shopware_Controllers_Frontend_ShopOrderGifts extends Shopware_Controllers_Frontend_Checkout
{


    private function getPlugin()
    {
        return Shopware()->Container()->get('kernel')->getPlugins()['ShopOrderGifts'];
    }

    public function addOrderGiftAction() {
    	if ($this->Request()->isPost()) {
    		$ordernumber = $this->Request()->getParam('ordernumber');
            if (empty($ordernumber)) {
                $this->View()->sBasketInfo = Shopware()->Snippets()->getNamespace()->get(
                    'CheckoutSelectPremiumVariant',
                    'Please select an option to place the required premium to the cart',
                    true
                );
            } else {
                $session = Shopware()->Session();
                $giftId = $this->Request()->getParam('giftId', 0);
                if(!empty($giftId)) {
                    $sOrderGiftId = $session->offsetExists('sOrderGiftId') ? $session->offsetGet('sOrderGiftId') : [];
                    array_push($sOrderGiftId, $giftId);
                    $session->offsetSet('sOrderGiftId', $sOrderGiftId);
                }
//                Shopware()->Container()->get('shop_order_gifts.subscriber.frontend')->sInsertOrderGift($ordernumber, $giftId);
            }
        }

        $this->redirect(array(
        	'controller' => 'checkout',
        	'action' => $this->Request()->getParam('sTargetAction', 'index')
        ));
    }
}