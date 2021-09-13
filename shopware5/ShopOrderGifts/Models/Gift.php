<?php

namespace ShopOrderGifts\Models;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection,
    Shopware\Models\Shop\Shop,
    Shopware\Models\Article\Article,
    Shopware\Models\Customer\Group;


/**
 * Aquatuning Software Development - Order Gifts - Model - Gift
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_order_gifts")
 */

class Gift extends ModelEntity
{
    /**
     * @var integer   $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Status active or inactive.
     *
     * @var boolean   $status
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * The internal name of the gift.
     *
     * @var string   $name
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * The basket minimum amount for this gift.
     *
     * @var float   $price
     *
     * @ORM\Column(name="price", type="decimal")
     */
    private $price;

    /**
     * The basket minimum Quantity for this gift.
     *
     * @var int   $quantity_from
     *
     * @ORM\Column(name="quantity_from", type="integer", nullable=true)
     */
    private $quantityFrom;

    /**
     * The basket maximum Quantity for this gift.
     *
     * @var int   $quantity_to
     *
     * @ORM\Column(name="quantity_to", type="integer", nullable=true)
     */
    private $quantityTo;

    /**
     * The basket minimum amount for this gift.
     *
     * @var float   $price_from
     *
     * @ORM\Column(name="price_from", type="decimal", nullable=true)
     */
    private $priceFrom;

    /**
     * The basket maximum amount for this gift.
     *
     * @var float   $price_to
     *
     * @ORM\Column(name="price_to", type="decimal", nullable=true)
     */
    private $priceTo;

    /**
     * Active Gift from-date
     *
     * @var datetime   $date_from
     *
     * @ORM\Column(name="date_from", type="datetime", nullable=true)
     */
    private $dateFrom;

    /**
     * Active Gift to-date
     *
     * @var datetime   $date_to
     *
     * @ORM\Column(name="date_to", type="datetime", nullable=true)
     */
    private $dateTo;

    /**
     * @var int   $gift_type
     *
     * @ORM\Column(name="gift_type", type="integer", nullable=true)
     */
    private $giftType;

    /**
     * @var int   $percental
     *
     * @ORM\Column(name="percental", type="integer", nullable=true)
     */
    private $percental;

    /**
     * @var float   $value
     *
     * @ORM\Column(name="value", type="decimal", nullable=true)
     */
    private $value;

    /**
     * Amount of articles to be added from this gift.
     *
     * @var integer   $quantity
     *
     * @ORM\Column(name="quantity", type="integer", options={"default" = 1}, nullable=true)
     */
    private $quantity = 1;

    /**
     * @var int   $number_redeem
     *
     * @ORM\Column(name="number_redeem", type="integer", nullable=true)
     */
    private $numberRedeem;

    /**
     * @var int   $number_order
     *
     * @ORM\Column(name="number_order", type="integer", nullable=true)
     */
    private $numberOrder;

