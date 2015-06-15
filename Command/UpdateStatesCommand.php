<?php

namespace BrauneDigital\GeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use BrauneDigital\GeoBundle\Entity\City;

class UpdateStatesCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('escapegamer:geo:updatestates')
			->setDescription('Updating states')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$em = $this->getContainer()->get('doctrine')->getManager();

        $upt = $this->getContainer()->get('braunedigital_geo.update');

        $states = $em->getRepository('BrauneDigitalGeoBundle:State')->findAll();

        $numLocales = count($em->getRepository('BrauneDigitalTranslationBaseBundle:Language')->getEnabledCodes());

        $numUpdated = 0;

		foreach($states as $i => $state) {

            if(!$state->getGeonameIdentifier() || !$state->getCountry() || !$state->getNameUtf8() ||  !$state->getTranslations() || $state->getTranslations()->count() != $numLocales) {
                $upt->updateState($state);
                $state->setModificationDate(new \DateTime());
                $numUpdated++;
            }
            if($i % 10 == 0)
                $output->writeln('progess: ' . $i . ' / ' . count($states));
		}
        $em->flush();

        $output->writeln('Finished: '. $numUpdated . ' states have been updated');
	}
}