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

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * iDeal Response
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        if ($this->isRedirect()) {
            return false;
        }

        return !isset($this->data->Error);
    }

    public function isRedirect()
    {
        return false;
    }

    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            return (string) $this->data->order->URL;
        }
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }

    public function getTransactionReference()
    {
        if (isset($this->data->order)) {
            return (string) $this->data->order->transaction_id;
        }
    }

    public function getMessage()
    {
        if (isset($this->data->Error)) {
            $msg = (string) $this->data->Error->errorMessage;
            $detail = (string) $this->data->Error->errorDetail;

            return "$msg ($detail)";
        }
    }

    public function getCode()
    {
        if (isset($this->data->Error)) {
            return (string) $this->data->Error->errorCode;
        }
    }

    /**
     * Get an associateive array of banks returned from a fetchIssuers request
     */
    public function getIssuers()
    {
        if (isset($this->data->Directory)) {
            $issuers = array();

            foreach ($this->data->Directory->Country as $country) {
                foreach ($country->Issuer as $issuer) {
                    $id = (string) $issuer->issuerID;
                    $issuers[$id] = (string) $issuer->issuerName;
                }
            }

            return $issuers;
        }
    }
}
