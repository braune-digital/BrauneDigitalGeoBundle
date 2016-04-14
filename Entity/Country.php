<?php

namespace BrauneDigital\GeoBundle\Entity;

use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation as JMS;

/**
 * Country
 *
 * @JMS\ExclusionPolicy("all")
 */
class Country
{

    /**
     * ISO code (2 character)
     *
     * @var string
	 * @JMS\Expose
     */
    protected $code;

    /**
     * Top level domain
     *
     * @var string
     */
    protected $domain;

    /**
     * Postal code format
     *
     * @var string
     */
    protected $postalCodeFormat;

    /**
     * Postal code regular expression
     *
     * @var string
     */
    protected $postalCodeRegex;

    /**
     * Phone number prefix
     *
     * Where there is more than one possible phone prefix the different prefixes
     * will be separated by commas.
     *
     * @var string
     */
    protected $phonePrefix;



    /**
     * @var array
     *
     */
    protected $languages;

    protected $cities;

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

    /**
     * @param $method
     */
    public function __get($method) {
        return $this->proxyCurrentLocaleTranslation($method);
    }
}