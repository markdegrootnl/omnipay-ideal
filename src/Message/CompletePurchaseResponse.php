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
class CompletePurchaseResponse extends AbstractResponse
{

    public function rootElementExists(){
        return isset($this->data->Transaction);
    }

    public function hasErrors() {
        return isset($this->data->Error);
    }

    public function getErrorDetails() {
        if ($this->hasErrors()) {
            return (array)$this->data->Error;
        }
    }

    public function getTransaction(){
            return $this->data->Transaction;
    }

    public function getTransactionID(){
        if (isset($this->data->Transaction)) {
            return (string)$this->data->Transaction->transactionID;
        }
    }

    public function getStatus(){
        if (isset($this->data->Transaction)) {
            return (string)$this->data->Transaction->status;
        }
    }

    public function getStatusDateTimestamp(){
        if (isset($this->data->Transaction)) {
            return (string)$this->data->Transaction->statusDateTimestamp;
        }
    }

    public function getConsumerName(){
        if (isset($this->data->Transaction)) {
            return (string)$this->data->Transaction->consumerName;
        }
    }

    public function getConsumerIBAN(){
        if (isset($this->data->Transaction)) {
            return (string)$this->data->Transaction->consumerIBAN;
        }
    }

    public function getConsumerBIC(){
        if (isset($this->data->Transaction)) {
            return (string)$this->data->Transaction->consumerBIC;
        }
    }

    public function getAmount(){
        if (isset($this->data->Transaction)) {
            return (string)$this->data->Transaction->amount;
        }
    }

    public function getCurrency(){
        if (isset($this->data->Transaction)) {
            return (string)$this->data->Transaction->currency;
        }
    }
}
