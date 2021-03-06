<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_EbayMultiAccount
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\EbayMultiAccount\Controller\Adminhtml\JobScheduler;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassSyncJob
 * @package Ced\EbayMultiAccount\Controller\Adminhtml\JobScheduler
 */
class MassSyncJob extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /** @var \Ced\EbayMultiAccount\Model\ResourceModel\FeedDetails\CollectionFactory */
    public $feedDetailsCollection;
    /**
     * @var Filter
     */
    public $filter;

    const ADMIN_RESOURCE = 'Ced_EbayMultiAccount::EbayMultiAccount';

    /**
     * MassSyncJob constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Filter $filter
     * @param \Ced\EbayMultiAccount\Model\ResourceModel\FeedDetails\CollectionFactory $feedDetailsCollection
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        \Ced\EbayMultiAccount\Model\ResourceModel\FeedDetails\CollectionFactory $feedDetailsCollection
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->feedDetailsCollection = $feedDetailsCollection;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->feedDetailsCollection->create());
        $feedIds = $collection->getAllIds();
        if (!empty($feedIds)) {
            $feedIds = array_chunk($feedIds, 20);
            $this->_session->setFeedIdsForSync($feedIds);
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Ced_EbayMultiAccount::product');
            $resultPage->getConfig()->getTitle()->prepend(__('Start Job Syncing'));
            return $resultPage;
        } else {
            $this->messageManager->addErrorMessage(__('No Job Found To Sync'));
            return $this->_redirect('ebaymultiaccount/jobScheduler/index');
        }
    }
}
