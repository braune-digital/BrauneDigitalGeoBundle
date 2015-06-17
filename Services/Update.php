<?php
namespace BrauneDigital\GeoBundle\Services;

use Application\BrauneDigital\GeoBundle\Entity\City;
use Application\BrauneDigital\GeoBundle\Entity\State;
use GuzzleHttp\Client;

class Update {

    protected $em;
    protected $languages;
    protected $locales;


    public function __construct(\Doctrine\ORM\EntityManager $em, \Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->locales = $this->em->getRepository('BrauneDigitalTranslationBaseBundle:Language')->getEnabledCodes();
    }

    public function updateCity($city, $dbUpdate = true) {

        $result = null;


        if(!$city->getGeonameIdentifier()) {

           /* //search for a city with this name
            $dql = "
				  SELECT c
				  FROM BrauneDigitalGeoBundle:City c
				  JOIN c.translations ctr
				  WHERE ctr.nameUtf8 LIKE :nameUtf8
				  ORDER BY ctr.nameUtf8
				";
            $query = $this->em->createQuery($dql)->setParameter('nameUtf8', $city->getNameUtf8());
            $cities = $query->getResult();

            if (count($cities) > 0) {
                //TODO: City with the same name exists, therefore the existing city is not the correct one
                //$city = $cities[0];
            }
            else {
                return;
            } */

            $client = new Client();

            if($city->getCountry()) {

                $request = $client->createRequest('GET',
                    'http://api.geonames.org/search?name_equals=' . $city->getNameUtf8() . '&country=' . $city->getCountry()->getCode() .'&username=' . $this->container->getParameter('geonames_user') . '&type=json&maxRows=1&style=FULL');
            }
            else {
                $request = $client->createRequest('GET',
                    'http://api.geonames.org/search?name_equals=' . $city->getNameUtf8() .'&username=' . $this->container->getParameter('geonames_user') . '&type=json&maxRows=1&style=FULL');
            }

            $response = $client->send($request);
            $result = $response->json();

            if ($result['totalResultsCount'] > 0) {
                $result = $result['geonames'][0];
            }
            else {
                $result = null;
            }
        }
        else {
            $client = new Client();
            $request = $client->createRequest('GET', 'http://api.geonames.org/getJSON?geonameId=' . $city->getGeonameIdentifier() . '&username=' . $this->container->getParameter('geonames_user'));
            $response = $client->send($request);
            $result = $response->json();
        }


        if($result != null && array_key_exists('adminId1', $result) && array_key_exists('geonameId', $result)) {

            $state = $this->em->getRepository('BrauneDigitalGeoBundle:State')->findOneByGeonameIdentifier($result['adminId1']);
            $country = $this->em->getRepository('BrauneDigitalGeoBundle:Country')->findOneByCode($result['countryCode']);

            //check if state exists
            if ($state == null) {
                $state = new State();
                $state->setGeonameIdentifier($result['adminId1']);
                $state->setModificationDate(new \DateTime());
            }

            if($state->getID() == 0) {
                $this->updateState($state);
            }
            // $this->updateCountry($state); //not needed since we add cities by the country


            $city->translate('en')->setNameUtf8($result['name']);
            $city->setLatitude($result['lat']);
            $city->setLongitude($result['lng']);
            $city->setFcode($result['fcode']);

            $city->setState($state);
            $city->setTimezone($this->em->getRepository('BrauneDigitalGeoBundle:Timezone')->findOneByCode($result['timezone']['timeZoneId']));
            $city->setCountry($country);
            $city->setGeonameIdentifier($result['geonameId']);

        }


        if($dbUpdate) {
			$this->em->persist($city);
			$city->mergeNewTranslations();
			$this->em->flush();


            if($result != null && array_key_exists('alternateNames', $result)) {


                foreach($result['alternateNames'] as $trans){
                    if (array_key_exists('lang', $trans) && array_key_exists('name', $trans) &&  in_array($trans['lang'], $this->locales) ) {
						$city->translate( $trans['lang'], false)->setNameUtf8($trans['name']);
						$this->em->persist($city);
						$city->mergeNewTranslations();
						$this->em->flush();
                    }
                }
                $this->em->flush();
            }
        }
    }

    public function updateState($state, $dbUpdate = true) {

        if($state->getGeonameIdentifier()) {
            $client = new Client();
            $request = $client->createRequest('GET', 'http://api.geonames.org/getJSON?geonameId=' . $state->getGeonameIdentifier() . '&username=' . $this->container->getParameter('geonames_user'));
            $response = $client->send($request);
            $result = $response->json();

            if($result != null) {
                $country = $this->em->getRepository('BrauneDigitalGeoBundle:Country')->findOneByCode($result['countryCode']);

				$state->translate('en')->setNameUtf8($result['name']);
                $state->setNameAscii($result['asciiName']);
                $state->setLatitude($result['lat']);
                $state->setLongitude($result['lng']);
                $state->setTimezone($this->em->getRepository('BrauneDigitalGeoBundle:Timezone')->findOneByCode($result['timezone']['timeZoneId']));
                $state->setCountry($country);
                $state->setGeonameIdentifier($result['geonameId']);
				$this->em->persist($state);
				$state->mergeNewTranslations();
				$this->em->flush();
            }
        }

       // if(!$state->getId())
        if($dbUpdate) {
            $this->em->persist($state);
            $this->em->flush();

            if($result != null && array_key_exists('alternateNames', $result)) {

                $repository = $this->em->getRepository('BrauneDigitalGeoBundle:StateTranslation');

                foreach($result['alternateNames'] as $trans){
                    if (array_key_exists('lang', $trans) && array_key_exists('name', $trans) &&  in_array($trans['lang'], $this->locales) ) {
						$state->translate( $trans['lang'], false)->setNameUtf8($trans['name']);
						$this->em->persist($state);
						$state->mergeNewTranslations();
						$this->em->flush();
                    }
                }
                $this->em->flush();
            }
        }
    }

    public function updateAll(){
        $cities = $this->em->getRepository('BrauneDigitalGeoBundle:City')->findAll(true);
        foreach($cities as $city) {
            $this->updateCity($city);
        }
    }

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function setContainer (\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->container = $container;
    }

    public function getContainer () {
        return $this->container;
    }
} 