<?php

namespace Devall\CustomerFeedback\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * ViewModel for the Feedback Form.
 *
 * Provides necessary methods to interact with the feedback form elements such as retrieving form keys
 * and checking if the feedback functionality is enabled in the store configuration.
 */
class FeedbackFormViewModel implements ArgumentInterface
{
    /**
     * @var Registry Registry manager to access Magento registry system
     */
    private $registry;

    /**
     * @var FormKey Utility to generate a unique form key for CSRF protection
     */
    private $formKey;

    /**
     * @var ScopeConfigInterface Interface for configuration access, used here to check module settings
     */
    private $scopeConfig;

    /**
     * Constructor for FeedbackFormViewModel.
     *
     * Initializes the registry, form key generator, and scope configuration interface to be used
     * in other methods.
     *
     * @param Registry $registry Magento registry instance
     * @param FormKey $formKey Form key generator for security
     * @param ScopeConfigInterface $scopeConfig Config access for checking module settings
     */
    public function __construct(
        Registry $registry,
        FormKey $formKey,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->registry = $registry;
        $this->formKey = $formKey;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieves the unique form key required for form submission.
     *
     * This method utilizes Magento's built-in CSRF protection mechanism to generate a secure form key.
     *
     * @return string The generated form key
     */
    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Determines whether the feedback functionality is enabled in the store configuration.
     *
     * This method checks the configuration under the specified path to see if the feedback module
     * is enabled for the store view.
     *
     * @return bool True if feedback is enabled, otherwise false
     */
    public function isFeedbackEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'devall_feedback/settings/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
