<?php
namespace ShopOrderGifts\Models;

use Shopware\Components\Model\ModelRepository;
use Doctrine\ORM\Query;

/**
 *
 * Repository for the Faw model (Shopware\CustomModels\CustomModels\Gift).
 * <br>
 * The Gift model repository is responsible to load all Gift data.
 * It supports the standard functions like findAll or findBy and extends the standard repository for
 * some specific functions to return the model data as array.
 *
 */
class Repository extends ModelRepository
{

	/**
	 * Returns an instance of the \Doctrine\ORM\Query object which select the Gift article for the detail page
	 *
	 * @param $filter
	 * @return \Doctrine\ORM\Query
	 */
	public function getBackendDetailQuery($filter, $action='checkout')
	{
		$builder = $this->getBackedDetailQueryBuilder($filter, '', $action);
		return $builder->getQuery();
	}

	/**
	 * Helper function to create the query builder for the "getBackendDetailQuery" function.
	 * This function can be hooked to modify the query builder of the query object.
	 *
	 * @param $filter
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getBackedDetailQueryBuilder($filter, $order='', $action='', $sActive=0)
	{
		$builder = $this->createQueryBuilder("gift");
		$builder->select(array('gift', 'giftArticles', 'assignedArticles', 'categories', 'productStream', 'shops', 'customergroups' ))
				->leftJoin('gift.giftArticles', 'giftArticles')
				->leftJoin('gift.articles', 'assignedArticles')
				->leftJoin('gift.categories', 'categories')
				->leftJoin('gift.productStream', 'productStream')
				->leftJoin('gift.shops', 'shops')
				->leftJoin('gift.customergroups', 'customergroups');

		if($action == 'checkout'){
			$today = date('Y-m-d');
			$builder->andWhere('gift.dateFrom <= :fromDate')
				->andWhere("(gift.dateTo IS NULL OR gift.dateTo = '' OR gift.dateTo >= :toDate)")
				->andWhere("(IFNULL(gift.numberOrder, 0) < gift.numberRedeem OR gift.numberOrder < gift.numberRedeem)")
				->setParameter('fromDate', $today)
				->setParameter('toDate', $today)
				->addFilter($filter);
		}else {
			// Search Gifts
			if (!empty($filter) && $filter[0]["property"] == "filter" && !empty($filter[0]["value"])) {
				$builder->andWhere('gift.title LIKE ?1')
						->orWhere('gift.description LIKE ?1')
						->setParameter(1, '%'.$filter[0]["value"].'%');
			}
			// Get with perticular giftId
			if (!empty($filter) && $filter[0]["property"] == "id" && !empty($filter[0]["value"])) {
				$builder->andWhere('gift.id = ?1')
						->setParameter(1, $filter[0]["value"]);
			}

		}
		if($sActive){
			$builder->andWhere('gift.active = 1');
		}

		if (!empty($order)) {
			$builder->addOrderBy($order);
		}
		return $builder;
	}
}