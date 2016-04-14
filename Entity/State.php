<?php

namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation as JMS;

/**
 * State

 *
 * @JMS\ExclusionPolicy("all")
 */
class State
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
	 * Name (ASCII encoded)
	 *
	 * @var string
	 */
	protected $nameAscii;

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
	 * Creates a new locality
	 */
	public function __construct()
	{
		$this->creationDate = new \DateTime();
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
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->getNameUtf8() ?: $this->getNameAscii();
	}

	/**
	 * Returns the ASCII encoded name of the locality
	 *
	 * @return string
	 */
	public function getNameAscii()
	{
		return $this->nameAscii;
	}

	/**
	 * Sets the ASCII encoded name of the locality
	 *
	 * @param string $name Name
	 *
	 * @return Locality
	 */
	public function setNameAscii($name)
	{
		$this->nameAscii = $name;

		return $this;
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

	public function __call($method, $arguments)
	{
		return $this->proxyCurrentLocaleTranslation($method, $arguments);
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
		} else {
			$value = $this->translate($this->getDefaultLocale())->getNameUtf8();
			if ($value) {
				return $value;
			}
		}
		return '';

	}

	/**
	 * @JMS\VirtualProperty
	 */
	public function getNameUtf8() {
		$property = $this->translate()->getNameUtf8();
		if ($property) {
			return $property;
		} else {
			return $this->translate($this->getDefaultLocale())->getNameUtf8();
		}
	}
}