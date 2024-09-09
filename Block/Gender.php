<?php
namespace Magenest\FullPageCache\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;

class Gender extends Template
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getCustomerGender()
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            return $customer->getGender();
        }
        return false;
    }

    public function getCurrentCustomer() {
        $customerId = $this->customerSession->getCustomer()->getId();
        if ($customerId) {
            return $customerId;
        }
        return null;
    }
}
