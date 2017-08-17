<?php

namespace AppBundle\Services\Site;

use AppBundle\Entity\Source;
use AppBundle\Entity\Url;


interface SiteInterface
{
    public function getCars(Url $url);
}