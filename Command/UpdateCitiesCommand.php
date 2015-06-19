<?php

namespace BrauneDigital\GeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\BrauneDigital\GeoBundle\Entity\City;

class UpdateCitiesCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('braunedigital:geo:updatecities')
			->setDescription('Updating cities')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$em = $this->getContainer()->get('doctrine')->getManager();
        $upt = $this->getContainer()->get('braunedigital_geo.update');
        $cities = $em->getRepository('ApplicationBrauneDigitalGeoBundle:City')->findAll(true);
        $numLocales = count($em->getRepository('BrauneDigitalTranslationBaseBundle:Language')->getEnabledCodes());

        $numUpdated = 0;

		foreach($cities as $i => $city) {

            if(!$city->getGeonameIdentifier() || !$city->getState() || !$city->getTranslations() || $city->getTranslations()->count() != $numLocales) {
                $upt->updateCity($city);
                $city->setModificationDate(new \DateTime());
                $numUpdated++;
            }
            if($i % 10 == 0)
                $output->writeln('progess: ' . $i . ' / ' . count($cities));
		}
        $em->flush();

        $output->writeln('Finished: '. $numUpdated . ' cities have been updated');
	}
}