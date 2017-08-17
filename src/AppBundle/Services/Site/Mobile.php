<?php

namespace AppBundle\Services\Site;

use AppBundle\Entity\Car;
use AppBundle\Entity\Url;
use Symfony\Component\DomCrawler\Crawler;


class Mobile extends SiteAbstract implements SiteInterface
{
    public function getCars(Url $url)
    {
        if (!$this->getUrl()) {
            $this->setUrl($url);
        }

        $response = $this->getResponse();
        if (!$response || $response->getStatusCode() != 200) {
            return null;
        }
        $html = strval($response->getBody());
        file_put_contents('/tmp/last_mobile_de', $html);
        //$html = file_get_contents('/tmp/aaa');

        $crawler = new Crawler($html);
        $cars    = $this->crawl($crawler);

        $url = $this->getNextPageUrl($crawler);
        if (empty($url)) {
            return null;
        }
        $this->getUrl()->setUrl($url);

        return $cars;
    }

    /**
     * @param Crawler $crawler
     * @return Car[]
     */
    public function crawl(Crawler $crawler)
    {
        $elements = $crawler->filter('.parking-block');

        $cars = [];
        foreach ($elements as $el) {
            $car = new Car();
            $car->setName($el->getAttribute('data-park-title'))
                ->setPrice((int)$el->getAttribute('data-park-price-amount'))
                ->setExternalId($el->getAttribute('data-parking'));

            $cars[] = $car;
        }

        $elements = $crawler->filter('.rbt-regMilPow');
        $i = 0;
        foreach ($elements as $el) {
            $parts       = explode(',', strval($el->nodeValue));
            $yearData    = explode('/', $parts[0]);
            $mileageData = explode('kW', $parts[1]);
            if (isset($parts[2])) {
                $powerData = explode('kW', $parts[2]);
            } else {
                $powerData = 0;
            }

            $power   = str_replace(['PS', '(', ')', ' '], '', $powerData[1]);
            $power   = trim($power, ' ');
            $mileage = trim(str_replace(['km', '.'], '', $mileageData[0]));
            $year    = trim(array_pop($yearData));

            if (!$year && $mileage < 1000) {
                $year = date('y');
            }

            $car = $cars[$i];
            $car->setMileage($mileage)->setPower($power)->setYear($year);

            $i++;
        }

        return $cars;
    }

    private function getNextPageUrl(Crawler $crawler)
    {
        $nextPage = $crawler->filter('.rbt-page-forward');
        foreach ($nextPage as $paginator) {
            $url = $paginator->getAttribute('data-href');
            return $url;
        }
        return false;
    }
}