<?php
/**
 * Feedback Collection
 *
 * @author Developers-Alliance team
 * @Copyright (c) 2024 Developers-alliance (https:// www. developers-alliance. com)
 * @website https://developers-alliance.com
 * @package Devall_CustomerFeedback
 * @version 1.0.0
 *
 * This class serves as the Collection for the Feedback model.
 */
namespace Devall\CustomerFeedback\Model\ResourceModel\Feedback;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Devall\CustomerFeedback\Model\Feedback;
use Devall\CustomerFeedback\Model\ResourceModel\Feedback as FeedbackResource;

class Collection extends AbstractCollection
{
    /**
     * Initialize Feedback Collection
     *
     * The function is responsible for setting the model and resource model
     * information for the Feedback Collection.
     */
    protected function _construct()
    {
        $this->_init(Feedback::class, FeedbackResource::class);
    }
}
