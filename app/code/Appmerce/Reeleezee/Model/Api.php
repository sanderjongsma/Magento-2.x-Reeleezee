<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Model;

use Appmerce\Reeleezee\Helper\Vatcodehelper;
use Appmerce\Reeleezee\Helper\Vatgrouphelper;

use SoapClient;
use DOMDocument;

class Api
{
    /**
     * Reeleezee Constants
     */
    const VERSION = '1.24';
    const WDSL = 'https://portal.reeleezee.nl/ezVersionService/externalServices/TaxonomyService/taxonomywebservice.asmx?WSDL';
    const ADDRESS_TYPE_OFFICE = "Office";
    const ADDRESS_TYPE_HOME = "Home";
    const ADDRESS_TYPE_DELIVERY = "Delivery";
    const PRODUCT_TYPE = "Producten";
    const PRODUCT_ACTIVE = 'true';
    const TAX_INCL = 'true';
    const TAX_EXCL = 'false';
    const PAGE_BREAK_FALSE = 'false';
    const DEFAULT_LANGCODE = 'en';

    /**
     * Min and Maxlengths
     */
    const MINLENGTH_COMPANY = 1;
    const MINLENGTH_NAME = 1;
    const MAXLENGTH_ZIPCODE = 8;
    const MAXLENGTH_STREET = 30;
    const MAXLENGTH_CITY = 30;
    const MAXLENGTH_SKU = 32;
    const MAXLENGTH_NAME = 50;
    const MAXLENGTH_DESCRIPTION = 200;
    const MAXLENGTH_CID = 9;

    /**
     * Admin Settings Defaults
     */
    const PAYMENT_DUE_DAYS_DEFAULT = 14;
    const ACCOUNT_REF_DEFAULT = '8000';
    const UNIT_DEFAULT = 'ST';
    const PAGE_BREAK_DEFAULT = 0;

    /**
     * Booking Modes
     */
    const BOOKING_DOCUMENT = 'document';
    const BOOKING_RECEIPT = 'receipt';

    protected $doc;

    protected $log;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Appmerce\Reeleezee\Helper\Vatcodehelper
     */
    protected $vatCodeHelper;
    
    /**
     * @var \Appmerce\Reeleezee\Helper\Vatgrouphelper
     */
    protected $vatGroupHelper;
    
