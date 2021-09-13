<?php

use Shopware\Components\CSRFWhitelistAware;
use ShopOrderGifts\Models\Gift;

class Shopware_Controllers_Backend_ShopOrderGifts extends Shopware_Controllers_Backend_ExtJs implements CSRFWhitelistAware
{
	/**
	 * Disable template engine for all actions
	 *
	 * @return void
	 */
	public function preDispatch()
	{
		$this->get('template')->addTemplateDir(__DIR__ . '/../../Resources/views/');
		if (!in_array($this->Request()->getActionName(), ['index', 'load'])) {
			$this->Front()->Plugins()->Json()->setRenderer(true);
		}
	}

	public function getRepository(){
		return Shopware()->Models()->getRepository('\ShopOrderGifts\Models\Gift');
	}

	/**
	 * @inheritdoc
	 */
	public function getWhitelistedCSRFActions()
	{
		return [
			'getGiftList'
		];
	}

	public function getGiftListAction() {
		try{
			$sLists = $this->sGetList();
			foreach ($sLists as $key => &$sList) {
				if(count($sList['giftArticles'])) {
					$sArticleIds = [];
					foreach ($sList['giftArticles'] as $key => &$article) {
						$sArticleIds[] = $article['id'];
					}

					$sDatas = Shopware()->Db()->fetchAll("SELECT articleID, ordernumber AS `number` FROM s_articles_details WHERE articleID IN (".implode(', ', $sArticleIds).") AND kind = 1 GROUP BY articleID ");
					foreach ($sDatas as $key => $sData) {
						$sArticleNumbers[$sData['articleID']] = $sData['number'];
					}
					foreach ($sList['giftArticles'] as $key => &$article) {
						$article['number'] = $sArticleNumbers[$article['id']];
					}
				}

				if(count($sList['articles'])) {
					$sArticleIds = [];
					foreach ($sList['articles'] as $key => &$article) {
						$sArticleIds[] = $article['id'];
					}

					$sDatas = Shopware()->Db()->fetchAll("SELECT articleID, ordernumber AS `number` FROM s_articles_details WHERE articleID IN (".implode(', ', $sArticleIds).") AND kind = 1 GROUP BY articleID ");
					foreach ($sDatas as $key => $sData) {
						$sArticleNumbers[$sData['articleID']] = $sData['number'];
					}
					foreach ($sList['articles'] as $key => &$article) {
						$article['number'] = $sArticleNumbers[$article['id']];
					}
				}

			}

			$totalCount = count($sLists);
			$this->View()->assign(array('success' => true, 'data' => $sLists, 'totalCount' => $totalCount));
		} catch (\Doctrine\ORM\ORMException $e) {
			$this->View()->assign(array('success' => false, 'errorMsg' => $e->getMessage()));
		}
	}

	public function getAvailableArticlesAction() {
		try {
			$search = $this->Request()->getParam('search', null);
			$filter = $this->Request()->getParam('filter', []);
			$searchWhere = '';
			if(!empty($search)) {
				$searchWhere = "AND (d.ordernumber LIKE '%".$search."%' OR a.name LIKE '%".$search."%')";
			}
			if(!empty($filter)) {
				$searchWhere .= "AND (d.ordernumber LIKE '%".$filter[0]['value']."%' OR a.name LIKE '%".$filter[0]['value']."%')";
			}

			$sql = "SELECT a.id, a.name, d.ordernumber AS `number`
					FROM s_articles AS a
					LEFT JOIN s_articles_details AS d ON d.articleID = a.id
					LEFT JOIN s_articles_categories AS c ON c.articleID = a.id
					LEFT JOIN s_categories AS cat ON c.categoryID = cat.id
					WHERE a.active = ? AND d.active = ? AND d.kind = ? AND c.articleID = a.id AND cat.path IS  NOT NULL $searchWhere
					GROUP BY a.id";
			$sLists = Shopware()->Db()->fetchAll($sql, [1, 1, 1]);

			$totalCount = count($sLists);
			$this->View()->assign(array('success' => true, 'data' => $sLists, 'totalCount' => $totalCount));
		} catch (\Doctrine\ORM\ORMException $e) {
			$this->View()->assign(array('success' => false, 'errorMsg' => $e->getMessage()));
		}
	}
	public function getAssignedArticlesAction() {
		try {
			$giftId = $this->Request()->getParam('giftId');
			$search = $this->Request()->getParam('search', null);
			if(empty($giftId)) {
				return $this->View()->assign(array('success' => false));
			}
			$searchWhere = '';
			if(!empty($search)) {
				$searchWhere = "AND (d.ordernumber LIKE '%".$search."%' OR a.name LIKE '%".$search."%')";
			}
			$sql = "SELECT a.id, a.name, d.ordernumber AS `number`
					FROM s_plugin_order_articles AS g
					LEFT JOIN s_articles AS a ON a.id = g.articleId
					LEFT JOIN s_articles_details AS d ON d.articleID = a.id
					WHERE g.giftId = ? AND d.kind = ? $searchWhere
					GROUP BY a.id";
			$sLists = Shopware()->Db()->fetchAll($sql, [$giftId, 1]);

			$totalCount = count($sLists);
			$this->View()->assign(array('success' => true, 'data' => $sLists, 'totalCount' => $totalCount));
		} catch (\Doctrine\ORM\ORMException $e) {
			$this->View()->assign(array('success' => false, 'errorMsg' => $e->getMessage()));
		}
	}

