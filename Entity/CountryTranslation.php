<?php
namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

use Application\AppBundle\Entity\SeoBase;

/**
 * @ORM\Entity()
 * @ORM\Table(name="geo_country_translation")
 * )
 */
class CountryTranslation
{

	use \BrauneDigital\GeoBundle\Model\Base;
	use \BrauneDigital\GeoBundle\Model\SeoBase;

	use ORMBehaviors\Translatable\Translation;
	use ORMBehaviors\Sluggable\Sluggable;
	use \Application\AppBundle\Model\Sluggable\SluggableMethods {
		\Application\AppBundle\Model\Sluggable\SluggableMethods::generateSlugValue insteadof ORMBehaviors\Sluggable\Sluggable;
	}

	/**
	 * Name
	 * @ORM\Column(length=50)
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