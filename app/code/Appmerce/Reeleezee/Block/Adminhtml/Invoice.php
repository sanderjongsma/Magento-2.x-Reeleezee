<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Block\Adminhtml;

class Invoice extends \Magento\Sales\Block\Adminhtml\Order\Invoice\View
{
    /**
     * Add & remove control buttons
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        // Add Reeleezee import button
        $this->buttonList->add(
            'invoice_reeleezee_import',
            [
                'label' => __('Reeleezee Import'),
                'class' => 'save',
                'onclick' => 'setLocation(\'' . $this->getImportUrl() . '\')'
            ]
        );
        
        // Add Reeleezee export button
        $this->buttonList->add(
            'invoice_reeleezee_export',
            [
                'label' => __('Reeleezee XML Export'),
                'class' => 'save',
                'onclick' => 'setLocation(\'' . $this->getExportUrl() . '\')'
            ]
        );
    }

    /**
     * Reeleezee import URL
     * 
     * @return url
     */
    public function getImportUrl()
    {
        return $this->getUrl('reeleezee/*/import', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * Reeleezee export URL
     * 
     * @return url
     */
    public function getExportUrl()
    {
        return $this->getUrl('reeleezee/*/export', ['invoice_id' => $this->getInvoice()->getId()]);
    }

}
