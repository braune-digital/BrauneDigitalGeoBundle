<?php

namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Knp\DoctrineBehaviors\Model as ORMBehaviors;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */

class City
{

	/**
	 * State
	 *
	 * @var State
	 * @JMS\Expose
	 */
	protected $state;

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
	 * @JMS\Expose
	 * @var Country
	 */
	protected $country;


	/**
	 * Latitude coordinate
	 *
	 * @var float
	 */
	protected $latitude;

	/**
	 * Longitude coordinate
	 *
	 * @var float
	 */
	protected $longitude;

	/**
	 * Timezone
	 *
	 * @var Timezone
	 */
	protected $timezone;

	/**
	 * Creation date
	 *
	 * @var DateTime
	 */
	protected $creationDate;

	/**
	 * Modification date
	 *
	 * @var DateTime
	 */
	protected $modificationDate;

    /**
     * Name (UTF-8 encoded)
     * @var string
     */
    protected $fcode;

	/**
	 * Creates a new locality
	 */
	public function __construct()
	{
		$this->creationDate = new \DateTime();
        $this->latitude = 0;
        $this->longitude = 0;
	}


	/**
	 * Returns the GeoNames.org identifier of this locality
	 *
	 * @return integer
	 */
	public function getGeonameIdentifier()
	{
		return $this->geonameIdentifier;
	}

	/**
	 * Sets the GeoNames.org identifier of this locality
	 *
	 * @param integer $geonameIdentifier Identifier
	 *
	 * @return Locality
	 */
	public function setGeonameIdentifier($geonameIdentifier)
	{
		$this->geonameIdentifier = $geonameIdentifier;

		return $this;
	}

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
	 *
	 * @return Locality
	 */
	public function setCountry(Country $country)
	{
		$this->country = $country;

		return $this;
	}

	/**
	 * Returns the name of the locality
	 * @JMS\VirtualProperty
	 * @return string
	 */
	public function getName()
	{
		return $this->getNameUtf8();
	}

	/**
	 * Returns the approximate latitude of the locality
	 *
	 * @return float
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}

	/**
	 * Sets the latitude of the locality
	 *
	 * @param string $latitude Latitude
	 *
	 * @return float
	 */
	public function setLatitude($latitude)
	{
		$this->latitude = $latitude;

		return $this;
	}

	/**
	 * Returns the longitude of the locality
	 *
	 * @return float
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}

	/**
	 * Sets the longitude of the locality
	 *
	 * @param float $longitude Longitude
	 *
	 * @return Locality
	 */
	public function setLongitude($longitude)
	{
		$this->longitude = $longitude;

		return $this;
	}

	/**
	 * Returns the timezone
	 *
	 * @return Timezone
	 */
	public function getTimezone()
	{
		return $this->timezone;
	}

	/**
	 * Sets the timezone
	 *
	 * @param Timezone $timezone Timezone
	 *
	 * @return Locality
	 */
	public function setTimezone(Timezone $timezone = null)
	{
		$this->timezone = $timezone;

		return $this;
	}

	/**
	 * Returns the creation date of this locality
	 *
	 * @return DateTime
	 */
	public function getCreationDate()
	{
		return $this->creationDate;
	}

	/**
	 * Returns the modification date of this locality
	 *
	 * @return DateTime
	 */
	public function getModificationDate()
	{
		return $this->modificationDate;
	}

	/**
	 * Sets the modification date of this locality
	 *
	 * @param \DateTime $modificationDate Modification date
	 *
	 * @return Locality
	 */
	public function setModificationDate(\DateTime $modificationDate)
	{
		$this->modificationDate = $modificationDate;

		return $this;
	}


    /**
     * Returns the state
     * 
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the state
     * 
     * @param State $state State
     */
    public function setState(State $state)
    {
        $this->state = $state;

        return $this;
    }

    public function getGeopoint() {
        return $this->getLatitude() . ',' . $this->getLongitude();
    }

	/**
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments)
	{
		return $this->proxyCurrentLocaleTranslation($method, $arguments);
	}


	/**
	 * @param $method
	 */
	public function __get($method) {
		return $this->proxyCurrentLocaleTranslation($method);
	}

	public function getNameUtf8() {
		$property = $this->translate()->getNameUtf8();
		if ($property) {
			return $property;
		} else {
			return $this->translate($this->getDefaultLocale())->getNameUtf8();
		}
	}

    public function getDescription() {
        $property = $this->translate()->getDescription();
        if ($property) {
            return $property;
        } else {
            return $this->translate($this->getDefaultLocale())->getDescription();
        }
    }
	/**
	 * __toString
	 *
	 * @return string
	 */
	public function __toString()
	{
		$name = $this->translate()->getNameUtf8();
		if ($name) {
			return $name;
		} else if ($this->translate($this->getDefaultLocale())->getNameUtf8()) {
			return $this->translate($this->getDefaultLocale())->getNameUtf8();
		} else {
			return (string) $this->getId();
		}

	}


    /**
     * @return string
     */
    public function getFcode()
    {
        return $this->fcode;
    }

    /**
     * @param string $fcode
     */
    public function setFcode($fcode)
    {
        $this->fcode = $fcode;
    }


	/**
	 * @JMS\VirtualProperty
	 */
	public function getLabel() {
		$label = $this->getName();
		$label .= ($this->getCountry()) ? ', ' . $this->getCountry()->getCode() : '';
		$label .= ($this->getState()) ? ', ' . $this->getState()->getNameUtf8() : '';
		return $label;
	}

	public function getType() {
		return 'city';
	}
}