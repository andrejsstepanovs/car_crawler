<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class CrawlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:crawl');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('RUN');

        $url = 'https://suchen.mobile.de/fahrzeuge/search.html?cn=DE&damageUnrepaired=NO_DAMAGE_UNREPAIRED&doorCount=FOUR_OR_FIVE&isSearchRequest=true&makeModelVariant1.makeId=1900&makeModelVariant1.modelId=8&maxPowerAsArray=PS&minPowerAsArray=PS&scopeId=C';

        $crawler = $this->getContainer()->get('app.crawler');
		$crawler->crawl($url);
    }
}