<?php
namespace Magenest\FullPageCache\Plugin\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Helper\View;

class CustomerPlugin
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var View
     */
    protected $customerViewHelper;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param View $customerViewHelper
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        View $customerViewHelper
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerViewHelper = $customerViewHelper;
    }

    /**
     * Plugin after for getSectionData
     *
     * @param \Magento\Customer\CustomerData\Customer $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(
        \Magento\Customer\CustomerData\Customer $subject,
        array $result
    ) {
        if (!empty($result)) {
            $customer = $this->currentCustomer->getCustomer();
            $result['genderId'] = $customer->getGender();
        }

        return $result;
    }
}
