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

namespace Magetop\Productslider\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Slider
 * @package Magetop\Productslider\Controller\Product
 */
class Slider extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Slider constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}