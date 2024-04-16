<?php

/**
 * Handles the submission of customer feedback.
 *
 * @author Developers-Alliance team
 * Copyright (c) 2024 Developers-alliance (https:// www. developers-alliance. com)
 * @package Devall_CustomerFeedback
 * @version 1.0.0
 *
 * This controller manages the actions required to process customer feedback submissions,
 * including validation of the customer's session and form key, processing the feedback data,
 * and sending notifications.
 */

namespace Devall\CustomerFeedback\Controller\Submit;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Devall\CustomerFeedback\Model\FeedbackDataHandler;
use Devall\CustomerFeedback\Model\FeedbackNotifier;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Customer\Model\Session as CustomerSession;

class Save extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var FeedbackDataHandler
     */
    protected $feedbackDataHandler;

    /**
     * @var FeedbackNotifier
     */
    protected $feedbackNotifier;

    /**
     * @var FormKeyValidator
     */
    protected $formKeyValidator;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * Constructor.
     * Initializes core class dependencies.
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        FeedbackDataHandler $feedbackDataHandler,
        FeedbackNotifier $feedbackNotifier,
        FormKeyValidator $formKeyValidator,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->feedbackDataHandler = $feedbackDataHandler;
        $this->feedbackNotifier = $feedbackNotifier;
        $this->formKeyValidator = $formKeyValidator;
        $this->_customerSession = $customerSession;
    }

    /**
     * Execute the action.
     *
     * Validates the session and form key, processes feedback, and returns a JSON response.
     */
    public function execute() {
        $result = $this->resultJsonFactory->create();

        if (!$this->_customerSession->isLoggedIn()) {
            return $result->setData(['success' => false, 'message' => 'You need to be logged in to submit feedback.']);
        }

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $result->setData(['success' => false, 'message' => 'Invalid form key. Please refresh the page and try again.']);
        }

        try {
            $feedback = $this->feedbackDataHandler->handle($this->getRequest());
            $this->feedbackNotifier->notify($feedback);
            return $result->setData(['success' => true, 'message' => 'Your feedback has been submitted!']);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
