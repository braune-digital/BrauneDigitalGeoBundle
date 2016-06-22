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

            if ($result != null && array_key_exists('adminId1', $result) && array_key_exists('geonameId', $result)) {

                $country = $this->em->getRepository('ApplicationBrauneDigitalGeoBundle:Country')->findOneByCode($result['countryCode']);
            }
        }

        $entity->setCountry($country);
        return $country;
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
                $entity->setCountry($country);

                $city->setLatitude($result['lat']);
                $city->setLongitude($result['lng']);
                $city->setFcode($result['fcode']);

                $city->setTimezone($this->em->getRepository('ApplicationBrauneDigitalGeoBundle:Timezone')->findOneByCode($result['timezone']['timeZoneId']));
                $city->setGeonameIdentifier($result['geonameId']);

                $locales = $this->em->getRepository('BrauneDigitalTranslationBaseBundle:Language')->getEnabledCodes();

                $alternateNames = $result['alternateNames'];

                foreach ($locales as $locale) {
                    $names = array_filter($alternateNames, function($n) use ($locale) {
                        if (!isset($n['lang'])) {
                            return false;
                        }
                        return $locale == $n['lang'];
                    });
                    if (count($names)) {
                        $city->translate($locale)->setNameUtf8($names[array_keys($names)[0]]['name']);
                    }
                }

                $this->em->persist($city);
                $city->mergeNewTranslations();
                $this->em->flush();

            } else {
                $city = null;
            }

        }

        $entity->setCity($city);

        return $city;
    }
} 