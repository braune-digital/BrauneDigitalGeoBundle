<?php

namespace BrauneDigital\GeoBundle\Command;

use Application\BrauneDigital\GeoBundle\Entity\Country;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\BrauneDigital\GeoBundle\Entity\City;

class UpdateCountriesCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('braunedigital:geo:updatecountries')
			->setDescription('Updating countries')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
    {

        $client = new Client();
        $em = $this->getContainer()->get('doctrine')->getManager();

        $numAdded = 0;
        $numUpdated = 0;
        $progress = 0;
        $locales = $em->getRepository('BrauneDigitalTranslationBaseBundle:Language')->getEnabledCodes();
        foreach ($locales as $locale) {
            $output->writeln("Locale " . $locale);
            $request = $client->createRequest('GET', 'http://api.geonames.org/countryInfoJSON?username=' . $this->getContainer()->getParameter('geonames_user') . '&lang=' . $locale);
            $response = $client->send($request);
            $result = $response->json();

            if ($result != null) {
                $countries = $result['geonames'];
            }

            if ($countries == null || count($countries) == 0) {
                $output->writeln('Could not get any countries for lang=' . $locale);
                continue;
            }
            foreach ($countries as $country) {
                $c = $em->getRepository('ApplicationBrauneDigitalGeoBundle:Country')->findOneByCode($country['countryCode']);
                if (!$c) {
                    $c = new Country();
                    $c->setCode($country['countryCode']);
                    $c->setName($country['countryName']);
                    $em->persist($c);
                    $numAdded++;
                } else if ($c->getName() == null) {
                    $c->setName($country['countryName']);
                }

                $c->setGeonameIdentifier($country['geonameId']);


                $languages = explode(',',  $country['languages']);
                foreach($languages as $index=>$language) {
                    $trimmed = explode('-', $language);
                    $languages[$index] = $trimmed[0];
                }
                if (is_array($c->getLanguages())) {
                    $c->setLanguages(array_merge($c->getLanguages(), array_diff($languages, $c->getLanguages())));
                } else {
                    $c->setLanguages($languages);
                }
                //add Translation
                $c->translate($locale, false)->setName($country['countryName']);
				$em->persist($c);
                $c->mergeNewTranslations();
                $em->flush();
                $numUpdated++;

                if($numUpdated % 10 == 0)
                    $output->writeln('progess: '. $numUpdated. '/' . count($countries)*count($locales));
            }

        }
        $output->writeln('Finished: '. $numAdded. ' cities were added.');
    }
}