	public function getProductStreamAction() {
		try {
			$sql = "SELECT p.id, p.name FROM s_product_streams AS p";
			$sLists = Shopware()->Db()->fetchAll($sql, [$giftId, 1]);

			$totalCount = count($sLists);
			$this->View()->assign(array('success' => true, 'data' => $sLists, 'totalCount' => $totalCount));
		} catch (\Doctrine\ORM\ORMException $e) {
			$this->View()->assign(array('success' => false, 'errorMsg' => $e->getMessage()));
		}
	}

	public function createGiftAction() {
		try {
			$sName = $this->Request()->getParam('name');
			if(!empty($sName)) {
				Shopware()->Db()->query("INSERT INTO s_plugin_order_gifts (name) VALUES(?)", [$sName]);
				$id = Shopware()->Db()->lastInsertId();
			}
			$sLists = $this->sGetList($id);
			$totalCount = count($sLists);
			$this->View()->assign(array('success' => true, 'data' => $sLists, 'totalCount' => $totalCount));
		} catch (\Doctrine\ORM\ORMException $e) {
			$this->View()->assign(array('success' => false, 'errorMsg' => $e->getMessage()));
		}
	}

	private function sGetList($id=0) {
		$repository = $this->getRepository();
		$filter = [];
		if(!empty($id)) {
			$filter = array(array("property" => "id", "value" => $id));
		}
		$dataQuery = $repository->getBackendDetailQuery($filter, '');
		return $dataQuery->getArrayResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

	}

	public function updateGiftAction() {
		try {
			$sParams = $this->Request()->getParams();
			$giftId = $sParams['id'];

			// Save gift Details
			$this->sSaveGiftDetails($giftId, $sParams);

			// Save Gift Articles
			if(!empty($sParams['giftArticles'])) {
				$this->sSaveGiftArticles($giftId, $this->sConvertDataToIds($sParams['giftArticles']));
			}

			// Save Assigned Articles
			if(!empty($sParams['assignedArticles'])) {
				$this->sSaveAssignedArticles($giftId, $this->sConvertDataToIds($sParams['assignedArticles']));
			} else {
				$this->sSaveAssignedArticles($giftId, false);
			}

			// Save Assigned Categories
			if(!empty($sParams['categories'])) {
				$this->sSaveAssignedCategories($giftId, $this->sConvertDataToIds($sParams['categories']));
			}

			// Save Assigned Product-Stream
			if(!empty($sParams['productStream'])) {
				$this->sSaveAssignedProductStream($giftId, $this->sConvertDataToIds($sParams['productStream']));
			} else {
				$this->sSaveAssignedProductStream($giftId, false);
			}

			$sLists = $this->sGetList($giftId);

			$totalCount = count($sLists);
			$this->View()->assign(array('success' => true, 'data' => $sLists, 'totalCount' => $totalCount));
		} catch (\Doctrine\ORM\ORMException $e) {
			$this->View()->assign(array('success' => false, 'errorMsg' => $e->getMessage()));
		}
	}


