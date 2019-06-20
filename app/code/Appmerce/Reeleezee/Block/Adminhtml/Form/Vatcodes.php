<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Block\Adminhtml\Form;

use Appmerce\Reeleezee\Model\Source\Vatcode;

/**
 * HTML select element block with vat code options
 */
class Vatcodes extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Vat codes cache
     *
     * @var array
     */
    private $_vatCodes;

    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addVatCodeAllOption = true;

    /**
     * Retrieve allowed vat codes
     *
     * @param int $classId  return name by customer group id
     * @return array|string
     */
    protected function _getVatCodes($classId = null)
    {
        if ($this->_vatCodes === null) {
            $vatCode = new Vatcode;
            $this->_vatCodes = $vatCode->toOptionArray();
        }
        if (!is_null($classId)) {
            return isset($this->_vatCodes[$classId]) ? $this->_vatCodes[$classId] : null;
        }
        return $this->_vatCodes;
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
            if ($this->_addVatCodeAllOption) {
                $this->addOption(
                    'default',
                    __('-- Choose --')
                );
            }
            foreach ($this->_getVatCodes() as $vatCode) {
                $this->addOption($vatCode['value'], addslashes($vatCode['label']));
            }
        }
        return parent::_toHtml();
    }

}
