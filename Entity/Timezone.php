<?php

namespace BrauneDigital\GeoBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * Timezone
 *
 */
class Timezone
{
    /**
     * GeoNames.org ID
     *
     * Uniquely identifies this locality for syncronization from data on
     * GeoNames.org.
     *
     * @var integer
     */
    protected $geonameIdentifier;

    /**
     * Country
     *
     * @var Country
     */
    protected $country;

    /**
     * Timezone code
     *
     * @var string
     */
    protected $code;

    /**
     * Returns the country
     * 
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the country
     * 
     * @param Country $country Country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Returns the timezone code
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the timezone code
     * 
     * @param string $code Timezone code
     *
     * @return string
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getGeonameIdentifier()
    {
        return $this->geonameIdentifier;
    }

    /**
     * @param int $geonameIdentifier
     */
    public function setGeonameIdentifier($geonameIdentifier)
    {
        $this->geonameIdentifier = $geonameIdentifier;
    }


}