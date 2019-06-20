<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Controller\Adminhtml\Order\Invoice;

class Import extends \Appmerce\Reeleezee\Controller\Adminhtml\Reeleezee
{
    /**
     * Import order to Reeleezee
     */
    public function execute()
    {
        $invoice_id = $this->getRequest()->getParam('invoice_id');
        $invoice = $this->_getInvoice()->load($invoice_id);
        $this->api->importReeleezee($invoice);
        $this->_redirect('sales/invoice/view', array('invoice_id' => $invoice_id));
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Appmerce_Reeleezee::config');
    }
}
