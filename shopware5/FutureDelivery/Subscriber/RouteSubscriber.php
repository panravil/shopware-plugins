<?php

namespace FutureDelivery\Subscriber;

use Doctrine\Common\Util\Debug;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Plugin\ConfigReader;
use Enlight_Event_EventArgs;
use Enlight_Hook_HookArgs;
use Shopware\Components\Theme\LessDefinition;
use Doctrine\Common\Collections\ArrayCollection;
use sBasket;

class RouteSubscriber implements SubscriberInterface
{

    private $pluginDirectory;
	
	public function __construct($pluginName, $pluginDirectory)
    {
        $this->pluginDirectory = $pluginDirectory;
    }
	
    public static function getSubscribedEvents()
    {
        return [
			'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onPostDispatch',
			'Enlight_Controller_Action_PostDispatchSecure_Frontend_Listing' => 'onFrontendListing',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'addJsFiles',
			'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onFrontendDetail',
			'Enlight_Controller_Action_Frontend_Checkout_Confirm'        => 'onCheckoutConfirm',
			'Shopware_Controllers_Frontend_Checkout::ajaxAddArticleCartAction::before' => 'onAddArticleInCartBefore',
			'Shopware_Controllers_Frontend_Checkout::ajaxAddArticleCartAction::after' => 'onAddArticleInCartAfter',
			'Shopware_Controllers_Frontend_Checkout::changeQuantityAction::before' => 'onChangeQuantityAction',
			'Shopware_Controllers_Frontend_Checkout::changeQuantityAction::after' => 'onChangeQuantityActionAfter',
			'Shopware_Controllers_Frontend_Checkout::getInstockInfo::replace' => 'ongetstockinfo',
			'sOrder::sSaveOrder::before'=> 'beforeSaveOrder',
            'Shopware_CronJob_SplitOrders' => 'splitOrders',
			'Shopware_Modules_Order_SendMail_FilterVariables' => 'onSendOrderMail'
        ];
    }
	
    /**
     * Provide the file collection for js files
     *
     * @param Enlight_Event_EventArgs $args

     * @return \Doctrine\Common\Collections\ArrayCollection

     */
	public function addJsFiles(Enlight_Event_EventArgs $args)
    {
        $jsFiles = array($this->pluginDirectory . '/Resources/views/frontend/_public/src/js/task.js');
        return new ArrayCollection($jsFiles);
    }

	public function onCheckoutConfirm(\Enlight_Event_EventArgs $args)
	{
		$controller = $args->getSubject();
		$view = $args->getSubject()->View();
		$basket = Shopware()->Modules()->Basket()->sGetBasket();
		$basketData = $basket['content'];

        $flag = 0;

		foreach($basketData as $data)
		{
			$sql = "SELECT basket_quantity_2,basket_quantity_3 FROM s_order_basket_attributes WHERE basketID = ? ORDER BY id DESC LIMIT 1";
			$rows = Shopware()->Db()->fetchAll($sql, [$data["id"]]);
			foreach($rows as $row)
			{
				$data["basket_quantity_2"] = $row["basket_quantity_2"];
				if($data["basket_quantity_2"] > 0) $flag++;
				$data["basket_quantity_3"] = $row["basket_quantity_3"];
				if($data["basket_quantity_3"] > 0) $flag++;
			}
		}

		if($flag > 0)
		{
			Shopware()->Session()->sPaymentID = 4;
			Shopware()->Front()->Request()->setPost('sPayment', 4);
			Shopware()->Modules()->Admin()->sUpdatePayment();

			// unset(Shopware()->Session()->sDispatch);
		}
	}
	
	public function onFrontendDetail(Enlight_Event_EventArgs $args)
    {		
        Shopware()->Front()->Response()->setHeader('Cache-Control', 'private, no-cache');
        $view = $args->getSubject()->View();
		$view->addTemplateDir($this->pluginDirectory . '/Resources/views');
    }
	
