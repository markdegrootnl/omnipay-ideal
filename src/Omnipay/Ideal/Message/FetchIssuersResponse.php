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
 * iDeal Response
 */
class FetchIssuersResponse extends AbstractResponse
{
    public function rootElementExists(){
        return isset($this->data->Directory);
    }

    public function getDirectory() {
        return $this->data->Directory;
    }
    
    public function getIssuers() {
        if (isset($this->data->Directory)) {
            $issuers = array();

            foreach ($this->data->Directory->Country as $country) {
                foreach ($country->Issuer as $issuer) {
                    $id = (string) $issuer->issuerID;
                    $issuers[(string)$country->countryNames][$id] = (string) $issuer->issuerName;
                }
            }

            return $issuers;
        }
    }
}
