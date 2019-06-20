<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Block\Adminhtml\Form;

/**
 * Vat group mapping field
 */
class Vatgroupmapping extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var Tax class
     */
    protected $_taxClassRenderer;

    /**
     * @var Vat group
     */
    protected $_vatGroupRenderer;

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
     * Retrieve vat group renderer
     *
     * @return Vat group
     */
    protected function _getVatGroupRenderer()
    {
        if (!$this->_vatGroupRenderer) {
            $this->_vatGroupRenderer = $this->getLayout()->createBlock(
                \Appmerce\Reeleezee\Block\Adminhtml\Form\Vatgroups::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_vatGroupRenderer->setClass('vat_group_select');
            $this->_vatGroupRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_vatGroupRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'tax_class_id',
            ['label' => __('Product Tax Class'), 'renderer' => $this->_getTaxClassRenderer()]
        );
        $this->addColumn(
            'vat_group',
            ['label' => __('Reeleezee Vat Group'), 'renderer' => $this->_getVatGroupRenderer()]
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
        $optionExtraAttr['option_' . $this->_getTaxClassRenderer()->calcOptionHash($row->getData('tax_class_id'))] = 'selected="selected"';
        $optionExtraAttr['option_' . $this->_getVatGroupRenderer()->calcOptionHash($row->getData('vat_group'))] = 'selected="selected"';
        
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