	public function onAddArticleInCartBefore(Enlight_Hook_HookArgs $args)
    {
		
		$controller = $args->getSubject();
        $request = $controller->Request();
		
		$qty = $request->getParam('sQuantity');
		$qty2 = $request->getParam('sQuantity2');
		$qty3 = $request->getParam('sQuantity3');
		
		$quantity = $request->getParam('sQuantity');
		/*if(!is_null($qty2) && is_null($qty3))
		{
			$quantity = ($qty+$qty2);
		}
		elseif(!is_null($qty3) && is_null($qty2))
		{
			$quantity = ($qty+$qty3);
		}
		elseif(!is_null($qty3) && !is_null($qty2))
		{*/
			$quantity = ($qty+$qty2+$qty3);
		//}
		
		
		$orderNumber = $request->getParam('sAdd');
		$articleObj = Shopware()->Modules()->Articles()->sGetProductByOrdernumber($orderNumber);
		$maxpurchase = $articleObj['maxpurchase'];
		
		if(isset($maxpurchase) && $quantity > $maxpurchase)
		{
			// Accessing AttributeID
			$session = Shopware()->Session();
			$sql = "SELECT id,quantity FROM s_order_basket WHERE sessionID = ? AND ordernumber='$orderNumber'";
			$basketRow = Shopware()->Db()->fetchAll($sql, [Shopware()->Session()->get('sessionId')]);
			
			if(count($basketRow) > 0)
			{
				$newQuantity  = ($basketRow[0]['quantity'] + $quantity);
				if($newQuantity > $maxpurchase)
				{
					$request->setParam('sQuantity',$basketRow[0]['quantity']);
				}
			}
			else
			{
				//Get Request Parameters
				$qty = $request->getParam('sQuantity');
				$qty2 = $request->getParam('sQuantity2');
				$qty3 = $request->getParam('sQuantity3');
				/*
				//Adding future Quantity in basket attribute
				if(!is_null($qty2) && is_null($qty3))
				{
					
					$quantity = ($qty+$qty2);
					$request->setParam('sQuantity',$quantity);
					
				}
				elseif(!is_null($qty3) && is_null($qty2))
				{
					
					$quantity = ($qty+$qty3);
					$request->setParam('sQuantity',$quantity);
					
				}
				elseif(!is_null($qty3) && !is_null($qty2))
				{*/
					
					$quantity = ($qty+$qty2+$qty3);
					$request->setParam('sQuantity',$quantity);

				//}
			}
			
		}
		else
		{ 
			$session = Shopware()->Session();
			$sql = "SELECT id,quantity FROM s_order_basket WHERE sessionID = ? AND ordernumber='$orderNumber'";
			$basketRow = Shopware()->Db()->fetchAll($sql, [Shopware()->Session()->get('sessionId')]);
			
			if(count($basketRow) > 0)
			{
				$newQuantity  = ($basketRow[0]['quantity'] + $quantity);
				if($newQuantity > $maxpurchase)
				{
					$request->setParam('sQuantity',$basketRow[0]['quantity']);
					$request->setParam('sQuantity2',0);
					$request->setParam('sQuantity3',0);
				}
				else
				{
					$request->setParam('sQuantity',$quantity);
				}
			}
			else
			{
				//Get Request Parameters
				$qty = $request->getParam('sQuantity');
				$qty2 = $request->getParam('sQuantity2');
				$qty3 = $request->getParam('sQuantity3');

				//Adding future Quantity in basket attribute
				/*if(!is_null($qty2) && is_null($qty3))
				{
					
					$quantity = ($qty+$qty2);
					$request->setParam('sQuantity',$quantity);
					
				}
				elseif(!is_null($qty3) && is_null($qty2))
				{
					
					$quantity = ($qty+$qty3);
					$request->setParam('sQuantity',$quantity);
					
				}
				elseif(!is_null($qty3) && !is_null($qty2))
				{*/
					
					$quantity = ($qty+$qty2+$qty3);
					$request->setParam('sQuantity',$quantity);

				//}
			}
				
		}
		

    }
	
	
	public function onAddArticleInCartAfter(Enlight_Hook_HookArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();
		//Show All Parameters
		//$request->getParams()
				
		// Accessing AttributeID
	    $session = Shopware()->Session();
		$sql = "SELECT id FROM s_order_basket WHERE sessionID = ? ORDER BY id DESC LIMIT 1";
        $basketRow = Shopware()->Db()->fetchAll($sql, [Shopware()->Session()->get('sessionId')]);
		//$row[0]["id"];
		$sql = "SELECT id,basket_quantity_2,basket_quantity_3 FROM s_order_basket_attributes WHERE basketID = ? ORDER BY id DESC LIMIT 1";
        $attributesRow = Shopware()->Db()->fetchAll($sql, [$basketRow[0]["id"]]);
		$attributeID = $attributesRow[0]['id'];
		
		/*$orderNumber = $request->getParam('sAdd');
		$articleObj = Shopware()->Modules()->Articles()->sGetProductByOrdernumber($orderNumber);
		$maxpurchase = $articleObj['maxpurchase'];*/
		
		/*if(isset($maxpurchase) && $request->getParam('sQuantity') > $maxpurchase)
		{
			$sessionID = Shopware()->Session()->get('sessionId');
			$basketID = $basketRow[0]["id"];
			$sql1 = "DELETE FROM s_order_basket WHERE sessionID ='$sessionID' AND id =$basketID";
			Shopware()->Db()->query($sql1);
			$sql2 = "DELETE FROM s_order_basket_attributes WHERE id = $attributeID";
			Shopware()->Db()->query($sql2);
		}*/
		/*else
		{*/
			
			//Debug 
			/*echo "<pre>";
			$result = \Doctrine\Common\Util\Debug::dump($request->getParams(), 2, true, false);
			print_r($result); 
			die();*/
	
			//Get Request Parameters
			$qty2 = $request->getParam('sQuantity2');
			$qty3 = $request->getParam('sQuantity3');
			/*if(isset($qty2) && $qty2 == 0)
			{
				$qty2 = NULL;
			}
			if(isset($qty3) && $qty3 == 0)
			{
				$qty3 = NULL;
			}*/
			if(isset($attributesRow[0]['basket_quantity_2']))
			{
				$qty2 = ($attributesRow[0]['basket_quantity_2'] + $qty2);
			}
			
			if(isset($attributesRow[0]['basket_quantity_3']))
			{
				$qty3 = ($attributesRow[0]['basket_quantity_3'] + $qty3);
			}
			

			//Adding future Quantity in basket attribute
			if(!is_null($qty2) && is_null($qty3))
			{
				$sql = "UPDATE s_order_basket_attributes
				SET 
				basket_quantity_2 = $qty2
				WHERE id = $attributeID";
			}
			elseif(!is_null($qty3) && is_null($qty2))
			{
				$sql = "UPDATE s_order_basket_attributes
				SET 
				basket_quantity_3 = $qty3
				WHERE id = $attributeID";
			}
			elseif(!is_null($qty3) && !is_null($qty2))
			{
				$sql = "UPDATE s_order_basket_attributes
				SET 
				basket_quantity_2 = $qty2,
				basket_quantity_3 = $qty3
				WHERE id = $attributeID";
			}
			
			if(isset($qty2) || isset($qty3))
			{
				$res = Shopware()->Db()->query($sql);
			}
			
				
		//}
			
		//Debug 
		/*echo "<pre>";
		$result = \Doctrine\Common\Util\Debug::dump($view, 2, true, false);
		print_r($result); 
		die();*/
    }
	
