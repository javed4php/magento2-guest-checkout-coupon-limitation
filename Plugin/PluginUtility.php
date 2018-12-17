<?php
/***
 * Copyright © Javed LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.javed.com | javed4php@gmail.com
*/

namespace Javed\GuestCheckoutCouponLimitation\Plugin;

class PluginUtility
{
    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @var \Javed\GuestCheckoutCouponLimitation\Helper\Data
     */
    protected $helperData;

    /**
     * PluginUtility constructor.
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Javed\GuestCheckoutCouponLimitation\Helper\Data $helperData
     */
    public function __construct(
       \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Javed\GuestCheckoutCouponLimitation\Helper\Data $helperData,
         \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Psr\Log\LoggerInterface $logger
        
    ) {
        $this->couponFactory = $couponFactory;
        $this->helperData = $helperData;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
         
       
    }
    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $rule
     * @param $address
     * @return bool
     * @throws \Exception
     */
    public function aroundCanProcessRule($subject, \Closure $proceed, $rule, $address)
    {
         $result = $proceed($rule, $address);
        $quote      = $address->getQuote();
        $couponCode = $quote->getCouponCode();
        if ($this->helperData->isEnabled() && $couponCode) {
            $coupon = $this->couponFactory->create();
            $coupon->load($couponCode, 'code');
            if ($coupon->getId() && $rule->getUsesPerCoupon() && $coupon->getTimesUsed() >= $rule->getUsesPerCoupon()) {
                $result = false;
            }
            
         if (!$quote->getCustomerId() && !$quote->getCustomerEmail() && $quote->getShippingAddress()->getEmail()) {
              
              $searchCriteria = $this->searchCriteriaBuilder
                                      ->addFilter('customer_email', $quote->getShippingAddress()->getEmail(), 'eq')
                                      ->addFilter('coupon_code', $couponCode, 'eq')
                                      ->create();
                                      
              $orders = $this->orderRepository->getList($searchCriteria);
            
              if( $orders->getSize() >= $rule->getUsesPerCustomer() ){
                      $result = false;
                }
        }
    }
       return $result;
    }
}
