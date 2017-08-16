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

        $url = $input->getAttribute('url');

        $crawler = $this->getContainer()->get('app.crawler');
		$crawler->crawl($url);
    }
}