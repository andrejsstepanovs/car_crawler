<?php

namespace AppBundle\Services\Site;

use AppBundle\Entity\Url;
use GuzzleHttp\Client;
use Symfony\Component\Validator\Validator\RecursiveValidator;


abstract class SiteAbstract
{
    /** @var RecursiveValidator */
    protected $validator;

    /** @var string */
    protected $userAgent;

    /** @var Url */
    protected $url;

    public function __construct(RecursiveValidator $validator, $userAgent)
    {
        $this->validator = $validator;
        $this->userAgent = $userAgent;
    }

    /**
     * @return Url
     */
    protected function getUrl()
    {
        return $this->url;
    }

    protected function setUrl(Url $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    protected function getResponse()
    {
        $errors = $this->validator->validate($this->getUrl());
        if ($errors->count()) {
            return false;
        }

        $client   = new Client();
        $params   = ['headers' => ['User-Agent' => $this->userAgent]];
        $response = $client->request('GET', $this->getUrl()->getUrl(), $params);
        return $response;
    }
}