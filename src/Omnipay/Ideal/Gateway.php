<?php

/*
 * This file is part of the Omnipay package.
 *
 * (c) Adrian Macneil <adrian@adrianmacneil.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omnipay\Ideal;

use Omnipay\Common\AbstractGateway;

/**
 * iDeal Gateway
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'iDeal';
    }

    public function getDefaultParameters()
    {
        return array(
            'acquirer' => array('', 'ing', 'rabobank'),
            'merchantId' => '',
            'publicKeyPath' => '',
            'privateKeyPath' => '',
            'privateKeyPassphrase' => '',
            'testMode' => false,
        );
    }

    public function getAcquirer()
    {
        return $this->getParameter('acquirer');
    }

    public function setAcquirer($value)
    {
        return $this->setParameter('acquirer', $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getSubId()
    {
        return $this->getParameter('subId');
    }

    public function setSubId($value)
    {
        return $this->setParameter('subId', $value);
    }

    public function getPublicKeyPath()
    {
        return $this->getParameter('publicKeyPath');
    }

    public function setPublicKeyPath($value)
    {
        return $this->setParameter('publicKeyPath', $value);
    }

    public function getPrivateKeyPath()
    {
        return $this->getParameter('privateKeyPath');
    }

    public function setPrivateKeyPath($value)
    {
        return $this->setParameter('privateKeyPath', $value);
    }

    public function getPrivateKeyPassphrase()
    {
        return $this->getParameter('privateKeyPassphrase');
    }

    public function setPrivateKeyPassphrase($value)
    {
        return $this->setParameter('privateKeyPassphrase', $value);
    }

    public function fetchIssuers(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ideal\Message\FetchIssuersRequest', $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ideal\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ideal\Message\CompletePurchaseRequest', $parameters);
    }
}
