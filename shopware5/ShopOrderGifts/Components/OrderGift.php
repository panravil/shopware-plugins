<?php

namespace ShopOrderGifts\Components;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle;

class OrderGift {

	private $connection;


	public function __construct(
		Connection $connection
	) {
		$this->connection = $connection;
	}

	public function getRepository(){
		return Shopware()->Models()->getRepository('\ShopOrderGifts\Models\Gift');
	}

	public function sGetOrderGift() {
		$repository = $this->getRepository();
		$filter = array(array("property" => "status", "value" => 1));
		$dataQuery = $repository->getBackendDetailQuery($filter, 'checkout');
		$sOrderGifts = $dataQuery->getArrayResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

		foreach ($sOrderGifts as $key => &$sOrderGift) {
            $sLangData = $this->sGetTranslation($sOrderGift['id']);
            if(!empty($sLangData) && !empty($sLangData['txtArtikel'])) {
                $sOrderGift['name'] = $sLangData['txtArtikel'];
            }

			// Update Gift Articles
			foreach ($sOrderGift['giftArticles'] as $sKey => &$giftArticle) {
				$sArticle = Shopware()->System()->sMODULES['sArticles']->sGetPromotionById("fix", 0, $giftArticle["id"]);
                if(empty($sArticle)) {
                    //unset($sOrderGift['giftArticles'][$sKey]);
                }
                $giftArticle["sArticle"] = $sArticle;
	        	$giftArticle["sVariants"] = $this->getVariantDetailsForPremiumArticles($giftArticle["id"], $giftArticle["main_detail_id"]);
			}

			// Update Articles
			$sArticles = [];
			foreach ($sOrderGift['articles'] as $key => $article) {
				$sArticles[$article['id']] = $article;
			}
			$sOrderGift['articles'] = $sArticles;

			// Update Categories
			$sCategories = [];
			foreach ($sOrderGift['categories'] as $key => $category) {
				$sCategories[$category['id']] = $category;
			}
			$sOrderGift['categories'] = $sCategories;

			// Update Product-stream
			foreach ($sOrderGift['productStream'] as $key => &$productStream) {
				$productStream['articles'] = $this->sGetProductStreamArticle($productStream['id']);
			}
		}

		return $sOrderGifts;
	}

    private function sGetTranslation($giftId) {
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
        $shopId = $contextService->getShopContext()->getShop()->getId();
        $sql = "SELECT objectdata FROM s_core_translations WHERE objecttype = ? AND objectkey = ? AND objectlanguage = ?";
        $sLangData = Shopware()->Db()->fetchOne($sql, ['article', $giftId, $shopId]);

       return unserialize($sLangData);

    }

	/**
     * For the provided article id, returns the associated variant numbers and additional texts
     *
     * @param $articleId
     * @param $mainDetailId
     * @return array
     */
    private function getVariantDetailsForPremiumArticles($articleId, $mainDetailId)
    {
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();

        $sql = "SELECT id, ordernumber, additionaltext
            FROM s_articles_details
            WHERE articleID = :articleId AND kind != 3";

        $variantsData = Shopware()->Db()->fetchAll(
            $sql,
            array('articleId' => $articleId)
        );

        foreach ($variantsData as $variantData) {
            $product = new StoreFrontBundle\Struct\ListProduct(
                $articleId,
                $variantData['id'],
                $variantData['ordernumber']
            );

            if ($variantData['id'] == $mainDetailId) {
                $variantData = Shopware()->Modules()->Articles()->sGetTranslation(
                    $variantData,
                    $articleId,
                    "article"
                );
            } else {
                $variantData = Shopware()->Modules()->Articles()->sGetTranslation(
                    $variantData,
                    $variantData['id'],
                    "variant"
                );
            }

            $product->setAdditional($variantData['additionaltext']);
            $products[$variantData['ordernumber']] = $product;
        }

        $products = Shopware()->Container()->get('shopware_storefront.additional_text_service')->buildAdditionalTextLists($products, $context);

        return array_map(
            function (StoreFrontBundle\Struct\ListProduct $elem) {
                return array(
                	'variantId' => $elem->getVariantId(),
                    'ordernumber' => $elem->getNumber(),
                    'additionaltext' => $elem->getAdditional()
                );
            },
            $products
        );
    }

    private function sGetProductStreamArticle($streamId){
    	$sProductStreams = [];
        $sContainer = Shopware()->Container();
        /** @var ProductSearch $searchService */
        $searchService = $sContainer->get('shopware_search.product_search');

            /** @var \Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface $contextService */
            $contextService = $sContainer->get('shopware_storefront.context_service');
            $context = $contextService->getShopContext();

            /** @var \Shopware\Components\ProductStream\CriteriaFactoryInterface $factory */
            $factory = $sContainer->get('shopware_product_stream.criteria_factory');
            $criteria = $factory->createCriteria($sContainer->get("front")->Request(), $context);
            $criteria->limit(50);
            /** @var \Shopware\Components\ProductStream\RepositoryInterface $streamRepository */
            $streamRepository = $sContainer->get('shopware_product_stream.repository');
            $streamRepository->prepareCriteria($criteria, $streamId);

            $searchResult = $searchService->search($criteria, $context);
            foreach ($searchResult->getProducts() as $key => $sProduct) {
                $sProductStreams[$sProduct->getId()] = $sProduct->getNumber();
            }

    	return $sProductStreams;
    }

}