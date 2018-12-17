<?php
/***
 * Copyright © Javed LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.javed.com | javed4php@gmail.com
*/

namespace Javed\GuestCheckoutCouponLimitation\Plugin\Customer\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Psr\Log\LoggerInterface as PsrLogger;

class AccountManagement
{

 public function __construct(
    StoreManagerInterface $storeManager,
    CustomerRepositoryInterface $customerRepository,
PsrLogger $logger
)
 {
    $this->storeManager = $storeManager;
     $this->customerRepository = $customerRepository;
      $this->logger = $logger;

 }

    public function beforeIsEmailAvailable(
        \Magento\Customer\Model\AccountManagement $subject,
        $customerEmail, $websiteId = null
    ) {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $shippingAddress = $cart->getQuote()->getShippingAddress();
      
        
       

        try {
           
             if ($shippingAddress && !$cart->getQuote()->getCustomerId() && $cart->getQuote()->getCustomerEmail()) {
                $shippingAddress->setData('email', $customerEmail);
                $shippingAddress->save();
            }
             $logger->info("javed=".$shippingAddress->getEmail());
        } catch (NoSuchEntityException $e) {
            $logger->info($e->getMessage());
        }

        try {
            if ($websiteId === null) {
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
            }
            $this->customerRepository->get($customerEmail, $websiteId);
            return false;
        } catch (NoSuchEntityException $e) {
            return true;
        }
    }

    
}