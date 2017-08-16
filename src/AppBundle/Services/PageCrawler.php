<?php

namespace AppBundle\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;


class PageCrawler
{
	private $requestParams;

	private $file;

	private $hederDone;

	public function __construct()
	{
		$this->requestParams = [
		    'headers' => [
		        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
		    ]
		];

		$this->file = '/home/andrejs/Desktop/cars.csv';
		if (file_exists($this->file)) {
			unlink($this->file);
		}
	}

	public function crawl($url)
	{
		$i = 0;
		do {
			$client = new Client();
			$res = $client->request('GET', $url, $this->requestParams);

			if (200 != $res->getStatusCode()) {
				die('FAILED');
			}
			$i++;
			echo "$i\n";

			$html = $res->getBody();
			//file_put_contents('/tmp/aaa', $html);
			//$html = file_get_contents('/tmp/aaa');

			$crawler = new Crawler(strval($html));
			
			$data = $this->crawlMobileDeSearchResult($crawler);
			$this->saveData($data);

			$url  = $this->getMobileDeNextPageUrl($crawler);
			echo 'next: ' . $url . PHP_EOL;
		} while ($url);
	}

	private function saveData(array $data)
	{
		$fp = fopen($this->file, 'a');

		if (!$this->hederDone) {
			foreach ($data as $row) {
	    		fputcsv($fp, array_keys($row));
	    		break;
			}
			$this->hederDone = true;
		}
		
		foreach ($data as $row) {
    		fputcsv($fp, $row);
		}

		fclose($fp);
	}

	private function getMobileDeNextPageUrl(Crawler $crawler)
	{
		$nextPage = $crawler->filter('.rbt-page-forward');
		foreach ($nextPage as $paginator) {
			$url = $paginator->getAttribute('data-href');
			return $url;
		}
		return false;
	}

	private function crawlMobileDeSearchResult(Crawler $crawler)
	{
		$elements = $crawler->filter('.parking-block');


		$data = [];
		foreach ($elements as $el) {
			$data[] = [
				'name'    => $el->getAttribute('data-park-title'),
				'price'   => (int)$el->getAttribute('data-park-price-amount'),
				'id'      => $el->getAttribute('data-parking'),
				'mileage' => null,
				'year'    => null,
				'hp'      => null,
			];
		}

		$elements = $crawler->filter('.rbt-regMilPow');
		$i = 0;
		foreach ($elements as $el) {
			$parts = explode(',', strval($el->nodeValue));

			$mileage = explode('kW', $parts[2]);
			$year 	 = explode('/', $parts[0]);
			$hp  	 = explode('kW', $parts[1]);

			$data[$i]['hp']      = trim(str_replace(['PS', '(', ')', ' '], '', $mileage[1]));
			$data[$i]['year']    = trim(array_pop($year));
			$data[$i]['mileage'] = trim(str_replace(['km', '.'], '', $hp[0]));
			$i++;
		}

		return $data;
	}	
}