<?php

namespace BrauneDigital\GeoBundle\Model;

use BrauneDigital\GeoBundle\Entity\City;
use BrauneDigital\GeoBundle\Entity\Country;
use BrauneDigital\GeoBundle\Entity\State;

interface GeoInterface
{

	public function setCity(City $city);
	public function setCountry(Country $country);
	public function setState(State $state);
}
