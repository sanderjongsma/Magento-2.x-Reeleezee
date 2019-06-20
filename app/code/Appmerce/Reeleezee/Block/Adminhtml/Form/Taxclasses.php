<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Block\Adminhtml\Form;

/**
 * HTML select element block with tax class options
 */
class Taxclasses extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Tax classes cache
     *
     * @var array
     */
    private $_taxClasses;

    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addTaxClassAllOption = true;

    /**
     * @var \Magento\Tax\Model\TaxClass\Source\Product
     */
    protected $productTaxClassSource;
    
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productTaxClassSource = $productTaxClassSource;
    }
    
    /**
     * Retrieve allowed tax classes
     *
     * @param int $classId  return name by customer group id
     * @return array|string
     */
    protected function _getTaxClasses($classId = null)
    {
        if ($this->_taxClasses === null) {
            $this->_taxClasses = $this->productTaxClassSource->getAllOptions(false);
        }
        if (!is_null($classId)) {
            return isset($this->_taxClasses[$classId]) ? $this->_taxClasses[$classId] : null;
        }
        return $this->_taxClasses;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            if ($this->_addTaxClassAllOption) {
                $this->addOption(
                    'default',
                    __('-- Choose --')
                );
            }
            foreach ($this->_getTaxClasses() as $taxClass) {
                $this->addOption($taxClass['value'], addslashes($taxClass['label']));
            }
        }
        return parent::_toHtml();
    }

}
