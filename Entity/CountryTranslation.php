<?php
namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

use Application\AppBundle\Entity\SeoBase;

class CountryTranslation
{

	use \BrauneDigital\GeoBundle\Model\Base;
	use \BrauneDigital\GeoBundle\Model\SeoBase;

	/**
	 * Name
	 * @var string
	 */
	protected $name;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}


	public function getSluggableFields()
	{
		return ['name'];
	}

	/**
	 * __toString
	 *
	 * @return string
	 */
	public function __toString()
	{
		return ($this->getTranslatable()->getName()) ? $this->getTranslatable()->getName() : '';

	}


}