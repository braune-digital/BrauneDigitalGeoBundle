<?php
namespace BrauneDigital\GeoBundle\Services;

use Application\BrauneDigital\GeoBundle\Entity\City;
use Application\BrauneDigital\GeoBundle\Entity\State;
use Application\BrauneDigital\GeoBundle\Entity\Timezone;
use GuzzleHttp\Client;

class Request {

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
     * @param integer $geonameId
     * @return City
     */
    public function requestCity($geonameId)
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

                $city->setLatitude($result['lat']);
                $city->setLongitude($result['lng']);
                $city->setFcode($result['fcode']);

                $city->setTimezone($this->em->getRepository('ApplicationBrauneDigitalGeoBundle:Timezone')->findOneByCode($result['timezone']['timeZoneId']));
                $city->setCountry($country);
                $city->setGeonameIdentifier($result['geonameId']);

                $locales = $this->em->getRepository('BrauneDigitalTranslationBaseBundle:Language')->getEnabledCodes();
                foreach ($locales as $locale) {
                    $names = array_filter($result['alternateNames'], function($n) use ($locale) {
                        if (!isset($n['lang'])) {
                            return false;
                        }
                        return $locale == $n['lang'];
                    });

                    $city->translate($locale)->setNameUtf8($names[array_keys($names)[0]]['name']);
                }

                $this->em->persist($city);
                $city->mergeNewTranslations();
                $this->em->flush();

            } else {
                $city = null;
            }

        }


        return $city;
    }
} 