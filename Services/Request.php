<?php
namespace BrauneDigital\GeoBundle\Services;

use Application\BrauneDigital\GeoBundle\Entity\City;
use Application\BrauneDigital\GeoBundle\Entity\State;
use Application\BrauneDigital\GeoBundle\Entity\Timezone;
use BrauneDigital\GeoBundle\Model\GeoInterface;
use GuzzleHttp\Client;

class Request {

    const GEONAMES_TYPE_CITY = 'P';
    const GEONAMES_TYPE_COUNTRY = 'A';

    protected $em;
    protected $languages;
    protected $locales;


    public function __construct(\Doctrine\ORM\EntityManager $em, \Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->locales = $this->em->getRepository('BrauneDigitalTranslationBaseBundle:Language')->getEnabledCodes();
    }

    /**
     * @param $fcl
     * @param $geonameId
     * @return mixed
     */
    public function request($fcl, $geonameId, GeoInterface $entity)
    {
        switch ($fcl) {
            case self::GEONAMES_TYPE_CITY:
                $this->requestCity($geonameId, $entity);
                break;
            case self::GEONAMES_TYPE_COUNTRY:
                $this->requestCountry($geonameId, $entity);
                break;
        }
        return $entity;
    }

    /**
     * @param integer $geonameId
     * @return City
     */
    public function requestCountry($geonameId, GeoInterface $entity)
    {
        $country = $this->container->get('doctrine')->getRepository('Application\BrauneDigital\GeoBundle\Entity\Country')->findOneBy(array(
            'geonameIdentifier' => $geonameId
        ));

        if (!$country) {
            $client = new Client();
            $request = $client->createRequest('GET', 'http://api.geonames.org/getJSON?geonameId=' . $geonameId . '&username=' . $this->container->getParameter('geonames_user'));
            $response = $client->send($request);
            $result = $response->json();

            if ($result != null && array_key_exists('countryCode', $result)) {
                $country = $this->em->getRepository('ApplicationBrauneDigitalGeoBundle:Country')->findOneByCode($result['countryCode']);
            }
        }

        if ($country) {
            $entity->setCountry($country);
        }

        return $country;
    }


    /**
     * @param integer $geonameId
     * @return State
     */
    public function requestState($geonameId, GeoInterface $entity)
    {
        $state = $this->container->get('doctrine')->getRepository('Application\BrauneDigital\GeoBundle\Entity\State')->findOneBy(array(
            'geonameIdentifier' => $geonameId
        ));

        if (!$state) {
            $state = new State();
            $client = new Client();
            $request = $client->createRequest('GET', 'http://api.geonames.org/getJSON?geonameId=' . $geonameId . '&username=' . $this->container->getParameter('geonames_user'));
            $response = $client->send($request);
            $result = $response->json();

            if($result != null && array_key_exists('geonameId', $result)) {
                $country = $this->em->getRepository('ApplicationBrauneDigitalGeoBundle:Country')->findOneByCode($result['countryCode']);
                if ($country) {
                    $state->setCountry($country);
                }

                $state->setLatitude($result['lat']);
                $state->setLongitude($result['lng']);

                $timezone = $this->em->getRepository('ApplicationBrauneDigitalGeoBundle:Timezone')->findOneByCode($result['timezone']['timeZoneId']);
                if ($timezone) {
                    $state->setTimezone($timezone);
                }
                $state->setGeonameIdentifier($result['geonameId']);
                $state->setNameAscii($result['asciiName']);

                $locales = $this->em->getRepository('BrauneDigitalTranslationBaseBundle:Language')->getEnabledCodes();

                if (isset($result['name'])) {
                    $state->translate('en')->setNameUtf8($result['name']);
                }

                foreach($result['alternateNames'] as $alternateName) {
                    if (isset($alternateName['lang']) && in_array($alternateName['lang'], $locales)) {
                        if(!$state->translate($alternateName['lang'])->getNameUtf8() || isset($alternateName['isPreferredName'])) {
                            $state->translate($alternateName['lang'])->setNameUtf8($alternateName['name']);
                        }
                    }
                }

                $this->em->persist($state);
                $state->mergeNewTranslations();
                $this->em->flush();

            } else {
                $state = null;
            }

        }

        if ($state != null) {
            $entity->setState($state);
        }

        return $state;
    }



    /**
     * @param integer $geonameId
     * @return City
     */
    public function requestCity($geonameId, GeoInterface $entity)
    {

        $city = $this->container->get('doctrine')->getRepository('Application\BrauneDigital\GeoBundle\Entity\City')->findOneBy(array(
            'geonameIdentifier' => $geonameId
        ));

        if (!$city) {
            $city = new City();
            $client = new Client();
            $request = $client->createRequest('GET', 'http://api.geonames.org/getJSON?geonameId=' . $geonameId . '&username=' . $this->container->getParameter('geonames_user'));
            $response = $client->send($request);
            $result = $response->json();

            if($result != null && array_key_exists('adminId1', $result) && array_key_exists('geonameId', $result)) {
                $country = $this->em->getRepository('ApplicationBrauneDigitalGeoBundle:Country')->findOneByCode($result['countryCode']);

                if ($country) {
                    $city->setCountry($country);
                }

                $state = $this->em->getRepository('ApplicationBrauneDigitalGeoBundle:Country')->findOneByCode($result['adminId1']);

                if ($state) {
                    $city->setState($state);
                }

                $city->setLatitude($result['lat']);
                $city->setLongitude($result['lng']);
                $city->setFcode($result['fcode']);

                $timezone = $this->em->getRepository('ApplicationBrauneDigitalGeoBundle:Timezone')->findOneByCode($result['timezone']['timeZoneId']);
                if ($timezone) {
                    $city->setTimezone($timezone);
                }
                $city->setGeonameIdentifier($result['geonameId']);

                $locales = $this->em->getRepository('BrauneDigitalTranslationBaseBundle:Language')->getEnabledCodes();
                
                if (isset($result['name'])) {
                    $city->translate('en')->setNameUtf8($result['name']);
                }

                foreach($result['alternateNames'] as $alternateName) {
                    if (isset($alternateName['lang']) && in_array($alternateName['lang'], $locales)) {
                        if(!$city->translate($alternateName['lang'])->getNameUtf8() || isset($alternateName['isPreferredName'])) {
                            $city->translate($alternateName['lang'])->setNameUtf8($alternateName['name']);
                        }
                    }
                }
                
                $this->em->persist($city);
                $city->mergeNewTranslations();
                $this->em->flush();

            } else {
                $city = null;
            }

        }

        if ($city != null) {
            $entity->setCity($city);
        }

        return $city;
    }
} 