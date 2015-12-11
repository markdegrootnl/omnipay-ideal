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

use DOMDocument;
use DOMNode;
use DOMXPath;
use SimpleXMLElement;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * iDeal Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const LANGUAGE = 'nl';
    const EXPIRATION_PERIOD = 'PT10M';
    const IDEAL_VERSION = '3.3.1';
    const IDEAL_NS = 'http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1';
    const XMLDSIG_NS = 'http://www.w3.org/2000/09/xmldsig#';

    protected $endpoints = [
        'abnamro' => [
            'production' => 'https://abnamro.ideal-payment.de/ideal/iDEALv3',
            'test' => 'https://abnamro-test.ideal-payment.de/ideal/iDEALv3'
        ],
        'ing' => [
            'production' => 'https://ideal.secure-ing.com/ideal/iDEALv3',
            'test' => 'https://idealtest.secure-ing.com/ideal/iDEALv3'
        ],
        'rabobank' => [
            'production' => 'https://ideal.rabobank.nl/ideal/iDEALv3',
            'test' => 'https://idealtest.rabobank.nl/ideal/iDEALv3'
        ]
    ];

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

    public function getIssuer()
    {
        return $this->getParameter('issuer');
    }

    public function setIssuer($value)
    {
        return $this->setParameter('issuer', $value);
    }

    protected function getBaseData($action)
    {
        $this->validate('acquirer', 'testMode', 'merchantId', 'subId', 'publicKeyPath', 'privateKeyPath', 'privateKeyPassphrase');
        
        $data = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><$action />");
        $data->addAttribute('xmlns', static::IDEAL_NS);
        $data->addAttribute('version', static::IDEAL_VERSION);
        $data->createDateTimestamp = gmdate('Y-m-d\TH:i:s.000\Z');

        return $data;
    }

    /*
    * This function overwrites the \Omnipay\Common\Message\AbstractRequest::validate() function
    * to fix: https://github.com/thephpleague/omnipay-common/issues/13
    * and can be removed once the issue is fixed upstream.
    */
    public function validate()
    {
        foreach (func_get_args() as $key) {
            $value = $this->parameters->get($key);
            if (empty($value) && $value !== '0' && $value !== false) {
                throw new InvalidRequestException("The $key parameter is required");
            }
        }
    }

    /**
     * Sign an XML request
     *
     * @param string
     * @return string
     */
    public function signXML($data)
    {
        $xml = new DOMDocument;
        $xml->preserveWhiteSpace = false;
        $xml->loadXML($data);

        $sig = new DOMDocument;
        $sig->preserveWhiteSpace = false;
        $sig->loadXML(
            '<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
                <SignedInfo>
                    <CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
                    <SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/>
                    <Reference URI="">
                        <Transforms>
                            <Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
                        </Transforms>
                        <DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                        <DigestValue/>
                    </Reference>
                </SignedInfo>
                <SignatureValue/>
                <KeyInfo><KeyName/></KeyInfo>
            </Signature>'
        );

        $sig = $xml->importNode($sig->documentElement, true);
        $xml->documentElement->appendChild($sig);

        // calculate digest
        $xpath = $this->getXPath($xml);
        $digestValue = $xpath->query('ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestValue')->item(0);
        $digestValue->nodeValue = $this->generateDigest($xml);

        // calculate signature
        $signedInfo = $xpath->query('ds:Signature/ds:SignedInfo')->item(0);
        $signatureValue = $xpath->query('ds:Signature/ds:SignatureValue')->item(0);
        $signatureValue->nodeValue = $this->generateSignature($signedInfo);

        // add key reference
        $keyName = $xpath->query('ds:Signature/ds:KeyInfo/ds:KeyName')->item(0);
        $keyName->nodeValue = $this->getPublicKeyDigest();

        return $xml->saveXML();
    }

    /**
     * Generate sha256 digest of xml
     *
     * @param DOMNode
     * @return string
     */
    public function generateDigest(DOMDocument $xml)
    {
        $xml = $xml->cloneNode(true);

        // strip Signature
        foreach ($this->getXPath($xml)->query('ds:Signature') as $node) {
            $node->parentNode->removeChild($node);
        }

        $message = $this->c14n($xml);

        return base64_encode(hash('sha256', $message, true));
    }

    /**
     * Generate RSA signature of SignedInfo element
     *
     * @param DOMNode
     * @return string
     */
    public function generateSignature(DOMNode $xml)
    {
        $message = $this->c14n($xml);

        $key = openssl_get_privatekey('file://'.$this->getPrivateKeyPath(), $this->getPrivateKeyPassphrase());
        if ($key && openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA256)) {
            openssl_free_key($key);

            return base64_encode($signature);
        }

        $error = 'Invalid private key.';
        while ($msg = openssl_error_string()) {
            $error .= ' '.$msg;
        }

        throw new InvalidRequestException($error);
    }

    /**
     * Exclusive XML canonicalization
     *
     * @link http://www.w3.org/2001/10/xml-exc-c14n
     */
    protected function c14n(DOMNode $xml)
    {
        return $xml->C14N(true, false);
    }

    protected function getXPath(DOMDocument $xml)
    {
        $xpath = new DOMXPath($xml);
        $xpath->registerNamespace('ds', static::XMLDSIG_NS);
        $xpath->registerNamespace('ideal', static::IDEAL_NS);

        return $xpath;
    }

    public function getPublicKeyDigest()
    {
        if (openssl_x509_export('file://'.$this->getPublicKeyPath(), $cert)) {
            $cert = str_replace(array('-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----'), '', $cert);

            return strtoupper(sha1(base64_decode($cert)));
        }

        throw new InvalidRequestException("Invalid public key");
    }

    public function send()
    {
        $data = $this->signXML($this->getData()->asXML());
        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $data)->send();

        return $this->response = $this->parseResponse($this, $httpResponse->xml());
    }

    public function sendData($data){
        throw new Exception('This method is not implemented.');
    }

    public abstract function parseResponse(\Omnipay\Common\Message\RequestInterface $request, $data);

    public function getEndpoint()
    {
        $this->validate('acquirer');
        $environment = $this->getTestMode() ? 'test' : 'production';

        if (array_key_exists($acquirer = $this->getAcquirer(), $this->endpoints)) {
            return $this->endpoints[$acquirer][$environment];
        }

        throw new InvalidRequestException('Invalid acquirer selected');
    }
}
