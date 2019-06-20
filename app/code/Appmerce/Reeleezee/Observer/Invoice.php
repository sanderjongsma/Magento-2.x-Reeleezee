<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Observer;

class Invoice implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Appmerce\Reeleezee\Model\Api
     */
    protected $api;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Appmerce\Reeleezee\Model\Api $api
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Appmerce\Reeleezee\Model\Api $api
    ) {
        $this->api = $api;
    }

    /**
     * Reads the invoice and imports XML data into Reeleezee via WDSL
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        if ($this->api->getConfigData('reeleezee/settings/import_mode', $invoice->getStoreId())) {
            $this->api->importReeleezee($invoice);
        }
        return;
    }

}
