<?php

/*
 * This file is part of the Omnipay package.
 *
 * (c) Adrian Macneil <adrian@adrianmacneil.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omnipay\Ideal\Message;

/**
 * iDeal Complete Purchase Request
 */
class CompletePurchaseRequest extends AbstractRequest
{
    public function getData()
    {
    	$this->validate('transactionId');

        $data = $this->getBaseData('AcquirerStatusReq');
        $data->Merchant->merchantID = $this->getMerchantId();
        $data->Merchant->subID = $this->getSubId();
        $data->Transaction->transactionID = $this->getTransactionId();
        
        return $data;
    }
}
