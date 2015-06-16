<?php

namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\MaxDepth;

use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 *
 * @ORM\Table(name="geo_city")
 * @ORM\Entity(repositoryClass="CityRepository")
 * @ExclusionPolicy("all")
 */

class City
{

	use ORMBehaviors\Translatable\Translatable;
	use \BrauneDigital\TranslationBaseBundle\Model\Translatable\TranslatableMethods {
		\BrauneDigital\TranslationBaseBundle\Model\Translatable\TranslatableMethods::proxyCurrentLocaleTranslation insteadof ORMBehaviors\Translatable\Translatable;
	}


	/**
	 * @var int
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @Expose
	 */
	private $id;


	/**
	 * State
	 *
	 * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(nullable=true)
	 * @var State
	 * @Expose
	 */
	protected $state;



	/**
	 * GeoNames.org ID
	 *
	 * Uniquely identifies this locality for syncronization from data on
	 * GeoNames.org.
	 *
	 * @ORM\Column(name="geoname_id", type="integer", nullable=true)
	 * @var integer
	 */
	protected $geonameIdentifier;

	/**
	 * Country
	 *
	 * @ORM\ManyToOne(targetEntity="Country")
	 * @ORM\JoinColumn(nullable=false)
	 * @Expose
	 * @var Country
	 */
	protected $country;


	/**
	 * Latitude coordinate
	 *
	 * @ORM\Column(name="latitude", type="float", scale=6, precision=9, nullable=false)
	 * @var float
	 */
	protected $latitude;

	/**
	 * Longitude coordinate
	 *
	 * @ORM\Column(name="longitude", type="float", scale=6, precision=9, nullable=false)
	 * @var float
	 */
	protected $longitude;

	/**
	 * Timezone
	 *
	 * @ORM\ManyToOne(targetEntity="Timezone")
     * @ORM\JoinColumn(nullable=true)
	 * @var Timezone
	 */
	protected $timezone;

	/**
	 * Creation date
	 *
	 * @ORM\Column(name="creation_date", type="datetime")
	 * @var DateTime
	 */
	protected $creationDate;

	/**
	 * Modification date
	 *
	 * @ORM\Column(name="modification_date", type="datetime", nullable=true)
	 * @var DateTime
	 */
	protected $modificationDate;

	/**
	 * @ORM\OneToMany(targetEntity="\Application\AppBundle\Entity\Offer", mappedBy="city")
	 */
	protected $events;


    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     */
    protected $image;

	/**
	 * @var string
	 * @ORM\Column(name="old_slug", type="string", length=255, nullable=true)
	 */
	private $oldSlug;

	/**
	 * Name (UTF-8 encoded)
	 * @ORM\Column(name="old_name_utf8", type="string", nullable=true)
	 * @var string
	 */
	protected $oldNameUtf8;

	/**
	 * Name (UTF-8 encoded)
	 * @ORM\Column(name="old_name_ascii", type="string", nullable=true)
	 * @var string
	 */
	protected $oldNameAscii;

    /**
     * Name (UTF-8 encoded)
     * @ORM\Column(name="fcode", type="string", nullable=true)
     * @var string
     */
    protected $fcode;

	/**
	 * Creates a new locality
	 */
	public function __construct()
	{
		$this->creationDate = new \DateTime();
		$this->events = new ArrayCollection();
        $this->latitude = 0;
        $this->longitude = 0;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
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
	 * @VirtualProperty
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
     * @return ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param ArrayCollection $events
     */
    public function setRelation($events)
    {
        $this->events = $events;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

	public function getNumEvents() {
		return $this->events->count();
	}

	public function __call($method, $arguments)
	{
		return $this->proxyCurrentLocaleTranslation($method, $arguments);
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
	public function getOldSlug()
	{
		return $this->oldSlug;
	}

	/**
	 * @param string $oldSlug
	 */
	public function setOldSlug($oldSlug)
	{
		$this->oldSlug = $oldSlug;
	}

	/**
	 * @return mixed
	 */
	public function getOldNameUtf8()
	{
		return $this->oldNameUtf8;
	}

	/**
	 * @param mixed $oldNameUtf8
	 */
	public function setOldNameUtf8($oldNameUtf8)
	{
		$this->oldNameUtf8 = $oldNameUtf8;
	}

	/**
	 * @return string
	 */
	public function getOldNameAscii()
	{
		return $this->oldNameAscii;
	}

	/**
	 * @param string $oldNameAscii
	 */
	public function setOldNameAscii($oldNameAscii)
	{
		$this->oldNameAscii = $oldNameAscii;
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
	 * @VirtualProperty
	 */
	public function getLabel() {
		$label = $this->getName();
		$label .= ($this->getCountry()) ? ', ' . $this->getCountry()->getCode() : '';
		$label .= ($this->getState()) ? ', ' . $this->getState()->getNameUtf8() : '';
		return $label;
	}
}