	public function onPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
		//$action = $args->getSubject()->Request()->getActionName();
		Shopware()->Front()->Response()->setHeader('Cache-Control', 'private, no-cache');
		$view = $args->getSubject()->View();
		$basket = $view->getAssign('sBasket');
		$basketData = $basket['content'];
		$newBasketData = [];

        $firstTotal = 0;
        $secondTotal = 0;
        $thirdTotal = 0;
        $date_2 = 0;
        $date_3 = 0;

		foreach($basketData as $data)
		{
			//$data["isf_here"] = "working 123";
			
			$sql = "SELECT basket_quantity_2,basket_quantity_3 FROM s_order_basket_attributes WHERE basketID = ? ORDER BY id DESC LIMIT 1";
			$rows = Shopware()->Db()->fetchAll($sql, [$data["id"]]);
			foreach($rows as $row)
			{
				$data["basket_quantity_2"] = $row["basket_quantity_2"];
				$data["basket_quantity_3"] = $row["basket_quantity_3"];
			}
			
			$newBasketData[] = $data;

            if($date_2 == 0)
            {
                if(isset($data['additional_details']['delivery_date_2']))
                    $date_2 = $data['additional_details']['delivery_date_2'];
            }

            if($date_3 == 0)
            {
                if(isset($data['additional_details']['delivery_date_3']))
                    $date_3 = $data['additional_details']['delivery_date_3'];
            }

            $firstPrice = ($data['quantity']-$data['basket_quantity_2'])-$data['basket_quantity_3'];
            $convertNumber = str_replace(',','.',$data['price']);
            $firstPrice = $firstPrice*$convertNumber;
            $firstTotal = $firstTotal+$firstPrice;

            if(isset($data['additional_details']['delivery_date_2']))
            {
                $convertNumber = str_replace(',','.',$data['price']);
                $secondPrice = $data['basket_quantity_2']*$convertNumber;
                $secondTotal = $secondTotal+$secondPrice;
            }

            if(isset($data['additional_details']['delivery_date_3']))
            {
                $convertNumber = str_replace(',','.',$data['price']);
                $thirdPrice = $data['basket_quantity_3']*$convertNumber;
                $thirdTotal = $thirdTotal+$thirdPrice;
            }
		}

        $CartSummery = [];
        $CartSummery['firstTotal'] = $firstTotal;
        $CartSummery['secondTotal'] = $secondTotal;
        $CartSummery['thirdTotal'] = $thirdTotal;
        $CartSummery['date_2'] = $date_2;
        $CartSummery['date_3'] = $date_3;

        $_SESSION['CartSummery'] = $CartSummery;
		
		$basketData = $newBasketData;
		$basket['content'] = $basketData; 
		
		$view->assign('sBasket', $basket);

