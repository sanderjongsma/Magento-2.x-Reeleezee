<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Controller\Adminhtml\Order\Invoice;

class Export extends \Appmerce\Reeleezee\Controller\Adminhtml\Reeleezee
{
    /**
     * Export as XML and output to screen
     */
    public function execute()
    {
        $invoice_id = $this->getRequest()->getParam('invoice_id');
        $invoice = $this->_getInvoice()->load($invoice_id);
        $contents = $this->api->importReeleezee($invoice, true, true);
        
        // Output XML
        $result = $this->_rawResultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_RAW);
        $result->setHeader('Content-Type', 'text/xml');
        $result->setContents($contents);
        return $result;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Appmerce_Reeleezee::config');
    }
}
