<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ShopOrderGifts\Subscriber;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Enlight_Hook_HookArgs;
use Shopware\Components\DependencyInjection\Container as DIContainer;
use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Theme\LessDefinition;
use Shopware\Bundle\SearchBundle\Criteria;
use ShopOrderGifts\Components\OrderGift;
use Enlight_Template_Manager;
use sBasket;

class Frontend implements SubscriberInterface
{

     /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $pluginDir;

    /**
     * @var string
     */
    private $viewDir;

    /**
     * @var Enlight_Template_Manager
     */
    private $template;

    /**
     * @param $pluginDir
     * @param $viewDir
     * @param Enlight_Template_Manager $template
     */
    public function __construct(
        DIContainer $container,
        $pluginDir,
        $viewDir,
        Enlight_Template_Manager $template
    ) {
        $this->container = $container;
        $this->pluginDir = $pluginDir;
        $this->viewDir = $viewDir;
        $this->template = $template;
    }
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch'                     => 'onPostDispatchCheckGift',
			'Theme_Compiler_Collect_Plugin_Javascript'                  => 'addJsFiles',
			'Theme_Compiler_Collect_Plugin_Css'                         => 'addCssFiles',
            'Enlight_Controller_Action_PostDispatch_Frontend_Detail'    => 'getGiftByArticleId',
            'sBasket::sAddArticle::after'                               => 'addArticle',
            'sBasket::sDeleteArticle::after'                            => 'deleteArticle',
			'Shopware_Controllers_Frontend_Checkout::changeQuantityAction::after' => 'onChangeQuantityAfter'
        ];
    }

    public function onPostDispatchCheckGift(\Enlight_Event_EventArgs $args)
    {
        $subject    = $args->getSubject();
        $view       = $args->getSubject()->View();
        $request    = $subject->Request();

        if($request->getModuleName() != 'backend' && $request->getModuleName() != 'api') {
            $subject->View()->addTemplateDir(dirname(__DIR__) . '/Resources/views');
    
            $gift_conditions = $this->gift_conditions();
            $this->check_article_gifts($gift_conditions, $view);
        }
    }

	public function addJsFiles(Enlight_Event_EventArgs $args)
    {
        $jsFiles = array(
            $this->pluginDir.'/Resources/views/frontend/_public/src/js/owl.carousel.min.js',
            $this->pluginDir.'/Resources/views/frontend/_public/src/js/gifts.js'
        );
        return new ArrayCollection($jsFiles);
    }

	public function addCssFiles(Enlight_Event_EventArgs $args)
    {
        $cssFiles = array(
            $this->pluginDir.'/Resources/views/frontend/_public/src/js/owl.carousel.min.css'
        );
        return new ArrayCollection($cssFiles);
    }

    public function deleteArticle(Enlight_Hook_HookArgs $args){

        $sessionID = Shopware()->Session()->get('sessionId');
        $db = Shopware()->Db();
		$BasketId = $args->get('id');
        $result = Shopware()->Db()->fetchAll("select quantity,articleID From s_order_basket where id = $BasketId");
		$articleID = $result[0]['articleID'];

        $gift_conditions = $this->gift_conditions();
        $this->check_article_gifts($gift_conditions,'','delete');

        Shopware()->Modules()->Basket()->sRefreshBasket();
    }

	public function getGiftOrderNumberByArticleIdAndQuantity($articleId,$currentCartQuantity,$include = true)
	{
		$db = Shopware()->Db();
        $sessionID = Shopware()->Session()->get('sessionId');
        $giftDataForCurrentArticle = $this->gift_conditions($articleId);

        $giftsOrderNumbers = [];

		if(count($giftDataForCurrentArticle) > 0)
		{
             $giftID = [];
             $giftDataArticles = [];
             $finalArray = [];
             foreach($giftDataForCurrentArticle as $giftId=>$DataGift)
             {
                 $gift_articles = implode(',',$DataGift['articles']);

                 $articles = $db->fetchAll(
                    'SELECT sum(b.quantity*(if(a.attr34=1,a.attr30,1))) as qty
                        FROM s_order_basket b
                        LEFT JOIN s_articles_details d
                          ON d.ordernumber = b.ordernumber
                          AND d.articleID = b.articleID
                        LEFT JOIN s_articles_attributes a
                          ON a.articledetailsID = d.id
                        WHERE b.sessionID = ?
                          AND b.modus = 0
                          AND b.articleID IN (?)',
                     [$sessionID,$gift_articles]
                 );

                 $currentCartQuantity = $articles[0]['qty'];
                $quantity = $DataGift["selection_limit"];

                    $finalArray[$giftId] = [
                        'quantity_from' =>  $DataGift['qty_from'],
                        'quantity_to' => $DataGift['qty_to'],
                    ];

                    $giftID[] = $giftId;

                    $sql = "SELECT a.giftId, b.id , b.name , c.ordernumber FROM s_plugin_order_gifts_articles a inner join s_articles b on a.articleId = b.id inner join s_articles_details as c on b.id = c.articleID where giftId IN (".$DataGift['gifts'].") AND c.instock > $quantity";
                    $giftDatas = $db->fetchAll($sql);

                    foreach($giftDatas as $giftData)
                    {
                        $giftDataArticles[] = $giftData;
                    }
                }

				if(count($giftDataArticles))
				{
					foreach($giftDataArticles as &$articleData)
					{
                        if($finalArray[$articleData['giftId']])
                        {
                            $articleData['quantity'] = $finalArray[$articleData['giftId']];
                            $giftImages = Shopware()->Modules()->Articles()->getArticleListingCover($articleData['id']);
                            $articleData['Image'] = $giftImages['src'];

                            $qf = (int)$articleData['quantity']['quantity_from'];
                            $qt = (int)$articleData['quantity']['quantity_to'];

                            if(isset($currentCartQuantity) && $include==true)
                            {
                                if($currentCartQuantity >= $qf && $currentCartQuantity <= $qt)
                                {
                                    $giftsOrderNumbers[] = $articleData['ordernumber'];
                                }
                            }else
                            {
                                if($currentCartQuantity < $qf && $currentCartQuantity > $qt)
                                {
                                    $giftsOrderNumbers[] = $articleData['ordernumber'];
                                }
                            }
                        }
					}
				}
			}
        return $giftsOrderNumbers;
	}
	
		
    public function addArticle(Enlight_Hook_HookArgs $args)
    {
        $session = Shopware()->Session();
        if (isset($_REQUEST['giftArticle']))
        {
			$orderNumber = $_REQUEST['sAdd'];
			$sessionID   = Shopware()->Session()->get('sessionId');
			$sql         = "SELECT id,quantity,modus FROM s_order_basket WHERE sessionID = ? and ordernumber = ?";
			$basketRow   = Shopware()->Db()->fetchAll($sql, [$sessionID,$orderNumber]);

			if(isset($basketRow) && sizeof($basketRow))
			{
				if($basketRow[0]["modus"] == 5)
				{
					$qty = $basketRow[0]["quantity"] + 1;
				}
				else
				{
					$qty = $basketRow[0]["quantity"];
				}
				
				$id = $basketRow[0]["id"];

				$sql = "UPDATE s_order_basket
					SET 
					quantity = $qty,
					price = '0',
					netprice = '0',
					modus = '5'
					WHERE id = $id";
					
				Shopware()->Db()->query($sql);
                Shopware()->Db()->query("update s_order_basket_attributes set gift_article='".$_REQUEST['giftParentOrderNumber']."', gift_article_qty_from='".$_REQUEST['quantity_from']."', gift_article_qty_to='".$_REQUEST['quantity_to']."' where basketID='".$id."'");
			}
			
			if (isset($_REQUEST['giftParentOrderNumber']))
			{
				$giftParentOrderNumber = $_REQUEST['giftParentOrderNumber'];
				$sql = "UPDATE s_order_basket_attributes
				SET 
				attribute2 = '$giftParentOrderNumber'
				WHERE basketID = $id";
				Shopware()->Db()->query($sql);
			}

            unset($_REQUEST['giftArticle']);
            Shopware()->Modules()->Basket()->sRefreshBasket();
        }
		else
		{
			$session = Shopware()->Session();
			$db = Shopware()->Db();
			$sessionID = Shopware()->Session()->get('sessionId');
			$orderNumber = $_REQUEST['sAdd'];

            $gift_conditions = $this->gift_conditions();
            $this->check_article_gifts($gift_conditions);

		}
    }

    public function sInsertOrderGift($mainId, $ordernumber=0, $giftId=0, $tax='7.7' ,$discount = 0, $discountNet = 0) {
        $session = Shopware()->Session();
        $db = Shopware()->Db();
        $userID = $session->offsetExists('sUserID') ? $session->offsetGet('sUserID') : 0;
        $articleID = 0;
        $discountName = Shopware()->Snippets()
                ->getNamespace('backend/static/order_gift_discounts')
                ->get('gift_discount_name');

        if(empty($ordernumber)) {
            $ordernumber = 'Discount';
        } else {
            $articleID = Shopware()->Modules()->Articles()->sGetArticleIdByOrderNumber($ordernumber);
            $discountName = Shopware()->Modules()->Articles()->sGetArticleNameByOrderNumber($ordernumber);
        }

        $db->insert(
            's_order_basket',
            array(
                'sessionID' => $session->get('sessionId'),
                'userID' => $userID,
                'articlename' => $discountName,
                'articleID' => $articleID,
                'ordernumber' => 'gift-'.$mainId,
                'quantity' => 1,
                'price' => $discount,
                'netprice' => $discountNet,
                'tax_rate' => $tax,
                'datum' => date("Y-m-d H:i:s"),
                'modus' => 5,
                'currencyFactor' => Shopware()->System()->sCurrency["factor"]
            )
        );

        $basketId = $db->lastInsertId();
        return $basketId;
    }

	public function unique_array($my_array, $key) { 
		$result = array(); 
		$i = 0; 
		$key_array = array(); 
		
		foreach($my_array as $val) { 
			if (!in_array($val[$key], $key_array)) { 
				$key_array[$i] = $val[$key]; 
				$result[$i] = $val; 
			} 
			$i++; 
		} 
		return $result; 
	}  


    public function getGiftByArticleId(\Enlight_Event_EventArgs $args)
	{

        $db = Shopware()->Db();
        $view = $args->getSubject()->View();
        $articleId = $view->sArticle['articleID'];
        $orderNumber = $view->sArticle['ordernumber'];
        $attr23 = $view->sArticle['attr23'];

        $gift_conditions = $this->gift_conditions($articleId);
        $this->check_article_gifts($gift_conditions,$view);

    }
	
	public function onChangeQuantityAfter(Enlight_Hook_HookArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();
        $db = Shopware()->Db();
		$basketID = $request->getParam('sArticle');

        $gift_conditions = $this->gift_conditions();
        $this->check_article_gifts($gift_conditions,'','delete');

	}

    private function gift_conditions($article_id = '')
    {
        $db = Shopware()->Db();

        $condition = '';
        if($article_id != '' && is_numeric($article_id))
            $condition = " and oa.articleId = '".$article_id."'";

        $results = $db->fetchAll("
                                SELECT GROUP_CONCAT(DISTINCT(oa.articleId)) AS articles
                                     , GROUP_CONCAT(DISTINCT(oga.articleId)) AS gifts
                                     , GROUP_CONCAT(DISTINCT(ops.productStreamId)) AS streamIds
                                     , og.* 
                                FROM `s_plugin_order_gifts` og
                                    LEFT JOIN `s_plugin_order_articles` oa ON og.id = oa.giftId 
                                    LEFT JOIN `s_plugin_order_gifts_articles` oga ON og.id = oga.giftId
                                    LEFT JOIN `s_plugin_order_product_stream` ops ON og.id = ops.giftId
                                WHERE og.status = 1
                                ". $condition ."
                                GROUP BY og.id
        ");

        $gift_conditions = [];

        foreach($results as $result)
        {
            $id = $result['id'];
            $articles = $this->getProductIdsFromStream($result['articles'], $result['streamIds']);
            $gift_conditions[$id]['qty_to']          = $result['quantity_to'];
            $gift_conditions[$id]['qty_from']        = $result['quantity_from'];
            $gift_conditions[$id]['amt_to']          = $result['price_to'];
            $gift_conditions[$id]['amt_from']        = $result['price_from'];
            $gift_conditions[$id]['date_to']         = $result['date_to'];
            $gift_conditions[$id]['date_from']       = $result['date_from'];
            $gift_conditions[$id]['selection_limit'] = $result['quantity'];
            $gift_conditions[$id]['articles']        = $articles;
            $gift_conditions[$id]['gifts']           = $result['gifts'];
        }

        return $gift_conditions;
    }

    public function getProductIdsFromStream($originalArticles, $streamIds)
    {
        $db               = Shopware()->Db();
        $container        = $this->container;
        $articleIds       = array();

        if($originalArticles) {
            $originalArticles = explode(',', $originalArticles);
        } else {
            $originalArticles = array();
        }

        if(isset($streamIds) && $streamIds != '')
        {
            $streamIdArray = explode(',', $streamIds);
            foreach($streamIdArray as $streamId)
            {
                $conditions  = $db->fetchAll("SELECT conditions FROM `s_product_streams` WHERE id = ?", [$streamId])[0]['conditions'];
                $product_ids = array();

                if(isset($conditions) && !empty($conditions)) {
                    $streamRepo = $container->get('shopware_product_stream.repository');
                    $criteria   = new Criteria();
                    $conditions = json_decode($conditions, true);
                    $conditions = $streamRepo->unserialize($conditions);
                    foreach ($conditions as $condition) { 
                        $criteria->addCondition($condition);
                    }
                    $context = $container->get('shopware_storefront.context_service')->getShopContext();
                    $service = $container->get('shopware_search.product_number_search');
                    $result  = $service->search($criteria, $context);
                    foreach($result->getProducts() as $product) {
                        $product_ids[] = $product->getId();
                    }
                } else {
                    $results  = $db->fetchAll("SELECT article_id 
                                               FROM `s_product_streams_selection` 
                                               WHERE stream_id = ?", [$streamId]);
                                                
                    foreach($results as $result) {
                        $product_ids[] = $result['article_id']; 
                    }
                }     
                $articleIds = array_unique(array_merge($articleIds, $product_ids), SORT_REGULAR);
            }
        }

        $articleIds = array_unique(array_merge($articleIds, $originalArticles), SORT_REGULAR);
        $articleIds = implode(',', $articleIds);

        return $articleIds;
    }

    public function check_article_gifts($gift_conditions, $view = '', $action='')
    {
        $db = Shopware()->Db();
        $sessionID = Shopware()->Session()->get('sessionId');
        $sql = "SELECT id,quantity,modus,ordernumber FROM s_order_basket WHERE sessionID = ?";
        $basketRow = Shopware()->Db()->fetchAll($sql, [$sessionID]);
        $id = $basketRow[0]["id"];

        $this->remove_orphan_gifts();

        foreach($gift_conditions as $giftId => $condition)
        {
            $gift_articles = $condition['articles'];

            if(!empty($gift_articles) && $gift_articles != '') {
                $articles = $db->fetchAll(
                    "SELECT IFNULL(sum(b.quantity*(if(a.attr34=1,a.attr30,1))), 0) as qty
                        , IFNULL(sum(b.quantity * b.price), 0) as price
                            FROM s_order_basket b
                            LEFT JOIN s_articles_details d
                            ON d.ordernumber = b.ordernumber
                            AND d.articleID = b.articleID
                            LEFT JOIN s_articles_attributes a
                            ON a.articledetailsID = d.id
                            WHERE b.sessionID = ?
                            AND b.modus = 0
                            AND b.articleID IN (".$gift_articles.")",
                    [$sessionID]
                );
                
                $sql = "SELECT b.id , b.name , c.ordernumber FROM s_articles b inner join s_articles_details as c on b.id = c.articleID where b.id IN (".$condition['gifts'].") AND c.instock > ".$condition['selection_limit'];
                $giftDataArticles = $db->fetchAll($sql);

                if (count($articles) > 0 && $articles[0]['qty'] > 0) 
                {
                    $article['qty'] = $articles[0]['qty'];
                    $article['price'] = $articles[0]['price'];
                    if (count($giftDataArticles)) 
                    {
                        foreach ($giftDataArticles as &$articleData) {
                            $articleData['giftId'] = $giftId;
                            $articleData['quantity'] = ['quantity_from' => $condition['qty_from'], 'quantity_to' => $condition['qty_to']];
                            $articleData['price'] = ['price_from' => $condition['amt_from'], 'price_to' => $condition['amt_to']];
                            $giftImages = Shopware()->Modules()->Articles()->getArticleListingCover($articleData['id']);
                            $articleData['Image'] = $giftImages['src'];
                            $articleData['giftParentOrderNumber'] = $gift_articles;
                        }

                        $taken_gift = 0;
                        $giftDataArticlesFinal = [];

                        foreach($giftDataArticles as $giftDataArticle)
                        {
                            if(isset($articles[0]['qty']))
                            {
                                $qf = (int)$giftDataArticle['quantity']['quantity_from'];
                                $qt = (int)$giftDataArticle['quantity']['quantity_to'];

                                $price_from = (int)$giftDataArticle['price']['price_from'];
                                $price_to   = (int)$giftDataArticle['price']['price_to'];

                                if(($articles[0]['qty'] >= $qf && $articles[0]['qty'] <= $qt) || ($articles[0]['price'] >= $price_from && $articles[0]['price'] <= $price_to))
                                {
                                    $giftDataArticlesFinal[] = $giftDataArticle;
                                    $sql = "SELECT sum(quantity) as basketGiftQuantity FROM s_order_basket WHERE modus = 5 and ordernumber='".$giftDataArticle["ordernumber"]."' and sessionID='$sessionID'";

                                    $basketGiftQuantity = $db->fetchAll($sql);

                                    if(isset($basketGiftQuantity[0]['basketGiftQuantity']) && $basketGiftQuantity[0]['basketGiftQuantity']>0)
                                        $taken_gift += $basketGiftQuantity[0]['basketGiftQuantity'];
                                }
                            }
                        }

                        if(count($giftDataArticlesFinal) == 0)
                        {
                            foreach($giftDataArticles as $giftDataArticle) {
                                Shopware()->Db()->query("DELETE FROM s_order_basket WHERE articleID='".$giftDataArticle["id"]."' and sessionID='$sessionID'");
                            }
                        }
                    }
                }
            }
        }

        foreach($gift_conditions as $giftId => $condition)
        {
            $gift_articles = $condition['articles'];

            if(!empty($gift_articles) && $gift_articles != '') {
                $articles = $db->fetchAll(
                    "SELECT IFNULL(sum(b.quantity*(if(a.attr34=1,a.attr30,1))), 0) as qty
                        , IFNULL(sum(b.quantity * b.price), 0) as price
                            FROM s_order_basket b
                            LEFT JOIN s_articles_details d
                            ON d.ordernumber = b.ordernumber
                            AND d.articleID = b.articleID
                            LEFT JOIN s_articles_attributes a
                            ON a.articledetailsID = d.id
                            WHERE b.sessionID = ?
                            AND b.modus = 0
                            AND b.articleID IN (".$gift_articles.")",
                    [$sessionID]
                );
                
                $sql = "SELECT b.id , b.name , c.ordernumber FROM s_articles b inner join s_articles_details as c on b.id = c.articleID where b.id IN (".$condition['gifts'].") AND c.instock > ".$condition['selection_limit'];
                $giftDataArticles = $db->fetchAll($sql);

                if (count($articles) > 0 && $articles[0]['qty'] > 0) 
                {
                    $article['qty'] = $articles[0]['qty'];
                    $article['price'] = $articles[0]['price'];
                    if (count($giftDataArticles)) 
                    {
                        foreach ($giftDataArticles as &$articleData) {
                            $articleData['giftId'] = $giftId;
                            $articleData['quantity'] = ['quantity_from' => $condition['qty_from'], 'quantity_to' => $condition['qty_to']];
                            $articleData['price'] = ['price_from' => $condition['amt_from'], 'price_to' => $condition['amt_to']];
                            $giftImages = Shopware()->Modules()->Articles()->getArticleListingCover($articleData['id']);
                            $articleData['Image'] = $giftImages['src'];
                            $articleData['giftParentOrderNumber'] = $gift_articles;
                        }

                        $taken_gift = 0;
                        $giftDataArticlesFinal = [];

                        foreach($giftDataArticles as $giftDataArticle)
                        {
                            if(isset($articles[0]['qty']))
                            {
                                $qf = (int)$giftDataArticle['quantity']['quantity_from'];
                                $qt = (int)$giftDataArticle['quantity']['quantity_to'];

                                $price_from = (int)$giftDataArticle['price']['price_from'];
                                $price_to   = (int)$giftDataArticle['price']['price_to'];

                                if(($articles[0]['qty'] >= $qf && $articles[0]['qty'] <= $qt) || ($articles[0]['price'] >= $price_from && $articles[0]['price'] <= $price_to))
                                {
                                    $giftDataArticlesFinal[] = $giftDataArticle;
                                    $sql = "SELECT sum(quantity) as basketGiftQuantity FROM s_order_basket WHERE modus = 5 and ordernumber='".$giftDataArticle["ordernumber"]."' and sessionID='$sessionID'";

                                    $basketGiftQuantity = $db->fetchAll($sql);

                                    if(isset($basketGiftQuantity[0]['basketGiftQuantity']) && $basketGiftQuantity[0]['basketGiftQuantity']>0)
                                        $taken_gift += $basketGiftQuantity[0]['basketGiftQuantity'];
                                }
                            }
                        }

                        if(count($giftDataArticlesFinal) > 0)
                        {
                            $giftId = $giftDataArticlesFinal[0]['giftId'];
                            $gift_selection_limit = $condition['selection_limit'];

                            if(isset($article['qty']) && count($giftDataArticlesFinal) > 0 && $taken_gift < $gift_selection_limit)
                            {

                                if(sizeof($giftDataArticlesFinal)>1)
                                {
                                    if(isset($view) && !empty($view)) {

                                        $view->assign('article_gift', $giftDataArticlesFinal);
                                        $view->assign('taken_gift', $gift_selection_limit-$taken_gift);
                                        $view->assign('gift_selection_limit', $gift_selection_limit-$taken_gift);
                                        $view->assign('show_gifts_in_cart', '1');
                                        break;
                                    }
                                }//if no selection option then add missing gifts to cart
                                else
                                {
                                    $_REQUEST['giftArticle'] = 1;
                                    $_REQUEST['sAdd'] = $giftDataArticlesFinal[0]["ordernumber"];
                                    $_REQUEST['sQuantity'] = $gift_selection_limit-$taken_gift;

                                    Shopware()->Modules()->Basket()->sAddArticle($giftDataArticlesFinal[0]["ordernumber"], $gift_selection_limit-$taken_gift);
                                    Shopware()->Modules()->Basket()->sRefreshBasket();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function remove_orphan_gifts()
    {
        $sessionID = Shopware()->Session()->get('sessionId');
        $db = Shopware()->Db();
        $sql = "SELECT ba.basketID, ba.gift_article, ba.gift_article_qty_from, ba.gift_article_qty_to FROM s_order_basket_attributes ba join s_order_basket b on ba.basketID = b.id WHERE b.modus=5 and ba.gift_article != '' and sessionID=?";

        $giftRows = Shopware()->Db()->fetchAll($sql,[$sessionID]);

        foreach($giftRows as $gift)
        {
            
            $basketcheck = $db->fetchAll(
                "SELECT COUNT(*) as total
                 FROM (SELECT IFNULL(sum(b.quantity*(if(a.attr34=1,a.attr30,1))), 0) as qty
                            , IFNULL(sum(b.quantity * b.price), 0) as price
                                FROM s_order_basket b
                                LEFT JOIN s_articles_details d
                                ON d.ordernumber = b.ordernumber
                                AND d.articleID = b.articleID
                                LEFT JOIN s_articles_attributes a
                                ON a.articledetailsID = d.id
                                WHERE b.sessionID = ?
                                AND b.modus = 0
                                AND b.articleID IN (".$gift['gift_article'].")) t
                WHERE t.qty >= ?
                and   t.qty <= ?",
                [$sessionID, $gift["gift_article_qty_from"], $gift["gift_article_qty_to"]]
            );
            
            if(!isset($basketcheck[0]["total"]) || $basketcheck[0]["total"] == 0)
            {
                $db->query("DELETE FROM s_order_basket WHERE id = ?", [$gift["basketID"]]);
                $db->query("DELETE FROM s_order_basket_attributes WHERE basketID = ?", [$gift["basketID"]]);
            }
        }
    }
	
}