    /**
     * Kumulierbarkeit
     *
     * @var boolean   $cumulative
     *
     * @ORM\Column(name="cumulative", type="boolean")
     */
    private $cumulative;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Shop\Shop")
     * @ORM\JoinTable(name="s_plugin_order_gifts_shops",
     *      joinColumns={
     *          @ORM\JoinColumn(name="giftId", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     *      }
     * )
     */
    protected $shops;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Article\Article")
     * @ORM\JoinTable(name="s_plugin_order_gifts_articles",
     *      joinColumns={
     *          @ORM\JoinColumn(name="giftId", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="articleId", referencedColumnName="id")
     *      }
     * )
     */
    protected $giftArticles;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Article\Article")
     * @ORM\JoinTable(name="s_plugin_order_articles",
     *      joinColumns={
     *          @ORM\JoinColumn(name="giftId", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="articleId", referencedColumnName="id")
     *      }
     * )
     */
    protected $articles;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Category\Category")
     * @ORM\JoinTable(name="s_plugin_order_categories",
     *      joinColumns={
     *          @ORM\JoinColumn(name="giftId", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
     *      }
     * )
     */
    protected $categories;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Shopware\Models\ProductStream\ProductStream")
     * @ORM\JoinTable(name="s_plugin_order_product_stream",
     *      joinColumns={
     *          @ORM\JoinColumn(name="giftId", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="productStreamId", referencedColumnName="id")
     *      }
     * )
     */
    protected $productStream;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Customer\Group")
     * @ORM\JoinTable(name="s_plugin_order_gifts_customergroups",
     *      joinColumns={
     *          @ORM\JoinColumn(name="giftId", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="customergroupId", referencedColumnName="id")
     *      }
     * )
     */
    protected $customergroups;

    /**
     * Model constructor to set default values.
     *
     * @return \Shopware\CustomModels\ShopOrderGifts\Gift
     */
    public function __construct()
    {
        $this->giftArticles = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->productStream = new ArrayCollection();
        $this->shops = new ArrayCollection();
        $this->customergroups = new ArrayCollection();
    }

    /**
     * get our unique db id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the gift name.
     *
     * @var string   $name
     *
     * @return void
     */
    public function setName( $name )
    {
        $this->name = $name;
    }

    /**
     * Get the gift name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the status enabled/disabled for this gift.
     *
     * @var boolean   $status
     *
     * @return void
     */
    public function setStatus( $status )
    {
        $this->status = $status;
    }

    /**
     * Get the status enabled/disabled of this gift.
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the gift starting price.
     *
     * @var float   $price
     *
     * @return void
     */
    public function setPrice( $price )
    {
        $this->price = $price;
    }

    /**
     * Get the gift starting price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the gift starting QuantityFrom.
     *
     * @var float   $quantityFrom
     *
     * @return void
     */
    public function setQuantityFrom( $quantityFrom )
    {
        $this->quantityFrom = $quantityFrom;
    }

    /**
     * Get the gift starting QuantityFrom.
     *
     * @return float
     */
    public function getQuantityFrom()
    {
        return $this->quantityFrom;
    }


    /**
     * Set the gift starting QuantityTo.
     *
     * @var float   $quantityTo
     *
     * @return void
     */
    public function setQuantityTo( $quantityTo )
    {
        $this->quantityTo = $quantityTo;
    }

    /**
     * Get the gift starting QuantityTo.
     *
     * @return float
     */
    public function getQuantityTo()
    {
        return $this->quantityTo;
    }

    /**
     * Set the gift starting PriceFrom.
     *
     * @var float   $priceFrom
     *
     * @return void
     */
    public function setPriceFrom( $priceFrom )
    {
        $this->priceFrom = $priceFrom;
    }

    /**
     * Get the gift starting PriceFrom.
     *
     * @return float
     */
    public function getPriceFrom()
    {
        return $this->priceFrom;
    }

    /**
     * Set the gift starting PriceTo.
     *
     * @var float   $priceTo
     *
     * @return void
     */
    public function setPriceTo( $priceTo )
    {
        $this->priceTo = $priceTo;
    }

    /**
     * Get the gift starting PriceTo.
     *
     * @return float
     */
    public function getPriceTo()
    {
        return $this->priceTo;
    }

    /**
     * Set the gift starting DateFrom.
     *
     * @var float   $dateFrom
     *
     * @return void
     */
    public function setDateFrom( $dateFrom )
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * Get the gift starting DateFrom.
     *
     * @return float
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set the gift starting DateTo.
     *
     * @var float   $dateTo
     *
     * @return void
     */
    public function setDateTo( $dateTo )
    {
        $this->dateTo = $dateTo;
    }

    /**
     * Get the gift starting DateTo.
     *
     * @return float
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set the gift starting GiftType.
     *
     * @var float   $giftType
     *
     * @return void
     */
    public function setGiftType( $giftType )
    {
        $this->giftType = $giftType;
    }

    /**
     * Get the gift starting GiftType.
     *
     * @return float
     */
    public function getGiftType()
    {
        return $this->giftType;
    }


    /**
     * Set the gift starting Percental.
     *
     * @var float   $percental
     *
     * @return void
     */
    public function setPercental( $percental )
    {
        $this->percental = $percental;
    }

    /**
     * Get the gift starting Percental.
     *
     * @return float
     */
    public function getPercental()
    {
        return $this->percental;
    }

    /**
     * Set the gift starting Value.
     *
     * @var float   $value
     *
     * @return void
     */
    public function setValue( $value )
    {
        $this->value = $value;
    }

    /**
     * Get the gift starting Value.
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Setter method for the property.
     *
     * @var integer   $quantity
     *
     * @return void
     */
    public function setQuantity( $quantity )
    {
        $this->quantity = $quantity;
    }

    /**
     * Getter method for the property.
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the gift starting NumberRedeem.
     *
     * @var float   $numberRedeem
     *
     * @return void
     */
    public function setNumberRedeem( $numberRedeem )
    {
        $this->numberRedeem = $numberRedeem;
    }

    /**
     * Get the gift starting NumberRedeem.
     *
     * @return float
     */
    public function getNumberRedeem()
    {
        return $this->numberRedeem;
    }


    /**
     * Set the gift starting NumberOrder.
     *
     * @var float   $numberOrder
     *
     * @return void
     */
    public function setNumberOrder( $numberOrder )
    {
        $this->numberOrder = $numberOrder;
    }

    /**
     * Get the gift starting NumberOrder.
     *
     * @return float
     */
    public function getNumberOrder()
    {
        return $this->numberOrder;
    }

    /**
     * Set the Cumulative enabled/disabled for this gift.
     *
     * @var boolean   $cumulative
     *
     * @return void
     */
    public function setCumulative( $cumulative )
    {
        $this->cumulative = $cumulative;
    }

    /**
     * Get the Cumulative enabled/disabled of this gift.
     *
     * @return boolean
     */
    public function getCumulative()
    {
        return $this->cumulative;
    }

    /**
     * Get all articles for this gift.
     *
     * @return ArrayCollection
     */
    public function getGiftArticles()
    {
        return $this->giftArticles;
    }

    /**
     * Set all giftArticles for this gift.
     *
     * @param ArrayCollection   $giftArticles
     *
     * @return void
     */
    public function setGiftArticles( $giftArticles )
    {
        $this->giftArticles = $giftArticles;
    }

    /**
     * Get all articles for this gift.
     *
     * @return ArrayCollection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set all articles for this gift.
     *
     * @param ArrayCollection   $articles
     *
     * @return void
     */
    public function setArticles( $articles )
    {
        $this->articles = $articles;
    }

    /**
     * Get all articles for this gift.
     *
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set all categories for this gift.
     *
     * @param ArrayCollection   $categories
     *
     * @return void
     */
    public function setCategories( $categories )
    {
        $this->categories = $categories;
    }

    /**
     * Get all articles for this gift.
     *
     * @return ArrayCollection
     */
    public function getProductStream()
    {
        return $this->productStream;
    }

    /**
     * Set all productStream for this gift.
     *
     * @param ArrayCollection   $productStream
     *
     * @return void
     */
    public function setProductStream( $productStream )
    {
        $this->productStream = $productStream;
    }

    /**
     * Add an article to the gift.
     *
     * @param \Shopware\Models\Article\Article   $article
     *
     * @return void
     */
    public function addArticle( \Shopware\Models\Article\Article $article )
    {
        // not yet added?
        if ( !$this->articles->contains( $article ) ) {
            $this->articles->add( $article );
        }
    }

    /**
     * Remove an article from the gift.
     *
     * @param \Shopware\Models\Article\Article   $article
     *
     * @return void
     */
    public function removeArticle( \Shopware\Models\Article\Article $article )
    {
        // remove it
        $this->articles->removeElement( $article );
    }

    /**
     * Get all shops for this gift.
     *
     * @return ArrayCollection
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * Set all shops for this gift.
     *
     * @param ArrayCollection   $shops
     *
     * @return void
     */
    public function setShops( $shops )
    {
        $this->shops = $shops;
    }

    /**
     * Add a shop to the gift.
     *
     * @param \Shopware\Models\Shop\Shop   $shop
     *
     * @return void
     */
    public function addShop( \Shopware\Models\Shop\Shop $shop )
    {
        // not yet added?
        if ( !$this->shops->contains( $shop ) ) {
            $this->shops->add( $shop );
        }
    }

    /**
     * Remove a shop from the gift.
     *
     * @param \Shopware\Models\Shop\Shop   $shop
     *
     * @return void
     */
    public function removeShop( \Shopware\Models\Shop\Shop $shop )
    {
        // remove it
        $this->shops->removeElement( $shop );
    }

    /**
     * Get all customer groups for this gift.
     *
     * @return ArrayCollection
     */
    public function getCustomergroups()
    {
        return $this->customergroups;
    }

    /**
     * Set all customer groups for this gift.
     *
     * @param ArrayCollection   $customergroups
     *
     * @return void
     */
    public function setCustomergroups( $customergroups )
    {
        $this->customergroups = $customergroups;
    }

    /**
     * Add a customer group to the gift.
     *
     * @param \Shopware\Models\Customer\Group   $customergroup
     *
     * @return void
     */
    public function addCustomergroup( \Shopware\Models\Customer\Group $customergroup )
    {
        // not yet added?
        if ( !$this->customergroups->contains( $customergroup )) {
            $this->customergroups->add( $customergroup );
        }
    }

    /**
     * Remove a customer group from the gift.
     *
     * @param \Shopware\Models\Customer\Group   $customergroup
     *
     * @return void
     */
    public function removeCustomergroup( \Shopware\Models\Customer\Group $customergroup )
    {
        // remove it
        $this->customergroups->removeElement( $customergroup );
    }

}