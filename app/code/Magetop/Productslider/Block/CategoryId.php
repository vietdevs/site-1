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
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magetop\Productslider\Helper\Data;

/**
 * Class CategoryId
 * @package Magetop\Productslider\Block
 */
class CategoryId extends AbstractSlider
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * CategoryId constructor.
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param DateTime $dateTime
     * @param Data $helperData
     * @param HttpContext $httpContext
     * @param CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        DateTime $dateTime,
        Data $helperData,
        HttpContext $httpContext,
        CategoryFactory $categoryFactory,
        array $data = []
    )
    {
        $this->_categoryFactory = $categoryFactory;

        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $dateTime, $helperData, $httpContext, $data);
    }

    /**
     * Get Product Collection by Category Ids
     *
     * @return $this|array
     */
    public function getProductCollection()
    {
        $productIds = $this->getProductIdsByCategory();
        $collection = [];
        if (!empty($productIds)) {
            $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds)->setPageSize($this->getProductsCount());;
            $this->_addProductAttributesAndPrices($collection);
        }

        return $collection;
    }

    /**
     * Get ProductIds by Category
     *
     * @return array
     */
    public function getProductIdsByCategory()
    {
        $productIds = [];
        $catIds     = $this->getSliderCategoryIds();
        foreach ($catIds as $catId) {
            $category   = $this->_categoryFactory->create()->load($catId);
            $collection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addCategoryFilter($category);

            foreach ($collection as $item) {
                $productIds[] = $item->getData('entity_id');
            }
        }

        return $productIds;
    }

    /**
     * Get Slider CategoryIds
     *
     * @return array|int|mixed
     */
    public function getSliderCategoryIds()
    {
        if ($this->getData('category_id')) {
            return $this->getData('category_id');
        }
        if ($this->getSlider()) {
            $catIds = explode(',', $this->getSlider()->getCategoriesIds());

            return $catIds;
        }

        return 2;
    }
}