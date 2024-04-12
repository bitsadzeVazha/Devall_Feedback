<?php

declare(strict_types=1);

/**
 * Handles the data operations for customer feedback submission.
 *
 * @author Devall
 * @website https://developers-alliance.com
 * @package Devall_CustomerFeedback
 * @version 1.0.0
 *
 * This class is responsible for processing the feedback form data,
 * validating and sanitizing it, and saving the feedback through the repository.
 */
namespace Devall\CustomerFeedback\Model;

use Magento\Framework\App\RequestInterface;
use Devall\CustomerFeedback\Api\FeedbackRepositoryInterface;
use Devall\CustomerFeedback\Model\FeedbackFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Escaper;
use Magento\Customer\Model\Session as CustomerSession;

class FeedbackDataHandler
{
    /**
     * @var FeedbackRepositoryInterface
     */
    protected $_feedbackRepository;

    /**
     * @var FeedbackFactory
     */
    protected $_feedbackFactory;

    /**
     * @var Escaper
     */
    protected $_escaper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Constructor method initializing dependencies.
     *
     * @param FeedbackRepositoryInterface $feedbackRepository Repository interface for feedback.
     * @param FeedbackFactory $feedbackFactory Factory for creating feedback instances.
     * @param Escaper $escaper Utility for escaping HTML entities.
     * @param CustomerSession $customerSession Customer session for retrieving logged-in customer data.
     */
    public function __construct(
        FeedbackRepositoryInterface $feedbackRepository,
        FeedbackFactory $feedbackFactory,
        Escaper $escaper,
        CustomerSession $customerSession
    ) {
        $this->_feedbackRepository = $feedbackRepository;
        $this->_feedbackFactory = $feedbackFactory;
        $this->_escaper = $escaper;
        $this->customerSession = $customerSession;
    }

    /**
     * Handles the request for feedback submission.
     *
     * Validates and sanitizes input data from the request, creates a feedback instance,
     * and saves it using the feedback repository.
     *
     * @param RequestInterface $request The request object containing submitted data.
     * @return Feedback The saved feedback model instance.
     * @throws LocalizedException If validation fails or feedback cannot be saved.
     */
    public function handle(RequestInterface $request) {
        $data = $request->getPostValue();

        if (empty($data)) {
            throw new LocalizedException(__('Invalid form data.'));
        }

        $cleanTitle = trim($this->_escaper->escapeHtml($data['title']));
        $cleanFeedback = trim($this->_escaper->escapeHtml($data['feedback']));
        $cleanProductId = filter_var($data['product_id'], FILTER_VALIDATE_INT);
        $cleanSubmitAnonymously = filter_var($data['submitAnonymously'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (empty($cleanTitle) || strlen($cleanTitle) > 255) {
            throw new LocalizedException(__('Title is required and cannot exceed 255 characters.'));
        }

        if (empty($cleanFeedback)) {
            throw new LocalizedException(__('Feedback is required.'));
        }

        if ($cleanProductId === false) {
            throw new LocalizedException(__('Invalid product ID.'));
        }

        if ($cleanSubmitAnonymously === null) {
            throw new LocalizedException(__('Invalid value for Submit Anonymously.'));
        }

        $feedback = $this->_feedbackFactory->create();
        $feedback->setTitle($cleanTitle);
        $feedback->setFeedback($cleanFeedback);
        $feedback->setProductId($cleanProductId);
        $feedback->setAnonymous($cleanSubmitAnonymously);

        if (!$feedback->getAnonymous() && $this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            $feedback->setFirstname($customer->getFirstname());
            $feedback->setLastname($customer->getLastname());
            $feedback->setEmail($customer->getEmail());
        } else {
            $feedback->setFirstname(null);
            $feedback->setLastname(null);
            $feedback->setEmail(null);
        }

        $this->_feedbackRepository->save($feedback);

        return $feedback;
    }
}
