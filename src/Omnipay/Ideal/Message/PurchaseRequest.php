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
 * iDeal Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('issuer', 'amount', 'currency', 'returnUrl');

        $data = $this->getBaseData('AcquirerTrxReq');
        $data->Issuer->issuerId = $this->getIssuer();
        $data->Merchant->merchantReturnURL = $this->getReturnUrl();
        $data->Transaction->purchaseID = $this->getTransactionId();
        $data->Transaction->amount = $this->getAmountDecimal();
        $data->Transaction->currency = $this->getCurrency();
        $data->Transaction->expirationPeriod = static::EXPIRATION_PERIOD;
        $data->Transaction->language = static::LANGUAGE;
        $data->Transaction->description = $this->getDescription();
        $data->Transaction->entranceCode = sha1(uniqid());

        return $data;
    }
}
