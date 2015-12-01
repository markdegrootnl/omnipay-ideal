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
 * iDeal Fetch Issuers Request
 */
class FetchIssuersRequest extends AbstractRequest
{
    public function getData()
    {
    	$data = $this->getBaseData('DirectoryReq');
        $data->Merchant->merchantID = $this->getMerchantId();
        $data->Merchant->subID = $this->getSubId();
        return $data;
    }

    public function parseResponse(\Omnipay\Common\Message\RequestInterface $request, $data){
    	return new FetchIssuersResponse($request, $data);
    }
}
