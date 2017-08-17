<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class Url
{
    /**
     * @Assert\Url()
     */
    public $url;

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrlHost()
    {
        $data = parse_url($this->getUrl());
        return $data['host'];
    }

    public function getScheme()
    {
        $data = parse_url($this->getUrl());
        return $data['scheme'];
    }
}