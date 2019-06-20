<?php
/**
 * Copyright © 2019 Appmerce - Applications for Ecommerce
 * http://www.appmerce.com
 */
namespace Appmerce\Reeleezee\Block\Adminhtml\Form;

/**
 * HTML select element block with country code options
 */
class Countrycodes extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Country codes cache
     *
     * @var array
     */
    private $_countryCodes;
    
    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addCountryCodeAllOption = true;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $_directoriesFactory;
    
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $directoriesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $directoriesFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_directoriesFactory = $directoriesFactory;
    }
    
    /**
     * Retrieve allowed country codes
     *
     * @param int $groupId return name by class Id
     * @return array|string
     */
    protected function _getCountryCodes($classId = null)
    {
        if ($this->_countryCodes === null) {
            $this->_countryCodes = $this->_directoriesFactory->create()->load()->toOptionArray(false);
            array_unshift($this->_countryCodes, ['value' => '', 'label' => __('All Countries')]);
        }
        if ($classId !== null) {
            return isset($this->_countryCodes[$classId]) ? $this->_countryCodes[$classId] : null;
        }
        return $this->_countryCodes;
    }

    /**
     * Sets name for input element
     *
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
            if ($this->_addCountryCodeAllOption) {
                $this->addOption(
                    'default',
                    __('-- Choose --')
                );
            }
            foreach ($this->_getCountryCodes() as $classId => $classLabel) {
                $this->addOption($classId, addslashes($classLabel));
            }
        }
        return parent::_toHtml();
    }

}
