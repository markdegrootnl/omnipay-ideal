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

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * iDeal Response
 */
abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{

    public function isSuccessful()
    {
        return !isset($this->data->Error) && isset($this->data->Acquirer) && $this->rootElementExists();
    }

    public abstract function rootElementExists();

    public function getAcquirerID()
    {
        if (isset($this->data->Acquirer)) {
            return (string)$this->data->Acquirer->acquirerID;
        }
    }

    public function getData() {
        return $this->data;
    }

    public function getError() {
        return $this->data->Error;
    }

    public function getErrorCode() {
        if (isset($this->data->Error)) {
            return (string)$this->data->Error->errorCode;
        }
    }

    public function getErrorMessage() {
        if (isset($this->data->Error)) {
            return (string)$this->data->Error->errorMessage;
        }
    }

    public function getErrorDetail() {
        if (isset($this->data->Error)) {
            return (string)$this->data->Error->errorDetail;
        }
    }

    public function getConsumerMessage() {
        if (isset($this->data->Error)) {
            return (string)$this->data->Error->consumerMessage;
        }
    }
}
