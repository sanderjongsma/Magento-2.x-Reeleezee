<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Controller\Adminhtml;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

abstract class Reeleezee extends \Magento\Backend\App\Action implements CsrfAwareActionInterface
{
    protected $log;

    /**
     * @var \Appmerce\Reeleezee\Model\Api
     */
    protected $api;

    /**
     * @var \Magento\Framework\Controller\Result
     */
    protected $_rawResultFactory;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Psr\Log\LoggerInterface $log
     * @param \Appmerce\Reeleezee\Model\Api $api
     * @param \Magento\Framework\Controller\ResultFactory $rawResultFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $log,
        \Appmerce\Reeleezee\Model\Api $api,
        \Magento\Framework\Controller\ResultFactory $rawResultFactory
    ) {
        parent::__construct($context);
        $this->log = $log;
        $this->api = $api;
        $this->_rawResultFactory = $rawResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
    
    /**
     * @return \Magento\Sales\Model\Order
     */
    protected function _getOrder()
    {
        return $this->_objectManager->get('Magento\Sales\Model\Order');
    }
    
    /**
     * @return \Magento\Sales\Model\Invoice
     */
    protected function _getInvoice()
    {
        return $this->_objectManager->get('Magento\Sales\Model\Order\Invoice');
    }
}