	public function deleteGiftAction() {
		try {
			$sParams = $this->Request()->getParams();
			$giftId = $sParams['id'];

			$model = Shopware()->Models()->find('ShopOrderGifts\Models\Gift', $giftId);
            if ($model) {
	            Shopware()->Models()->remove($model);
	            Shopware()->Models()->flush();
            }

			$sLists = $this->sGetList($giftId);
			$totalCount = count($sLists);
			$this->View()->assign(array('success' => true, 'data' => $sLists, 'totalCount' => $totalCount));
		} catch (\Doctrine\ORM\ORMException $e) {
			$this->View()->assign(array('success' => false, 'errorMsg' => $e->getMessage()));
		}
	}

	private function sConvertDataToIds($sDatas) {
		$sDataIds = [];
		foreach ($sDatas as $key => $sData) {
			$sDataIds[$key] = $sData;
			if(isset($sData['id']) && !empty($sData['id'])) {
				$sDataIds[$key] = $sData['id'];
			}
		}

		return $sDataIds;
	}

	private function sSaveGiftDetails($giftId, $sValues) {
		$status = 0;
		if($sValues['status']) {
			$status = 1;
		}

		if(empty($sValues['dateFrom'])) {
			$dateFrom = NULL;
		} else {
			$dTitme = strtotime($sValues['dateFrom']);
			$dateFrom = date("Y.m.d H:i:s", mktime(00, 00, 00, date('m', $dTitme), date('d', $dTitme), date('Y', $dTitme)));
		}

		if(empty($sValues['dateTo'])) {
			$dateTo = NULL;
		} else {
			$dTitme = strtotime($sValues['dateTo']);
			$dateTo = date("Y.m.d H:i:s", mktime(00, 00, 00, date('m', $dTitme), date('d', $dTitme), date('Y', $dTitme)));
		}

		$sGiftDatas = array(
			'status' => $status,
			'name' => $sValues['name'],
			'quantity_from' => $sValues['quantityFrom'],
			'quantity_to' => $sValues['quantityTo'],
			'price_from' => $sValues['priceFrom'],
			'price_to' => $sValues['priceTo'],
			'date_from' => $dateFrom,
			'date_to' => $dateTo,
			'gift_type' => $sValues['giftType'],
			'percental' => $sValues['percental'],
			'value' => $sValues['value'],
			'quantity' => $sValues['quantity'],
			'number_redeem' => $sValues['numberRedeem'],
			'cumulative' => $sValues['cumulative']
		);

		Shopware()->Db()->update(
			's_plugin_order_gifts',
			$sGiftDatas,
			array('id = ?' => $giftId)
		);

		return true;
	}

	private function sSaveGiftArticles($giftId, $sArticles) {
		$db = Shopware()->Db();
		// Refresh assiged articles of perticular gift
		$db->query("DELETE FROM s_plugin_order_gifts_articles WHERE giftId = ?", [$giftId]);
		foreach ($sArticles as $key => $sArticle) {
			$db->query("INSERT INTO s_plugin_order_gifts_articles (giftId, articleId) VALUES( ?, ?)", [$giftId, $sArticle]);
		}

		return true;
	}

	private function sSaveAssignedArticles($giftId, $sArticles) {
		$db = Shopware()->Db();

		// Refresh assiged articles of perticular gift
		$db->query("DELETE FROM s_plugin_order_articles WHERE giftId = ?", [$giftId]);
		
		if($sArticles) {
			foreach ($sArticles as $key => $sArticle) {
				$db->query("INSERT INTO s_plugin_order_articles (giftId, articleId) VALUES( ?, ?)", [$giftId, $sArticle]);
			}
		}

		return true;
	}

	private function sSaveAssignedCategories($giftId, $sCategories) {
		$db = Shopware()->Db();

		// Refresh assiged Categories of perticular gift
		$db->query("DELETE FROM s_plugin_order_categories WHERE giftId = ?", [$giftId]);
		foreach ($sCategories as $key => $sCategory) {
			$db->query("INSERT INTO s_plugin_order_categories (giftId, categoryId) VALUES( ?, ?)", [$giftId, $sCategory]);
		}

		return true;
	}

	private function sSaveAssignedProductStream($giftId, $sProductStreams) {
		$db = Shopware()->Db();
		// Refresh assiged Categories of perticular gift
		$db->query("DELETE FROM s_plugin_order_product_stream WHERE giftId = ?", [$giftId]);

		if($sProductStreams) {
			foreach ($sProductStreams as $key => $sProductStream) {
				$db->query("INSERT INTO s_plugin_order_product_stream (giftId, productStreamId) VALUES( ?, ?)", [$giftId, $sProductStream]);
			}
		}

		return true;
	}

}