        $_SESSION["BasketData"] = $basket;
		
		$view->addTemplateDir($this->pluginDirectory . '/Resources/views');
		
		//Debug 
		/*echo "<pre>";
		$result = \Doctrine\Common\Util\Debug::dump($basket['content'], 2, true, false);
		print_r($result); 
		die();*/
	}
	
	/**
     * @param \Enlight_Event_EventArgs $args
     * Handle order confirmation mail
     */
    public function onSendOrderMail(\Enlight_Event_EventArgs $args)
    {
		
		//Debug 
		
        $variables = $args->getReturn();

		$mainCartSummary = $_SESSION['CartSummery'];
        //echo "<pre>";
		//$resultargs = \Doctrine\Common\Util\Debug::dump($_SESSION['CartSummery']);
		//$result = \Doctrine\Common\Util\Debug::dump($mainCartSummary, 2, true, false);
		$resultfirst = $mainCartSummary['firstTotal'];
		$resultsecond = $mainCartSummary['secondTotal'];
		$resultthird = $mainCartSummary['thirdTotal'];
		$date_2 = $mainCartSummary['date_2'];
		$date_3 = $mainCartSummary['date_3'];
		 
		foreach($variables['sOrderDetails'] as $orderIndex => $orderDetails) {
			
				$ordernumber = $orderDetails['ordernumber'];
              
        }
		$cartDetails = ["firstTotal" => $resultfirst, "secondTotal" => $resultsecond, "thirdTotal" => $resultthird, "date_2" => $date_2, "date_3" => $date_3];
		$variables['sOrderDetails']['futureDeliveryTotal'] = $cartDetails;

        $ordersRepo  = Shopware()->Models()->getRepository( \Shopware\Models\Order\Detail::class );

        $orderDetails = $ordersRepo->findBy( array( 'number' => $variables['ordernumber'] ) );

        $basket = $_SESSION["BasketData"];

        foreach($basket['content'] as $key => $basketRow)
        {
            $orderDetails = Shopware()->Db()->fetchAll("select id,quantity from s_order_details where ordernumber = '".$variables['ordernumber']."' and articleordernumber = '".$basketRow['ordernumber']."'");

            Shopware()->Db()->query("update s_order_details_attributes set order_quantity_2 = '".$basketRow['basket_quantity_2']."', order_quantity_3 = '".$basketRow['basket_quantity_3']."' where detailID = '".$orderDetails[0]['id']."'");
            Shopware()->Db()->query("update s_order_details set quantity = '".($orderDetails[0]['quantity']-$basketRow['basket_quantity_2']-$basketRow['basket_quantity_3'])."'  where ordernumber = '".$variables['ordernumber']."' and articleordernumber = '".$basketRow['ordernumber']."'");
            if($basketRow['basket_quantity_2']+$basketRow['basket_quantity_3'] > 1)
            	Shopware()->Db()->query("update s_order set paymentID = '4'  where ordernumber = '".$variables['ordernumber']."'");

        }
		//echo sizeof($basket['content']);
        $args->setReturn($variables);
    }
	
	public function onChangeQuantityAction(Enlight_Hook_HookArgs $args)
	{
		$controller = $args->getSubject();
        $request = $controller->Request();
		
		//Get Request Parameters
		$qty = $request->getParam('sQuantity');
		$qty2 = $request->getParam('sQuantity2');
		$qty3 = $request->getParam('sQuantity3');
		
		
		$basketID = $request->getParam('sArticle');
		$session = Shopware()->Session();
		$sessionID = Shopware()->Session()->get('sessionId');
		
		$sql = "SELECT id,ordernumber,quantity FROM s_order_basket WHERE sessionID ='$sessionID' AND id =$basketID";
		$basketRow = Shopware()->Db()->fetchAll($sql);
		
		$sql = "SELECT id,basket_quantity_2,basket_quantity_3 FROM s_order_basket_attributes WHERE basketID =$basketID";
        $attributesRow = Shopware()->Db()->fetchAll($sql);
		$attributeID = $attributesRow[0]['id'];
		
		$orderNumber = $basketRow[0]['ordernumber'];
		$articleObj = Shopware()->Modules()->Articles()->sGetProductByOrdernumber($orderNumber);
		$maxpurchase = $articleObj['maxpurchase'];
		
		/*Total Request Quantity*/
		$TotalQuantity = $request->getParam('sQuantity');
		if(!is_null($qty2) && is_null($qty3))
		{
			$TotalQuantity = ($qty+$qty2);
		}
		elseif(!is_null($qty3) && is_null($qty2))
		{
			$TotalQuantity = ($qty+$qty3);
		}
		elseif(!is_null($qty3) && !is_null($qty2))
		{
			$TotalQuantity = ($qty+$qty2+$qty3);
		}
		
		if(isset($maxpurchase) && $TotalQuantity > $maxpurchase)
		{
			//Adding future Quantity in basket attribute
			$oldQuantity = $basketRow[0]['quantity'];
			$oldQuantity_2 = $basketRow[0]['basket_quantity_2'];
			$oldQuantity_3 = $basketRow[0]['basket_quantity_3'];
			$request->setParam('sQuantity',$oldQuantity);
			$request->setParam('sQuantity2',$oldQuantity_2);
			$request->setParam('sQuantity3',$oldQuantity_3);
			
		}
		else
		{
			
			//Adding future Quantity in basket attribute
			if(!is_null($qty2) && is_null($qty3))
			{
				$quantity = ($qty+$qty2);
				$request->setParam('sQuantity',$quantity);
			}
			elseif(!is_null($qty3) && is_null($qty2))
			{
				$quantity = ($qty+$qty3);
				$request->setParam('sQuantity',$quantity);
			}
			elseif(!is_null($qty3) && !is_null($qty2))
			{
				$quantity = ($qty+$qty2+$qty3);
				$request->setParam('sQuantity',$quantity);
			}
			
		}
		
		//Debug 
		/*echo "<pre>";
		$result = \Doctrine\Common\Util\Debug::dump($request->getParams(), 2, true, false);
		print_r($result); 
		die();*/
	}
	
	
	
	public function onChangeQuantityActionAfter(Enlight_Hook_HookArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();
		//Show All Parameters
		//$request->getParams()
		
		//Debug 
		/*echo "<pre>";
		$result = \Doctrine\Common\Util\Debug::dump($request->getParams(), 2, true, false);
		print_r($result); 
		die();*/
		
		$basketID = $request->getParam('sArticle');
		$session = Shopware()->Session();
		$sessionID = Shopware()->Session()->get('sessionId');
		
		$sql = "SELECT id,ordernumber FROM s_order_basket WHERE sessionID ='$sessionID' AND id =$basketID";
		$basketRow = Shopware()->Db()->fetchAll($sql);
		
		$sql = "SELECT id,basket_quantity_2,basket_quantity_3 FROM s_order_basket_attributes WHERE basketID =$basketID";
        $attributesRow = Shopware()->Db()->fetchAll($sql);
		$attributeID = $attributesRow[0]['id'];
		
		$orderNumber = $basketRow[0]['ordernumber'];
		$articleObj = Shopware()->Modules()->Articles()->sGetProductByOrdernumber($orderNumber);
		$maxpurchase = $articleObj['maxpurchase'];
		
		/*if(isset($maxpurchase) && $request->getParam('sQuantity') > $maxpurchase)
		{
			
		}
		else
		{*/
			//Get Request Parameters
			$qty2 = $request->getParam('sQuantity2');
			$qty3 = $request->getParam('sQuantity3');
			
			if(!isset($qty2))
			{
				$qty2 = 0;
			}
			if(!isset($qty3))
			{
				$qty3 = 0;
			}
			
			//Adding future Quantity in basket attribute
			if(!is_null($qty2) && is_null($qty3))
			{
				$sql = "UPDATE s_order_basket_attributes
				SET 
				basket_quantity_2 = $qty2
				WHERE id = $attributeID";
			}
			elseif(!is_null($qty3) && is_null($qty2))
			{
				$sql = "UPDATE s_order_basket_attributes
				SET 
				basket_quantity_3 = $qty3
				WHERE id = $attributeID";
			}
			elseif(!is_null($qty3) && !is_null($qty2))
			{
				$sql = "UPDATE s_order_basket_attributes
				SET 
				basket_quantity_2 = $qty2,
				basket_quantity_3 = $qty3
				WHERE id = $attributeID";
			}
			
			if(isset($qty2) || isset($qty3))
			{
				$res = Shopware()->Db()->query($sql);
			}
			
		//}
		
    }
	
	
	
	public function onFrontendListing(Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();
		$view->addTemplateDir($this->pluginDirectory . '/Resources/views');
    }
	
	
	
	public function ongetstockinfo(Enlight_Hook_HookArgs $args)
    {
		
		$qtyCart = $args->get('quantity');
        $orderNumber = $args->get('orderNumber');
        $articleObj = Shopware()->Modules()->Articles()->sGetProductByOrdernumber($orderNumber);
		$maxpurchase = $articleObj['maxpurchase'];
		
		$controller = $args->getSubject();
        $request = $controller->Request();
		$qty = $request->getParam('sQuantity');
		if($qty > $maxpurchase)
		{
			$args->setReturn(Shopware()->Snippets()->getNamespace('frontend')->get('CheckoutArticleNoStock',
			'Unfortunately we can not deliver the desired product in sufficient quantity', true));
		}
		else
		{
			$args->getSubject()->executeParent(
				$args->getMethod(),
				$args->getArgs()
			);
		}
		
    }


	
	public function beforeSaveOrder(Enlight_Hook_HookArgs $args)
	{
		
		$controller = $args->getSubject();
		$basketData = $controller->sBasketData['content'];
		$basket_quantity = [];
		foreach ($basketData as $basketRow) 
		{
			$id  = $basketRow['id'];
			$totalQty = $basketRow['quantity'];
			$sql = "SELECT basket_quantity_2,basket_quantity_3 FROM s_order_basket_attributes WHERE basketID = ?";
			$attributesRows = Shopware()->Db()->fetchAll($sql, [$id]);
			foreach ($attributesRows as $attributesRow) 
			{

				$attributesRow['basket_quantity_1'] = ($totalQty-$attributesRow['basket_quantity_2'])-$attributesRow['basket_quantity_3'];
				$basket_quantity["$id"] = $attributesRow;
			}
			
		}
		//Debug 
		/*echo "<pre>";
		$result = \Doctrine\Common\Util\Debug::dump($basket_quantity, 2, true, false);
		print_r($result); 
		die();*/
		
		
		$_SESSION['basketQty'] = $basket_quantity;

		$CartSummery = [];
		$CartSummery['firstTotal'] = $_REQUEST['firstTotal'];
		$CartSummery['secondTotal'] = $_REQUEST['secondTotal'];
		$CartSummery['thirdTotal'] = $_REQUEST['thirdTotal'];
		$CartSummery['date_2'] = $_REQUEST['date_2'];
		$CartSummery['date_3'] = $_REQUEST['date_3'];
		
		//$_SESSION['CartSummery'] = $CartSummery;
		
        $basket = $_SESSION["BasketData"];


	}

    /**
     * @param \Shopware_Components_Cron_CronJob $job
     *
     * @return bool|string
     * @throws \Exception
     */
    public function splitOrders(\Shopware_Components_Cron_CronJob $job)
    {
        $orders = Shopware()->Db()->fetchAll("select o.*, ot.neti_store_id, ot.neti_pickup_datetime, ot.repertus_channel, ot.tonur_no_invoice, ot.siexport, ot.deliverydate_date from s_order o join s_order_attributes ot on o.id = ot.orderID where (ot.order_splitted IS NULL or ot.order_splitted < 1) and CHAR_LENGTH(o.ordernumber) > 2 limit 0,1000");
        // Get ordernumber
        

        $manger = new \Shopware\Components\Api\Manager();
        $resource = $manger->getResource('Order');

        // Get ordernumber
        $numberRepository = Shopware()->Models()->getRepository(\Shopware\Models\Order\Number::class);

        foreach($orders as $order)
        {
        	$details = Shopware()->Db()->fetchAll("select d.*, dt.* from s_order_details d join s_order_details_attributes dt on d.id = dt.detailID where d.orderID=".$order['id']);

        	$billing = Shopware()->Db()->fetchAll("select * from s_order_billingaddress where orderID = ".$order['id']);

        	$shipping = Shopware()->Db()->fetchAll("select * from s_order_shippingaddress where orderID = ".$order['id']);

        	if(sizeof($details)==0)
                {
                    Shopware()->Db()->query("update s_order_attributes set order_splitted=1 where orderID = ".$order['id']);
                    continue;
                }

            $split = false;
            $reduction = 0;

            $tax_rate = 1-round($order['invoice_amount_net']/$order['invoice_amount'],2);
            echo $tax_rate;

            for($i=2;$i<4;$i++)
            {
                $total = 0;
                $order_total = 0;


                $termin = [];
                $new_order_detail = [];

                foreach($details as $detail)
                {

                    if(is_numeric($detail['order_quantity_'.$i]) && $detail['order_quantity_'.$i]>0 && !is_null($detail['order_quantity_'.$i]) && !empty($detail['order_quantity_'.$i]))
                        $split = true;
                    else
                        continue;

                    $detail['quantity'] = $detail['order_quantity_'.$i];

                    $new_order_detail[] = [
                                    "articleId" => $detail['articleID'],
                                    'articleName' => $detail['name'],
                                    'quantity' => $detail['order_quantity_'.$i],
                                    'articleNumber' => $detail['articleordernumber'],
                                    'unit' => $detail['unit'],
                                    'price' => $detail['price'],
                                    "taxId" => $detail['taxID'],
                                    "taxRate" => $detail['tax_rate'],
                                    "statusId" => $detail['status'],
                                    'attribute' => []
                                ];

                    $termin = Shopware()->Db()->fetchRow("select delivery_date_2, delivery_date_3 from s_articles_attributes where articleID = '".$detail['articleID']."'");

                    $total+= $detail['order_quantity_'.$i]*$detail['price'];
                    $order_total += ($detail['order_quantity_2']+$detail['order_quantity_3']);
                    /*Shopware()->Db()->query("INSERT INTO s_order_details (orderID, ordernumber, articleID, articleordernumber, price, quantity, name, status, shipped, shippedgroup, releasedate, modus, esdarticle, taxID, tax_rate, config, ean, unit, pack_unit, articleDetailID) SELECT '".($next_id)."', '".$newOrderNumber."', articleID, articleordernumber, price, '".$detail['order_quantity_'.$i]."', name, status, shipped, shippedgroup, releasedate, modus, esdarticle, taxID, tax_rate, config, ean, unit, pack_unit, articleDetailID FROM s_order_details WHERE id=".$detail['id']);
                    Shopware()->Db()->query("INSERT INTO s_order_details_attributes (detailID, attribute1, attribute2, attribute3, attribute4, attribute5, attribute6, tonur_greeting_card_id, tonur_greeting_card_text, tonur_greeting_card_article_ordernumber, bundle_id, bundle_article_ordernumber, bundle_package_id, swag_promotion_item_discount, swag_promotion_direct_item_discount, swag_promotion_direct_promotions, order_quantity_2, order_quantity_3) SELECT LAST_INSERT_ID(), attribute1, attribute2, attribute3, attribute4, attribute5, attribute6, tonur_greeting_card_id, tonur_greeting_card_text, tonur_greeting_card_article_ordernumber, bundle_id, bundle_article_ordernumber, bundle_package_id, swag_promotion_item_discount, swag_promotion_direct_item_discount, swag_promotion_direct_promotions, order_quantity_2, order_quantity_3 FROM s_order_details_attributes WHERE detailID=".$detail['id']);*/
                }

                if($split==false || $total<1)
                    continue;

                $reduction += $total;

                /*Shopware()->Db()->query("INSERT INTO s_order (ordernumber, userID, invoice_amount, invoice_amount_net, invoice_shipping, invoice_shipping_net, invoice_shipping_tax_rate, ordertime, status, cleared, paymentID, transactionID, comment, customercomment, internalcomment, net, taxfree, partnerID, temporaryID, referer, cleareddate, trackingcode, language, dispatchID, currency, currencyFactor, subshopID, remote_addr, deviceType, is_proportional_calculation, changed, navconnect_id ) SELECT '".$newOrderNumber."', userID, (select sum(price*quantity) from s_order_details where orderID='".$next_id."'), (select sum(price*quantity) from s_order_details where orderID='".$next_id."'), invoice_shipping, invoice_shipping_net, invoice_shipping_tax_rate, ordertime, status, cleared, paymentID, transactionID, comment, customercomment, internalcomment, net, taxfree, partnerID, temporaryID, referer, cleareddate, trackingcode, language, dispatchID, currency, currencyFactor, subshopID, remote_addr, deviceType, is_proportional_calculation, changed, navconnect_id FROM s_order WHERE id=".$order['id']);
                Shopware()->Db()->query("INSERT INTO s_order_attributes (orderID, attribute1, attribute2, attribute3, attribute4, attribute5, attribute6, neti_store_id, neti_pickup_datetime, repertus_channel, tonur_no_invoice, siexport, deliverydate_date, order_splitted) SELECT '".($next_id)."' , attribute1, attribute2, attribute3, attribute4, attribute5, attribute6, neti_store_id, neti_pickup_datetime, repertus_channel, tonur_no_invoice, siexport, '".$termin['delivery_date_'.$i]."', '1' FROM s_order_attributes WHERE orderID=".$order['id']);

                Shopware()->Db()->query("INSERT INTO s_order_billingaddress (userID, orderID, company, department, salutation, customernumber, firstname, lastname, street, zipcode, city, phone, countryID, stateID, ustid, additional_address_line1, additional_address_line2, title) SELECT userID, '".($next_id)."' , company, department, salutation, customernumber, firstname, lastname, street, zipcode, city, phone, countryID, stateID, ustid, additional_address_line1, additional_address_line2, title FROM s_order_billingaddress WHERE orderID=".$order['id']);
                Shopware()->Db()->query("INSERT INTO s_order_billingaddress_attributes (billingID, text1, text2, text3, text4, text5, text6) SELECT LAST_INSERT_ID() , text1, text2, text3, text4, text5, text6 FROM s_order_billingaddress_attributes WHERE billingID=(SELECT id FROM s_order_billingaddress WHERE orderID=".$order['id'].")");

                Shopware()->Db()->query("INSERT INTO s_order_shippingaddress (userID, orderID, company, department, salutation, firstname, lastname, street, zipcode, city, phone, countryID, stateID, additional_address_line1, additional_address_line2, title) SELECT userID, '".($next_id)."' , company, department, salutation, firstname, lastname, street, zipcode, city, phone, countryID, stateID, additional_address_line1, additional_address_line2, title FROM s_order_shippingaddress WHERE orderID=".$order['id']);
                Shopware()->Db()->query("INSERT INTO s_order_shippingaddress_attributes (shippingID, text1, text2, text3, text4, text5, text6) SELECT LAST_INSERT_ID()  , text1, text2, text3, text4, text5, text6 FROM s_order_shippingaddress_attributes WHERE shippingID=(SELECT id FROM s_order_shippingaddress WHERE orderID=".$order['id'].")");*/
            
                $numberModel = $numberRepository->findOneBy(['name' => 'invoice']);

                $newOrderNumber = $numberModel->getNumber() + 1;

                // Set new ordernumber
                $numberModel->setNumber($newOrderNumber);
                Shopware()->Models()->persist($numberModel);

                echo ' - '.$termin['delivery_date_'.$i];

                $data = [
                                    "customerId" => $order['userID'],
                                    "number" => $newOrderNumber,
                                    "paymentId" => 4,
                                    "dispatchId" => $order['dispatchID'],
                                    "partnerId" => "",
                                    "shopId" => $order['subshopID'],
                                    "invoiceAmount" => $total,
                                    "invoiceAmountNet" => round($total-($total*$tax_rate),2),
                                    "invoiceShipping" => 0,
                                    "invoiceShippingNet" => 0,
                                    "orderTime" => $order['ordertime'],
                                    "net" => 0,
                                    "taxFree" => 0,
                                    "languageIso" => "1",
                                    "currency" => $order["currency"],
                                    "currencyFactor" => $order['currencyFactor'],
                                    "remoteAddress" => $order['remote_addr'],
                                    "details" => $new_order_detail,
                                    "attribute" => [
						                                'neti_store_id' => $order['neti_store_id'],
						                                'neti_pickup_datetime' => $order['neti_pickup_datetime'],
						                                'repertus_channel' => $order['repertus_channel'],
						                                'tonur_no_invoice' => $order['tonur_no_invoice'],
						                                'siexport' => $order['siexport'],
						                                'deliverydate_date' => $termin['delivery_date_'.$i],
						                                'order_splitted' => '1'
						                            ],
                                    "documents" => [],
                                    "billing" => [
                                        "customerId" => $billing[0]['userID'],
                                        "countryId" => $billing[0]['countryID'],
                                        "stateId" => $billing[0]['stateID'],
                                        "company" => $billing[0]['company'],
                                        "department" => $billing[0]['department'],
                                        "salutation" => $billing[0]['salutation'],
                                        "firstName" => $billing[0]['firstname'],
                                        "lastName" => $billing[0]['lastname'],
                                        "street" => $billing[0]['street'],
                                        "zipCode" => $billing[0]['zipcode'],
                                        "city" => $billing[0]['city'],
                                    ],
                                    "shipping" => [
                                        "customerId" => $shipping[0]['userID'],
                                        "countryId" => $shipping[0]['countryID'],
                                        "stateId" => $shipping[0]['stateID'],
                                        "company" => $shipping[0]['company'],
                                        "department" => $shipping[0]['department'],
                                        "salutation" => $shipping[0]['salutation'],
                                        "firstName" => $shipping[0]['firstname'],
                                        "lastName" => $shipping[0]['lastname'],
                                        "street" => $shipping[0]['street'],
                                        "zipCode" => $shipping[0]['zipcode'],
                                        "city" => $shipping[0]['city'],
                                    ],
                                    "paymentStatusId" => 17,
                                    "orderStatusId" => 0
                                ];

                                $new_order = $resource->create($data);
                                Shopware()->Models()->flush();

                                Shopware()->Db()->query("update s_order_attributes set order_splitted=1, deliverydate_date = '".$termin['delivery_date_'.$i]."', neti_store_id = '".$order['neti_store_id']."', neti_pickup_datetime = '".$order['neti_pickup_datetime']."', repertus_channel = '".$order['repertus_channel']."', tonur_no_invoice = '".$order['tonur_no_invoice']."', siexport = '".$order['siexport']."'  where orderID = ".$new_order->getId());

            }

            Shopware()->Db()->query("update s_order_attributes set order_splitted=1 where orderID = ".$order['id']);

            if($split==true)
                Shopware()->Db()->query("update s_order set invoice_amount_net = '".round(($order['invoice_amount']-$reduction)-(($order['invoice_amount']-$reduction)*$tax_rate),2)."', invoice_amount = '".round($order['invoice_amount']-$reduction,2)."' where id = ".$order['id']);

        }

        Shopware()->Models()->flush();
    }
	
}
