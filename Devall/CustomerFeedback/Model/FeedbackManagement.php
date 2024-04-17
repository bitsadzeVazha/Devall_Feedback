<?php
declare(strict_types=1);

/**
 * Feedback Management Class
 *
 * This class implements the FeedbackManagementInterface and is responsible for managing
 * feedback submissions via API. It defines the method required to submit customer feedback.
 *
 * @author Developers-Alliance team
 * @Copyright (c) 2024 Developers-alliance (https:// www. developers-alliance. com)
 * @website https://developers-alliance.com
 * @package Devall_CustomerFeedback
 * @version 1.0.0
 */

namespace Devall\CustomerFeedback\Model;

use Devall\CustomerFeedback\Api\FeedbackManagementInterface;
use Devall\CustomerFeedback\Api\Data\FeedbackInterface;
use Devall\CustomerFeedback\Api\FeedbackRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Escaper;
use Magento\Customer\Model\Session as CustomerSession;

class FeedbackManagement implements FeedbackManagementInterface
{
    /**
     * @var FeedbackRepositoryInterface Holds the repository instance for saving feedback data.
     */
    protected $feedbackRepository;

    /**
     * @var CustomerSession Manages customer session, used to check if the user is logged in.
     */
    protected $customerSession;

    /**
     * @var Escaper Utilized to escape HTML characters in user input to prevent XSS attacks.
     */
    protected $escaper;

    /**
     * Initializes the dependencies required to manage feedback submissions.
     *
     * @param FeedbackRepositoryInterface $feedbackRepository Repository to save feedback data.
     * @param CustomerSession $customerSession Session manager to handle user login state.
     * @param Escaper $escaper Utility to escape HTML entities in user inputs.
     */
    public function __construct(
        FeedbackRepositoryInterface $feedbackRepository,
        CustomerSession $customerSession,
        Escaper $escaper
    ) {
        $this->feedbackRepository = $feedbackRepository;
        $this->customerSession = $customerSession;
        $this->escaper = $escaper;
    }

    /**
     * Submits feedback data after validating user login status and sanitizing input data.
     *
     * @param FeedbackInterface $feedback Feedback object containing data to be processed.
     * @return string JSON encoded result with success status and message.
     */
    public function submitFeedback(FeedbackInterface $feedback)
    {
        try {
            if (!$this->customerSession->isLoggedIn()) {
                throw new CouldNotSaveException(__('You need to be logged in to submit feedback.'));
            }

            $title = $this->escaper->escapeHtml($feedback->getTitle());
            $feedbackContent = $this->escaper->escapeHtml($feedback->getFeedback());
            $productId = filter_var($feedback->getProductId(), FILTER_VALIDATE_INT);
            $submitAnonymously = filter_var($feedback->getAnonymous(), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            $feedback->setTitle($title);
            $feedback->setFeedback($feedbackContent);
            $feedback->setProductId($productId);
            $feedback->setAnonymous($submitAnonymously);

            if (!$submitAnonymously && $this->customerSession->isLoggedIn()) {
                $customer = $this->customerSession->getCustomer();
                $feedback->setFirstname($customer->getFirstname());
                $feedback->setLastname($customer->getLastname());
                $feedback->setEmail($customer->getEmail());
            } else {
                $feedback->setFirstname(null);
                $feedback->setLastname(null);
                $feedback->setEmail(null);
            }

            $this->feedbackRepository->save($feedback);
            return json_encode(['success' => true, 'message' => 'Feedback submitted successfully']);
        } catch (LocalizedException $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
