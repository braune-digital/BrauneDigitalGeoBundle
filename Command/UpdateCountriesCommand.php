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
			->setName('escapegamer:geo:updatecountries')
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
                $entity = $em->getRepository('BrauneDigitalGeoBundle:Country')->findOneByCode($country['countryCode']);

                if (!$entity) {
                    $entity = new Country();
                    $entity->setCode($country['countryCode']);
                    $entity->setName($country['countryName']);
                    $em->persist($entity);
                    $em->flush();
                    $numAdded++;
                }else if($entity->getName() == null) {
                    $entity->setName($country['countryName']);
                }


                $languages = explode(',',  $country['languages']);
                foreach($languages as $index=>$language) {
                    //cut language at '-'
                    $trimmed = explode('-', $language);
                    $languages[$index] = $trimmed[0];
                }
                //add languages if not already existing
                $entity->setLanguages(array_merge($entity->getLanguages(), array_diff($languages, $entity->getLanguages())));

                //add Translation
				$entity->translate($locale, false)->setName($country['countryName']);
				$em->persist($entity);
				$entity->mergeNewTranslations();
                $em->flush();
                $numUpdated++;

                if($numUpdated % 10 == 0)
                    $output->writeln('progess: '. $numUpdated. '/' . count($countries)*count($locales));
            }

        }
        $output->writeln('Finished: '. $numAdded. ' cities were added.');
    }
}