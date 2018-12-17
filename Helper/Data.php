<?php
/***
 * Copyright © Javed LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.javed.com | javed4php@gmail.com
*/

namespace Javed\GuestCheckoutCouponLimitation\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ACTIVE = 'javed_checkout/guestcheckoutcouponlimitation/active';

    /**
     * Is active
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
