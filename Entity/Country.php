<?php

namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * Country
 *
 *
 * @Entity(repositoryClass="CountryRepository")
 * @Table(name="geo_country")
 * @ExclusionPolicy("all")
 */
class Country
{

	use ORMBehaviors\Translatable\Translatable;
	use \Application\AppBundle\Model\Translatable\TranslatableMethods {
		\Application\AppBundle\Model\Translatable\TranslatableMethods::proxyCurrentLocaleTranslation insteadof ORMBehaviors\Translatable\Translatable;
	}

    /**
     * Unique identifier which represents the country in the local database.
     *
     * @Column(type="integer")
     * @GeneratedValue
     * @Id
     * @var int
     */
    protected $id;

    /**
     * ISO code (2 character)
     *
     * @Column(length=2, unique=true)
     * @var string
	 * @Expose
     */
    protected $code;

    /**
     * Top level domain
     *
     * @Column(length=2, nullable=true)
     * @var string
     */
    protected $domain;

    /**
     * Postal code format
     *
     * @Column(name="postal_code_format", length=60, nullable=true)
     * @var string
     */
    protected $postalCodeFormat;

    /**
     * Postal code regular expression
     *
     * @Column(name="postal_code_regex", length=180, nullable=true)
     * @var string
     */
    protected $postalCodeRegex;

    /**
     * Phone number prefix
     *
     * Where there is more than one possible phone prefix the different prefixes
     * will be separated by commas.
     *
     * @Column(name="phone_prefix", length=20, nullable=true)
     * @var string
     */
    protected $phonePrefix;



    /**
     * @var array
     *
     * @Column(name="languages", type="json_array", nullable=true)
     */
    protected $languages;

    /**
     * @OneToMany(targetEntity="\BrauneDigital\GeoBundle\Entity\City", mappedBy="country")
     */
    protected $cities;

    /**
     * @ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     */
    protected $image;

    /**
     * Returns the unique identifier of this country in the local database
     * 
     * @return integer
     */

    public function __construct()
    {
        $languages = array();
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
     * Gets the unique 2 character ISO code of this country
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the unique 2 character ISO code of this country
     * 
     * @param string $code Country code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }


    /**
     * Gets the top level domain suffix of the country
     * 
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the top level domain suffix of the country
     * 
     * @param string $domain Domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Gets the format which postal codes from the country are expected to
     * adhere to
     * 
     * @return string
     */
    public function getPostalCodeFormat()
    {
        return $this->postalCodeFormat;
    }

    /**
     * Sets the format which postal codes from the country are expected to
     * adhere to
     * 
     * @param string $postalCodeFormat
     * @return Country
     */
    public function setPostalCodeFormat($postalCodeFormat)
    {
        $this->postalCodeFormat = $postalCodeFormat;

        return $this;
    }

    /**
     * Gets the regular expression which postal codes from the country are
     * expected to match
     * 
     * @return string
     */
    public function getPostalCodeRegex()
    {
        return $this->postalCodeRegex;
    }

    /**
     * Sets the regular expression which postal codes from the country are
     * expected to match
     * 
     * @param string $postalCodeRegex
     * @return Country
     */
    public function setPostalCodeRegex($postalCodeRegex)
    {
        $this->postalCodeRegex = $postalCodeRegex;

        return $this;
    }

    /**
     * Gets the prefix which is prepened to phone nubmers inside this country
     * 
     * @return string
     */
    public function getPhonePrefix()
    {
        return $this->phonePrefix;
    }

    /**
     * Sets the prefix which is prepened to phone nubmers inside this country
     * 
     * @param string $phonePrefix
     * @return Country
     */
    public function setPhonePrefix($phonePrefix)
    {
        $this->phonePrefix = $phonePrefix;

        return $this;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @return mixed
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * @param mixed $cities
     */
    public function setCities($cities)
    {
        $this->cities = $cities;
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


    /**
     * @return string
     */
    public function getFirstLanguage()
    {
        return (is_array($this->languages) && count($this->languages) > 0 && $this->languages[0] != '') ? $this->languages[0] : 'en';
    }

    /**
     * @param array $languages
     */
    public function setLanguages($languages)
    {
        if($languages != null) {
            $this->languages = $languages;
        }
    }

    public function getName() {
        $property = $this->translate()->getName();
        if ($property) {
            return $property;
        } else {
            return $this->translate($this->getDefaultLocale())->getName();
        }
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
		$name = $this->translate()->getName();
		if ($name) {
			return $name;
		} else if ($this->translate($this->getDefaultLocale())->getName()) {
			return $this->translate($this->getDefaultLocale())->getName();
		} else {
			return (string) $this->getId();
		}

	}


}