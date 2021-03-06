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

namespace Magetop\Productslider\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magetop\Productslider\Helper\Data;
use Magetop\Productslider\Model\Config\Source\Additional;

/**
 * Class Design
 * @package Magetop\Productslider\Block\Adminhtml\Slider\Edit\Tab
 */
class Design extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magetop\Productslider\Model\Config\Source\Additional
     */
    protected $_additional;

    /**
     * @var \Magetop\Productslider\Helper\Data
     */
    protected $_helperData;

    /**
     * Design constructor.
     * @param \Magetop\Productslider\Helper\Data $helperData
     * @param \Magetop\Productslider\Model\Config\Source\Additional $additional
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Data $helperData,
        Additional $additional,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    )
    {
        $this->_helperData = $helperData;
        $this->_additional = $additional;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magetop\Productslider\Model\Slider $slider */
        $slider = $this->_coreRegistry->registry('magetop_productslider_slider');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slider_');
        $form->setFieldNameSuffix('slider');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Design'),
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addField('title', 'text', [
            'name'  => 'title',
            'label' => __('Title'),
            'title' => __('Title'),
        ]);
        $fieldset->addField('description', 'textarea', [
            'name'  => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
        ]);
        $fieldset->addField('limit_number', 'text', [
            'name'  => 'limit_number',
            'label' => __('Limit the number of products'),
            'title' => __('Limit the number of products'),
        ]);

        $fieldset->addField('display_additional', 'multiselect', [
            'name'   => 'display_additional',
            'label'  => __('Display additional information'),
            'title'  => __('Display additional information'),
            'values' => $this->_additional->toOptionArray(),
            'note'   => __('Select information or button(s) to display with products.')
        ]);

        $isResponsive = $fieldset->addField('is_responsive', 'select', [
            'name'    => 'is_responsive',
            'label'   => __('Is Responsive'),
            'title'   => __('Is Responsive'),
            'options' => [
                '1' => __('Yes'),
                '0' => __('No'),
                '2' => __('Use Config')
            ]
        ]);

        $responsiveItem = $fieldset->addField('responsive_items', 'Magetop\Productslider\Block\Adminhtml\Slider\Edit\Tab\Renderer\Responsive', [
            'name'  => 'responsive_items',
            'label' => __('Max Items slider'),
            'title' => __('Max Items slider'),
        ]);

        $this->setChild('form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap($isResponsive->getHtmlId(), $isResponsive->getName())
                ->addFieldMap($responsiveItem->getHtmlId(), $responsiveItem->getName())
                ->addFieldDependence($responsiveItem->getName(), $isResponsive->getName(), '1')
        );

        $form->setValues($slider->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Design');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }
}
