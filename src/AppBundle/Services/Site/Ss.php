<?php

namespace AppBundle\Services\Site;

use AppBundle\Entity\Car;
use AppBundle\Entity\Url;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DomCrawler\Crawler;


class Ss extends SiteAbstract implements SiteInterface
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
        file_put_contents('/tmp/last_ss_com', $html);
        //$html = file_get_contents('/tmp/last_ss_com');

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
        /** @var Car[] $cars */
        $cars = [];

        /** @var \DOMElement $tr */
        $elements = $crawler->filter('tr');
        foreach ($elements as $i => $tr) {
            if ($i < 5) {
                continue;
            }

            $tds = $tr->childNodes;
            if ($tds->length && isset($tds[2]) && isset($tds[6]) && isset($tds[1])) {
                $href       = $tds[1]->childNodes[0]->getAttribute('href');
                $externalId = str_replace('.html', '', substr($href, strrpos($href, '/') + 1));

                $car = new Car();
                $car->setName($tds[2]->nodeValue);
                $car->setYear($tds[3]->nodeValue);
                $car->setEngine($tds[4]->nodeValue);
                $car->setPrice(str_replace(['  €', ','], '', $tds[6]->nodeValue));
                $car->setPower(0);
                $car->setExternalId($externalId);

                $mileage = $tds[5]->nodeValue;
                if (!$car->getMileage()) {
                    $name = $car->getName();
                    if (strpos($name, 'km') !== false) {
                        preg_match('/[0-9 ]*km/', $name, $match);
                        if (!empty($match)) {
                            $mileage = str_replace(['km', ' '], '', $match[0]);
                        }
                    }
                }

                $car->setMileage(str_replace(' tūkst.', '000', $mileage));
                $cars[] = $car;
            }
        }

        return $cars;
    }

    private function getNextPageUrl(Crawler $crawler)
    {
        $nextPage = $crawler->filter('.navi');
        $url      = false;
        foreach ($nextPage as $paginator) {
            $url = $paginator->getAttribute('href');
        }
        if (strpos($url, '/page') === false) {
            return false;
        }

        return $this->getUrl()->getScheme() . '://' . $this->getUrl()->getUrlHost() . $url;
    }
}