    /**
     * @var \Appmerce\Reeleezee\Helper\Vatgrouphelper
     */
    protected $messageManager;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Psr\Log\LoggerInterface $log
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Appmerce\Reeleezee\Helper\Vatcodehelper $vatCodeHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $log,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appmerce\Reeleezee\Helper\Vatcodehelper $vatCodeHelper,
        \Appmerce\Reeleezee\Helper\Vatgrouphelper $vatGroupHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->log = $log;
        $this->localeResolver = $localeResolver;
        $this->_context = $context;
        $this->_vatCodeHelper = $vatCodeHelper;
        $this->_vatGroupHelper = $vatGroupHelper;
        $this->messageManager = $messageManager;
    }
    
    /**
     * Get store configuration
     */
    public function getConfigData($path, $storeId = null)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    /**
     * Return AccountReference
     */
    public function getAccountReference($product, $store_id = null)
    {
        $code = '';
        $attribute_value = '';

        // $attribute = $this->getConfigData('reeleezee/taxes/accountref_attr', $store_id);
        // if (!empty($attribute)) {
           // $attribute_value = $product->getData($attribute, $store_id);
        // }
        $attribute_value_default = $this->getConfigData('reeleezee/taxes/accountref', $store_id);

        if (!empty($attribute_value)) {
            $code = $attribute_value;
        }
        elseif (!empty($attribute_value_default)) {
            $code = $attribute_value_default;
        }
        else {
            $code = self::ACCOUNT_REF_DEFAULT;
        }

        return $code;
    }

    /**
     * Return UnitCode
     */
    public function getUnitCode($product, $store_id = null)
    {
        $code = '';
        $attribute_value = '';

        // $attribute = $this->getConfigData('reeleezee/invoice/unit_code_attr', $store_id);
        // if (!empty($attribute)) {
            // $attribute_value = $product->getData($attribute, $store_id);
        // }
        $attribute_value_default = $this->getConfigData('reeleezee/invoice/unit_code', $store_id);

        if (!empty($attribute_value)) {
            $code = $attribute_value;
        }
        elseif (!empty($attribute_value_default)) {
            $code = $attribute_value_default;
        }
        else {
            $code = self::UNIT_DEFAULT;
        }

        return $code;
    }

    /**
     * Get Vat Group Mapping
     */
    public function getVatGroup($product, $store_id = null)
    {
        $code = $this->_vatGroupHelper->getConfigValue($product->getTaxClassId(), $store_id);
        if (empty($code)) {
            $code = $this->getConfigData('reeleezee/taxes/vatgroup_default', $store_id);
        }
        return $code;
    }

    /**
     * Get Vat Code Mapping
     */
    public function getVatCode($country, $customer_group_id, $product, $store_id = null)
    {
        $code = $this->_vatCodeHelper->getConfigValue($customer_group_id, $product->getTaxClassId(), $store_id);
        if (empty($code)) {
            $code = $this->getConfigData('reeleezee/taxes/vatcode_default', $store_id);
        }

        // Special case:
        // Customer not from EU: usually 0% (Nultarief)
        $eu_countries = array(
            'BE',
            'BG',
            'CZ',
            'DK',
            'DE',
            'EE',
            'IE',
            'EL',
            'ES',
            'FR',
            'IT',
            'CY',
            'LV',
            'LT',
            'LU',
            'HU',
            'MT',
            'NL',
            'AT',
            'PL',
            'PT',
            'RO',
            'SI',
            'SK',
            'FI',
            'SE',
            'GB'
        );
        if (!in_array($country, $eu_countries) && $country != $this->getConfigData('reeleezee/taxes/vatcode_origin', $store_id)) {
            $code = $this->getConfigData('reeleezee/taxes/vatcode_world', $store_id);
        }

        return $code;
    }

    /**
     * Get Shipping Vat Code Mapping
     */
    public function getShippingVatCode($customer_group_id, $store_id = null)
    {
        $shipping_tax_class = $this->getConfigData('tax/classes/shipping_tax_class', $store_id);
        if (is_null($shipping_tax_class)) {
            $code = $this->getConfigData('reeleezee/taxes/vatcode_default', $store_id);
        }
        else {
            $code = $this->_vatCodeHelper->getConfigValue($customer_group_id, $shipping_tax_class, $store_id);
            if (empty($code)) {
                $code = $this->getConfigData('reeleezee/taxes/vatcode_default', $store_id);
            }
        }
        return $code;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    protected function _getOrder()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $_objectManager->create('\Magento\Sales\Model\Order');
    }
    
    /**
     * Builds the XML message and posts to Reeleezee
     */
    public function importReeleezee($invoice, $outputMessage = true, $outputXML = false, $creditmemo = false)
    {
        $order_id = $invoice->getOrderId();
        $order = $this->_getOrder()->load($order_id);
        $customer_id = $order->getCustomerId();
        $items = $order->getAllItems();
        $account = $this->_getOrder()->load($customer_id);
        $store_id = $order->getStoreId();
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        // Get next invoice increment
        $invoice_increment_id = $invoice->getIncrementId();
        if (empty($invoice_increment_id)) {
            $resource = $_objectManager->create('\Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

            $_eavEntityStore = $connection->getTableName('eav_entity_store');
            $_eavEntityType = $connection->getTableName('eav_entity_type');

            $rows = $connection->query("select s.increment_last_id
                from " . $_eavEntityStore . " s, " . $_eavEntityType . " t
                where s.store_id = :id and s.entity_type_id = t.entity_type_id and t.entity_type_code = :code", array(
                'id' => $store_id,
                'code' => "invoice"
            ));

            // First orders have no $invoice_increment_id, we make one up
            $invoice_increment_id = 1;
            foreach ($rows as $row) {
                $invoice_increment_id = $row['increment_last_id'] + 1;
            }
        }

        // Open XML
        $import_names = preg_split('/\r\n|\r|\n/', $order->getStoreName());
        $website_name = isset($import_names[0]) ? $import_names[0] : '';
        $store_name = isset($import_names[1]) ? $import_names[1] : '';

        // Build DOMDocument on the fly
        $this->doc = new DOMDocument('1.0', "UTF-8");
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = true;

        // Root element
        $Reeleezee = $this->doc->createElementNS('http://www.reeleezee.nl/taxonomy/' . self::VERSION, 'Reeleezee');
        $Reeleezee = $this->doc->appendChild($Reeleezee);
        $Reeleezee->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $Reeleezee->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', 'http://www.reeleezee.nl/taxonomy/' . self::VERSION . ' taxonomy\\' . self::VERSION . '\Reeleezee.xsd');
        $attr = $this->doc->createAttribute('version');
        $attr->value = self::VERSION;
        $Reeleezee->appendChild($attr);

        // Reeleezee > Import
        $Import = $this->appendWrapper($Reeleezee, 'Import');

        // Reeleezee > Import > ExportInfo
        $ExportInfo = $Import->appendChild($this->doc->createElement('ExportInfo'));
        $Name = $this->appendChild($ExportInfo, 'Name', __('Import Invoice #%1 for Order #%2', $invoice_increment_id, $order->getIncrementId()));
        $Source = $this->appendChild($ExportInfo, 'Source', __('Magento - %1 - %2', $website_name, $store_name));
        $CreateDateTime = $this->appendChild($ExportInfo, 'CreateDateTime', date('c'));

        /**
         * 1. add/update RlzRelation
         */
        $billing = $invoice->getBillingAddress();
        $shipping = $invoice->getShippingAddress();
        if (!$shipping) {
            $shipping = $billing;
        }
        $customer_group_id = $billing->getOrder()->getData('customer_group_id');
        $email = $billing->getEmail() ? $billing->getEmail() : $billing->getEntityId() . '@localhost.com';

        // Generate "unique" integer based on email address,
        // used to avoid overriding pre-existing non-Magento customer IDs
        $invoice_customer_id = (int) abs(crc32($email));
        $invoice_customer_id = $this->trim_substr($invoice_customer_id, 0, self::MAXLENGTH_CID);

        // Reeleezee > Import > CustomerList > Customer
        $CustomerList = $this->appendWrapper($Import, 'CustomerList');
        $Customer = $this->appendWrapper($CustomerList, 'Customer');
        $attr = $this->doc->createAttribute('ID');
        $attr->value = $invoice_customer_id;
        $Customer->appendChild($attr);

        $ID = $this->appendChild($Customer, 'ID', $invoice_customer_id);

        // @FullName: NonEmpty, min0, MaxLenght50
        // @SearchName: NonEmpty, min0, MaxLength50
        $name = $billing->getFirstname() . ' ' . $billing->getLastname();
        $company = $billing->getCompany();
        if (strlen($company) >= self::MINLENGTH_COMPANY) {
            $rlz_name = $company;
        }
        elseif (strlen($name) >= self::MINLENGTH_NAME) {
            $rlz_name = $name;
        }
        else {
            $rlz_name = __('Nomen Nescio');
        }
        $FullName = $this->appendChild($Customer, 'FullName', $rlz_name);
        $SearchName = $this->appendChild($Customer, 'SearchName', $rlz_name);

        // @PhoneNumber, MinLenght1, MaxLength50
        $rlz_tel = preg_replace('/[^0-9]/', '', $billing->getTelephone());
        $PhoneNumber = $this->appendChild($Customer, 'PhoneNumber', $rlz_tel, true, self::MAXLENGTH_NAME);

        // @FaxNumber, MinLenght1, MaxLength50
        $rlz_fax = preg_replace('/[^0-9]/', '', $billing->getFax());
        $FaxNumber = $this->appendChild($Customer, 'FaxNumber', $rlz_fax, true, self::MAXLENGTH_NAME);

        // @EmailAddress, MinLenght3, MaxLength50
        $EmailAddress = $this->appendChild($Customer, 'EmailAddress', $email, true, self::MAXLENGTH_NAME);

        // @TaxDepositOBNumber
        // As of RLZ 1.23 now can import international VAT numbers
        // As of RLZ 1.24 now import VAT numbers without country prefix (subtr, 2, -1)
        $rlz_vat = false;
        $taxvat_customer = $account->getTaxvat();
        $taxvat_billing = $billing->getVatId();
        if (strlen($taxvat_billing) > 2) {
            $rlz_vat = $taxvat_billing;
        }
        elseif (strlen($taxvat_customer) > 2) {
            $rlz_vat = substr($taxvat_customer, 2);
        }

        // If VatNUmber is not valid, Reelezee import fails, so we do a check
        // @note this only checks the Dutch format, NLXXXXXXXXXBXX,
        // because Reeleezee currently only supports the dutch format
        if ($rlz_vat) {
            $TaxDepositOBNumber = $this->appendChild($Customer, 'TaxDepositOBNumber', $rlz_vat);
        }

        // @DefaultInvoiceDueDays, integer
        $rlz_due = $this->getConfigData('reeleezee/invoice/due_days', $store_id);
        if (empty($rlz_due) || $rlz_due <= 1) {
            $rlz_due = self::PAYMENT_DUE_DAYS_DEFAULT;
        }
        $DefaultInvoiceDueDays = $this->appendChild($Customer, 'DefaultInvoiceDueDays', $rlz_due);

        // Reeleezee > Import > CustomerList > Customer > AddressList
        $AddressList = $this->appendWrapper($Customer, 'AddressList');
        $ContactPersonList = $this->appendWrapper($Customer, 'ContactPersonList');

        // Delivery or Office address
        $shipCountry = $shipping->getCountryId();
        $rlz_address = $billing;
        $rlz_address_type = self::ADDRESS_TYPE_OFFICE;

        // Reeleezee > Import > CustomerList > Customer > AddressList > Address
        $Address = $this->appendWrapper($AddressList, 'Address');
        $attr = $this->doc->createAttribute('Type');
        $attr->value = $rlz_address_type;
        $Address->appendChild($attr);

        $street_address = $rlz_address->getStreet();
        $Street = $this->appendChild($Address, 'Street', implode(', ', $street_address), true, self::MAXLENGTH_STREET);
        $Zipcode = $this->appendChild($Address, 'Zipcode', $rlz_address->getPostcode(), true, self::MAXLENGTH_ZIPCODE);
        $City = $this->appendChild($Address, 'City', $rlz_address->getCity(), true, self::MAXLENGTH_CITY);
        $CountryCode = $this->appendChild($Address, 'CountryCode', $rlz_address->getCountryId(), true);

        /**
         * 2. add/update RlzSalesInvoice
         */
        $SalesInvoiceList = $this->appendWrapper($Import, 'SalesInvoiceList');

        // Decide invoice number type
        $rlz_number_type = $this->getConfigData('reeleezee/invoice/invoice_number', $store_id);
        switch ($rlz_number_type) {
            case 'invoice' :
                $rlz_number = str_replace('-', '', $invoice_increment_id);
                $created_at = $invoice->getCreatedAt();
                break;

            case 'order' :
                $rlz_number = str_replace('-', '', $order->getIncrementId());
                $created_at = $order->getCreatedAt();
                break;

            default :
                $rlz_number = FALSE;
                $created_at = $invoice->getCreatedAt();
        }

        // Creditmemo can't have custom invoice ID,
        // because it could override existing invoice/order ID
        if ($creditmemo) {
            $rlz_number = str_replace('-', '', $creditmemo->getIncrementId());
            $created_at = $creditmemo->getCreatedAt();
        }
        
        if ($rlz_number) {
            $rlz_number = preg_replace('/[^0-9]/', '', $rlz_number);
            $SalesInvoice = $this->appendWrapper($SalesInvoiceList, 'SalesInvoice');
            $attr = $this->doc->createAttribute('ReferenceNumber');
            $attr->value = $rlz_number;
            $SalesInvoice->appendChild($attr);

            $ReferenceNumber = $this->appendChild($SalesInvoice, 'ReferenceNumber', $rlz_number);
        }
        else {
            $SalesInvoice = $this->appendWrapper($SalesInvoiceList, 'SalesInvoice');
        }

        $ContactPersonReference = $this->appendWrapper($SalesInvoice, 'ContactPersonReference');
        $CustomerReference = $this->appendWrapper($SalesInvoice, 'CustomerReference');
        $attr = $this->doc->createAttribute('ID');
        $attr->value = $invoice_customer_id;
        $CustomerReference->appendChild($attr);

        $RecipientAddress = $this->appendWrapper($SalesInvoice, 'RecipientAddress');

        if (strlen($billing->getCompany()) > self::MINLENGTH_COMPANY) {
            $Name = $this->appendChild($RecipientAddress, 'Name', $billing->getCompany());
            $attentionField = 'AttentionOf';
        }
        else {
            $attentionField = 'Name';
        }
        $attention = $billing->getFirstname() . ' ' . $billing->getLastname();
        if (strlen($name) > self::MINLENGTH_NAME) {
            $rlz_name = $attention;
        }
        else {
            $rlz_name = __('Nomen Nescio');
        }
        $attentionField = $this->appendChild($RecipientAddress, $attentionField, $rlz_name, false, self::MAXLENGTH_NAME);

        $street_address = $billing->getStreet();
        $Street = $this->appendChild($RecipientAddress, 'Street', implode(', ', $street_address), false, self::MAXLENGTH_STREET);
        $Zipcode = $this->appendChild($RecipientAddress, 'Zipcode', $billing->getPostcode(), false, self::MAXLENGTH_ZIPCODE);
        $City = $this->appendChild($RecipientAddress, 'City', $billing->getCity(), false, self::MAXLENGTH_CITY);
        $CountryCode = $this->appendChild($RecipientAddress, 'CountryCode', $billing->getCountryId());

        // Reeleezee > Import > SalesInvoiceList > SalesInvoice
        $language_code = $this->getLanguageCode($store_id);
        $LanguageCode = $this->appendChild($SalesInvoice, 'LanguageCode', (!empty($language_code) ? $language_code : self::DEFAULT_LANGCODE), false, 2);
        $DocumentDate = $this->appendChild($SalesInvoice, 'DocumentDate', (!empty($created_at) ? $created_at : date('Y-m-d')), false, 10);
        $BookDate = $this->appendChild($SalesInvoice, 'BookDate', (!empty($created_at) ? $created_at : date('Y-m-d')), false, 10);
        $PaymentDueDate = $this->appendChild($SalesInvoice, 'PaymentDueDate', date('Y-m-d', strtotime('+' . $rlz_due . ' days')), false, 10);
        $IsVatIncludedInPrice = $this->appendChild($SalesInvoice, 'IsVatIncludedInPrice', ($this->getConfigData('tax/calculation/price_includes_tax', $store_id) ? 'true' : 'false'));
        $Status = $this->appendChild($SalesInvoice, 'Status', ($this->getConfigData('reeleezee/settings/invoice_status', $store_id) ? 'tentative' : 'open'));

        // Custom texts
        $invoice_header = $this->getConfigData('reeleezee/invoice/header', $store_id);
        if ($this->getConfigData('reeleezee/invoice/order_number', $store_id)) {
            $invoice_header .= ' - ' . __('Order') . ' #' . $order->getIncrementId();
        }

        // Credit memo
        if ($creditmemo) {
            if ($this->getConfigData('reeleezee/invoice/order_number', $store_id)) {
                $invoice_header .= ' - ' . __('Credit memo') . ' #' . $creditmemo->getIncrementId();
            }
        }

        $Header = $this->appendChild($SalesInvoice, 'Header', (!empty($invoice_header) ? $invoice_header : 'Header'), false, self::MAXLENGTH_DESCRIPTION);

        // Footer: show 'Paid with %1method' 
        $invoice_footer = __('Paid with %1', $invoice->getOrder()->getPayment()->getMethodInstance()->getTitle());
        $Footer = $this->appendChild($SalesInvoice, 'Footer', $invoice_footer, false, self::MAXLENGTH_DESCRIPTION);

        $invoice_bottomtext = $this->getConfigData('reeleezee/invoice/bottomtext', $store_id);
        $BottomText = $this->appendChild($SalesInvoice, 'BottomText', (!empty($invoice_bottomtext) ? $invoice_bottomtext : 'BottomText'), false, self::MAXLENGTH_DESCRIPTION);

        // Discount: @note amounts of 0 not allowed
        // But not for creditmemos
        if (!$creditmemo) {
            $discountAmount = round($invoice->getBaseDiscountAmount(), 2);
            if ($discountAmount != '0.00') {
                $Discount = $this->appendWrapper($SalesInvoice, 'Discount');
                $discount_amount = $invoice->getBaseDiscountAmount();

                // Use abs() to get absolute (non-negative) amount
                $Amount = $this->appendChild($Discount, 'Amount', abs($discount_amount));
                $Comment = $this->appendChild($Discount, 'Comment', false, true);
            }
        }

        // DocumentOrigin determines wether 'Invoice' or 'Booking event only'
        $DocumentOrigin = $this->appendChild($SalesInvoice, 'DocumentOrigin', $this->getConfigData('reeleezee/settings/booking_mode', $store_id));

        /**
         * 3. line items
         */
        $LineList = $this->appendWrapper($SalesInvoice, 'LineList');
        $count_line = -1;
        $break_after = $this->getConfigData('reeleezee/invoice/pagebreak', $store_id);
        if ($break_after < 0 || !is_numeric($break_after)) {
            $break_after = self::PAGE_BREAK_DEFAULT;
        }
        foreach ($items as $item) {
            $product = $_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
            $sku = $product->getSku();
            if ($item->getParentItemId() || empty($sku)) {
                continue;
            }

            $Line = $this->appendWrapper($LineList, 'Line');
            $Date = $this->appendChild($Line, 'Date', date('Y-m-d'));
            $Description = $this->appendChild($Line, 'Description', $product->getName(), false, self::MAXLENGTH_NAME);
            $Quantity = $this->appendChild($Line, 'Quantity', (int)$item->getQtyOrdered());
            $Unit = $this->appendChild($Line, 'Unit', $this->getUnitCode($product, $store_id));

            $ProductReference = $this->appendWrapper($Line, 'ProductReference');
            $attr = $this->doc->createAttribute('Code');
            $attr->value = $this->trim_substr(str_replace(' ', '', $sku), 0, self::MAXLENGTH_SKU);
            $ProductReference->appendChild($attr);

            if ((bool)$this->getConfigData('tax/calculation/price_includes_tax', $store_id)) {
                $price = $item->getBasePriceInclTax();
            }
            else {
                $price = $item->getBasePrice();
            }

            // Credit memo
            if ($creditmemo) {
                $price *= -1;
            }

            $Price = $this->appendChild($Line, 'Price', (!empty($price) ? $price : 0.0000));
            $VatCode = $this->appendChild($Line, 'VatCode', $this->getVatCode($billing->getCountryId(), $customer_group_id, $product, $store_id));

            $AccountReference = $this->appendWrapper($Line, 'AccountReference');
            $attr = $this->doc->createAttribute('Number');
            $attr->value = $this->getAccountReference($product, $store_id);
            $AccountReference->appendChild($attr);

            ++$count_line;
            if ($break_after > 0 && $count_line == $break_after) {
                $PageBreak = $this->appendChild($Line, 'PageBreak', 'true');
                $count_line = 0;
            }
            else {
                $PageBreak = $this->appendChild($Line, 'PageBreak', 'false');
            }
        }

        // Shipping cost
        if ($invoice->getBaseShippingInclTax() > 0) {
            $Line = $this->appendWrapper($LineList, 'Line');
            $Date = $this->appendChild($Line, 'Date', date('Y-m-d'));
            $Description = $this->appendChild($Line, 'Description', __('Shipping cost'), false, self::MAXLENGTH_NAME);
            $Quantity = $this->appendChild($Line, 'Quantity', 1);
            $Unit = $this->appendChild($Line, 'Unit', $this->getUnitCode('reeleezee/shipping/shipping_unit', $store_id));

            $ProductReference = $this->appendWrapper($Line, 'ProductReference');
            $attr = $this->doc->createAttribute('Code');
            $attr->value = $this->trim_substr($this->getConfigData('reeleezee/shipping/shipping_sku', $store_id), 0, self::MAXLENGTH_SKU);
            $ProductReference->appendChild($attr);

            // shipping tax setting must match product setting
            if ((bool)$this->getConfigData('tax/calculation/price_includes_tax', $store_id)) {
                $rlz_price = $invoice->getBaseShippingInclTax();
            }
            else {
                $rlz_price = $invoice->getBaseShippingAmount();
            }

            // Credit memo
            if ($creditmemo) {
                $rlz_price *= -1;
            }

            $Price = $this->appendChild($Line, 'Price', $rlz_price);
            $VatCode = $this->appendChild($Line, 'VatCode', $this->getShippingVatCode($customer_group_id, $store_id));

            $AccountReference = $this->appendWrapper($Line, 'AccountReference');
            $attr = $this->doc->createAttribute('Number');
            $attr->value = $this->getConfigData('reeleezee/shipping/shipping_account', $store_id);
            $AccountReference->appendChild($attr);

            $PageBreak = $this->appendChild($Line, 'PageBreak', 'false');
        }

        /**
         * 4. Add/update RlzProduct
         */
        $ProductList = $this->appendWrapper($Import, 'ProductList');

        $tracked_skus = array();
        foreach ($items as $item) {
            $product = $_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
            $sku = $product->getSku();
            if ($item->getParentItemId() || empty($sku)) {
                continue;
            }

            // Only pass unique SKUs in the ProductList
            if (!in_array($sku, $tracked_skus)) {
                $tracked_skus[] = $sku;
            }
            else {
                continue;
            }

            $Product = $this->appendWrapper($ProductList, 'Product');
            $attr = $this->doc->createAttribute('Code');
            $attr->value = $this->trim_substr(str_replace(' ', '', $sku), 0, self::MAXLENGTH_SKU);
            $Product->appendChild($attr);

            $Code = $this->appendChild($Product, 'Code', trim(str_replace(' ', '', $sku)), false, self::MAXLENGTH_SKU);
            $Name = $this->appendChild($Product, 'Name', $product->getName(), false, self::MAXLENGTH_NAME);
            $Description = $this->appendChild($Product, 'Description', $product->getName(), false, self::MAXLENGTH_DESCRIPTION);

            $product_description = $this->clean_html($product->getDescription());
            if ($product_description) {
                $Comment = $this->appendChild($Product, 'Comment', $product_description);
            }
            else {
                $Comment = $this->appendChild($Product, 'Comment', $product->getName());
            }

            // Import / override product prices?
            if ($this->getConfigData('reeleezee/settings/import_prices', $store_id)) {
                $Price = $this->appendChild($Product, 'Price', $item->getBasePrice());
                $CostPrice = $this->appendChild($Product, 'CostPrice', $item->getBaseCost());
            }

            $Unit = $this->appendChild($Product, 'Unit', $this->getUnitCode($product, $store_id));

            $AccountReference = $this->appendWrapper($Product, 'AccountReference');
            $attr = $this->doc->createAttribute('Number');
            $attr->value = $this->getAccountReference($product, $store_id);
            $AccountReference->appendChild($attr);

            $VatGroup = $this->appendChild($Product, 'VatGroup', $this->getVatGroup($product, $store_id));
            $VendorProductCode = $this->appendChild($Product, 'VendorProductCode', false, true);
            $Type = $this->appendChild($Product, 'Type', self::PRODUCT_TYPE);
            $Active = $this->appendChild($Product, 'Active', self::PRODUCT_ACTIVE);
        }

        // Output to screen
        if ($outputXML) {
            return $this->doc->saveXML();
        }

        // import via Soap
        $wdsl = self::WDSL;
        $client = new SoapClient($wdsl);

        // only show status message for admin
        $import_success = false;
        $adminSession = $_objectManager->create('Magento\Backend\Model\Auth\Session');
        try {
            $request = $client->Import(array(
                'userName' => $this->getConfigData('reeleezee/settings/username', $store_id),
                'password' => $this->getConfigData('reeleezee/settings/password', $store_id),
                'checkValidityOnly' => (bool)$this->getConfigData('reeleezee/settings/test_mode', $store_id),
                'xmlDocument' => $this->doc->saveXml(),
            ));

            // Debug logging by default
            $_message = 'store_id:' . $store_id
                . '|invoice_id:' . $invoice->getId()
                . '|order_id:' . $order->getId()
                . '|invoice_increment_id:' . $invoice_increment_id
                . '|order_increment_id:' . $order->getIncrementId()
                . '|debug_xml:' . $request->ImportResult . "\n------------------------------------\n" . $this->doc->saveXml();
            $this->log->addDebug($_message);
            
            if ($adminSession->isLoggedIn()) {
                $result = simplexml_load_string($request->ImportResult);
                if ($result->ImportResult['Succeeded'] == 'false') {
                    if ($outputMessage) {
                        $this->messageManager->addError(__('Failure: Reeleezee import failed. You need to enter this invoice manually in your Reeleezee administration.') . '<br />' . $result->ImportResult->MaxMessage);
                    }
                }
                else {
                    $import_success = true;
                    if ($outputMessage) {
                        $this->messageManager->addSuccess(__('Success: Reeleezee import succeeded.'));
                    }
                }
            }
        }
        catch (\Exception $e) {

            // Debug logging by default
            $_message = 'store_id:' . $store_id
                . '|invoice_id:' . $invoice->getId()
                . '|order_id:' . $order->getId()
                . '|invoice_increment_id:' . $invoice_increment_id
                . '|order_increment_id:' . $order->getIncrementId()
                . '|debug_xml:' . __('Exception: Reeleezee import failed. You need to enter this invoice manually in your Reeleezee administration.') . "\n------------------------------------\n" . $this->doc->saveXml();
            $this->log->addDebug($_message);
            
            if ($adminSession->isLoggedIn()) {
                if ($outputMessage) {
                    if (!extension_loaded('soap')) {
                        $this->messageManager->addError(__('PHP SOAP extension not found! Please install PHP SOAP on this server.'));
                    }
                    else {
                        $this->messageManager->addError(__('Exception: Reeleezee import failed. You need to enter this invoice manually in your Reeleezee administration.'));
                    }
                }
            }
        }

        return $import_success;
    }

    /**
     * Return language code nl, de, en etc.
     */
    public function getLanguageCode($store_id)
    {
        $code = explode('_', $this->getConfigData('general/locale/code', $store_id));
        return $code[0];
    }

    /**
     * Helper function for $this->doc->appendChild logic
     *
     * $Parent parent element
     * $Child string name
     * $value string
     */
    public function appendChild($Parent, $Child, $value = false, $notnil = false, $maxlen = 0)
    {
        // trim required (spaces not accepted as string)
        $value = trim($value);

        // May be nil, but is not empty
        if ($value || ($notnil && strlen($value) != 0)) {
            if ($maxlen > 0) {
                $value = $this->trim_substr($value, 0, $maxlen);
            }

            // Make sure to clean HTML after substr.
            $value = htmlspecialchars($value);
            $Element = $Parent->appendChild($this->doc->createElement($Child, $value));
        }

        // May be nil and is empty
        elseif ($notnil && strlen($value) == 0) {
            $Element = $Parent->appendChild($this->doc->createElement($Child));

            $attr = $this->doc->createAttribute('xsi:nil');
            $attr->value = 'true';

            $Element->appendChild($attr);
        }

        // May not be nil and is empty
        else {
            $Element = false;
        }
        return $Element;
    }

    /**
     * Trim after substr
     * Because Reeleezee doesn't accept perfixed or suffixed spaces on a strong
     */
    public function trim_substr($string, $start, $end)
    {
        return trim(substr($string, $start, $end));
    }

    /**
     * Clean html
     */
    public function clean_html($string)
    {
        return str_replace('&nbsp;', ' ', strip_tags($string));
    }

    /**
     * Helper function for $this->doc->appendChild logic
     *
     * $Parent parent element
     * $Child string name
     */
    public function appendWrapper($Parent, $Child)
    {
        $Element = $Parent->appendChild($this->doc->createElement($Child));
        return $Element;
    }

}
