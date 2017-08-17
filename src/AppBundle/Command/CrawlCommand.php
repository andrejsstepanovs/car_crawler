<?php

namespace AppBundle\Command;

use AppBundle\Entity\Source;
use AppBundle\Entity\Url;
use AppBundle\Services\Site\SiteInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class CrawlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:crawl')
             ->addArgument('url');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('RUN');

        $url = $this->getUrl($input, $output);

        $sourceRepository = $this->getContainer()->get('doctrine')->getRepository(Source::class);
        /** @var Source $source */
        $source = $sourceRepository->findByUrl($url);


        /** @var SiteInterface $site */
        $site = $this->getContainer()->get('app.site.' . strtolower($source->getName()));

        $crawler = $this->getContainer()->get('app.crawler');
        $crawler->crawl($url, $source, $site);
    }

    private function getUrl(InputInterface $input, OutputInterface $output)
    {
        $inputValue = $input->getArgument('url');
        $url = new Url();
        $url->setUrl($inputValue);

        $validator = $this->getContainer()->get('validator');
        $errors    = $validator->validate($url);
        if ($errors->count()) {
            $output->writeln((string)$errors);
            throw new \InvalidArgumentException('Wrong input');
        }
        return $url;
    }
}