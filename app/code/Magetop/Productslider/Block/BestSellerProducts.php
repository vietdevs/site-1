<?php
/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Productslider
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

namespace Magetop\Productslider\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magetop\Productslider\Helper\Data;

/**
 * Class BestSellerProducts
 * @package Magetop\Productslider\Block
 */
class BestSellerProducts extends AbstractSlider
{
    /**
     * @var BestSellersCollectionFactory
     */
    protected $_bestSellersCollectionFactory;

    /**
     * BestSellerProducts constructor.
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param DateTime $dateTime
     * @param Data $helperData
     * @param HttpContext $httpContext
     * @param BestSellersCollectionFactory $bestSellersCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        DateTime $dateTime,
        Data $helperData,
        HttpContext $httpContext,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        array $data = []
    )
    {
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;

        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $dateTime, $helperData, $httpContext, $data);
    }

    /**
     * get collection of best-seller products
     * @return mixed
     */
    public function getProductCollection()
    {
        $productIds  = [];
        $bestSellers = $this->_bestSellersCollectionFactory->create()
            ->setPeriod('month');

        foreach ($bestSellers as $product) {
            $productIds[] = $product->getProductId();
        }

        $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*')
            ->addStoreFilter($this->getStoreId())->setPageSize($this->getProductsCount());

        return $collection;
    }
}