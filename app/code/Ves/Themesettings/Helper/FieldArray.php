<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Themesettings
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Themesettings\Helper;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Store\Model\Store;

/**
 * MinSaleQty value manipulation helper
 */
class FieldArray
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;
    /**
     * @var \Ves\Themesettings\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param GroupManagementInterface $groupManagement
     * @param \Ves\Themesettings\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Math\Random $mathRandom,
        GroupManagementInterface $groupManagement,
        \Ves\Themesettings\Helper\Data $helperData
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->mathRandom = $mathRandom;
        $this->groupManagement = $groupManagement;
        $this->helperData = $helperData;
    }

    /**
     * Retrieve fixed qty value
     *
     * @param int|float|string|null $qty
     * @return float|null
     */
    protected function fixQty($qty)
    {
        return !empty($qty) ? (float) $qty : null;
    }

    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     * @return string
     */
    protected function serializeValue($value)
    {
        if (is_numeric($value)) {
            $data = (float) $value;
            return (string) $data;
        } elseif (is_array($value)) {
            $data = [];
            foreach ($value as $groupId => $qty) {
                if (!array_key_exists($groupId, $data)) {
                    $data[$groupId] = $this->fixQty($qty);
                }
            }
            if (count($data) == 1 && array_key_exists($this->getAllCustomersGroupId(), $data)) {
                return (string) $data[$this->getAllCustomersGroupId()];
            }
            return $this->helperData->getSerializeText($data);
        } else {
            return '';
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     * @return array
     */
    protected function unserializeValue($value)
    {
        if (is_numeric($value)) {
            return [$this->getAllCustomersGroupId() => $this->fixQty($value)];
        } elseif (is_string($value) && !empty($value)) {
            return $this->helperData->getUnserializeText($value);
        } else {
            return [];
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     * @return bool
     */
    protected function isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('customer_group_id', $row)
                || !array_key_exists('min_sale_qty', $row)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $groupId => $qty) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = ['customer_group_id' => $groupId, 'min_sale_qty' => $this->fixQty($qty)];
        }
        return $result;
    }

    /**
     * Decode value from used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('customer_group_id', $row)
                || !array_key_exists('min_sale_qty', $row)
            ) {
                continue;
            }
            $groupId = $row['customer_group_id'];
            $qty = $this->fixQty($row['min_sale_qty']);
            $result[$groupId] = $qty;
        }
        return $result;
    }

    /**
     * Retrieve min_sale_qty value from config
     *
     * @param int $customerGroupId
     * @param null|string|bool|int|Store $store
     * @return float|null
     */
    public function getConfigValue($customerGroupId, $store = null)
    {
        $value = $this->scopeConfig->getValue(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_MIN_SALE_QTY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $value = $this->unserializeValue($value);
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $result = null;
        foreach ($value as $groupId => $qty) {
            if ($groupId == $customerGroupId) {
                $result = $qty;
                break;
            } elseif ($groupId == $this->getAllCustomersGroupId()) {
                $result = $qty;
            }
        }
        return $this->fixQty($result);
    }

    /**
     * Make value readable by \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->unserializeValue($value);
        if (!$this->isEncodedArrayFieldValue($value)) {
            $value = $this->encodeArrayFieldValue($value);
        }
        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        $value = $this->serializeValue($value);
        return $value;
    }

    /**
     * Return the all customer group id
     *
     * @return int
     */
    protected function getAllCustomersGroupId()
    {
        return $this->groupManagement->getAllCustomersGroup()->getId();
    }
}
