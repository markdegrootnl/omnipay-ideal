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
class PurchaseResponse extends AbstractResponse
{

	public function rootElementExists(){
        return isset($this->data->Transaction) && isset($this->data->Issuer);
    }

    public function getIssuer() {
		return $this->data->Issuer;
	}

	public function getTransaction(){
		return $this->data->Transaction;
	}

	public function getIssuerAuthenticationURL() {
		if (isset($this->data->Issuer)) {
			return (string)$this->data->Issuer->issuerAuthenticationURL;
		}
	}

	public function getTransactionID(){
		if (isset($this->data->Transaction)) {
			return (string)$this->data->Transaction->transactionID;
		}
	}

	public function getTransactionCreateDateTimestamp() {
		if (isset($this->data->Transaction)) {
			return (string)$this->data->Transaction->transactionCreateDateTimestamp;
		}
	}

	public function getPurchaseID() {
		if (isset($this->data->Transaction)) {
			return (string)$this->data->Transaction->purchaseID;
		}
	}

}
