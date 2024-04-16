<?php

/**
 * Feedback Form Block
 *
 * @author Developers-Alliance team
 * @Copyright (c) 2024 Developers-alliance (https:// www. developers-alliance. com)
 * @package Devall_CustomerFeedback
 * @version 1.0.0
 *
 * This block class is responsible for preparing data related to the Feedback form.
 */

namespace Devall\CustomerFeedback\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\App\Config\ScopeConfigInterface;
class FeedbackForm extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var FormKey
     */
    protected $formKey;

    protected $scopeConfig;

    /**
     * FeedbackForm constructor.
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param FormKey $formKey
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        FormKey $formKey,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->formKey = $formKey;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get the current product.
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Get the form key.
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function isFeedbackEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'devall_feedback/settings/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
