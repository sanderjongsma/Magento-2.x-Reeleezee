<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Block\Adminhtml\Form;

/**
 * Vat code mapping field
 */
class Vatcodemapping extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var Tax class
     */
    protected $_taxClassRenderer;

    /**
     * @var Vat code
     */
    protected $_vatCodeRenderer;

    /**
     * @var Customergroup
     */
    protected $_groupRenderer;

    /**
     * Retrieve tax class renderer
     *
     * @return Tax class
     */
    protected function _getTaxClassRenderer()
    {
        if (!$this->_taxClassRenderer) {
            $this->_taxClassRenderer = $this->getLayout()->createBlock(
                \Appmerce\Reeleezee\Block\Adminhtml\Form\Taxclasses::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_taxClassRenderer->setClass('tax_class_select');
            $this->_taxClassRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_taxClassRenderer;
    }

    /**
     * Retrieve vat column renderer
     *
     * @return Vat code
     */
    protected function _getVatCodeRenderer()
    {
        if (!$this->_vatCodeRenderer) {
            $this->_vatCodeRenderer = $this->getLayout()->createBlock(
                \Appmerce\Reeleezee\Block\Adminhtml\Form\Vatcodes::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_vatCodeRenderer->setClass('vat_code_select');
            $this->_vatCodeRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_vatCodeRenderer;
    }

    /**
     * Retrieve group column renderer
     *
     * @return Customergroup
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                \Appmerce\Reeleezee\Block\Adminhtml\Form\Customergroups::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_groupRenderer->setClass('customer_group_select');
            $this->_groupRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'customer_group_id',
            ['label' => __('Customer Group'), 'renderer' => $this->_getGroupRenderer()]
        );
        $this->addColumn(
            'tax_class_id',
            ['label' => __('Product Tax Class'), 'renderer' => $this->_getTaxClassRenderer()]
        );
        $this->addColumn(
            'vat_code',
            ['label' => __('Reeleezee VAT Code'), 'renderer' => $this->_getVatCodeRenderer()]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Mapping');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('customer_group_id'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getTaxClassRenderer()->calcOptionHash($row->getData('tax_class_id'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getVatCodeRenderer()->calcOptionHash($row->getData('vat_code'))] = 'selected="selected"';
        
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
