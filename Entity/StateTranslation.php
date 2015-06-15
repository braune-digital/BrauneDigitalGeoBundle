<?php
namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity()
 * @ORM\Table(name="geo_state_translation")
 */
class StateTranslation
{

	use ORMBehaviors\Translatable\Translation;
	use ORMBehaviors\Sluggable\Sluggable;
	use \Application\AppBundle\Model\Sluggable\SluggableMethods {
		\Application\AppBundle\Model\Sluggable\SluggableMethods::generateSlugValue insteadof ORMBehaviors\Sluggable\Sluggable;
	}

	/**
	 * Name (UTF-8 encoded)
	 * @ORM\Column(name="name_utf8", type="string")
	 * @var string
	 */
	protected $nameUtf8;

	/**
	 * @return string
	 */
	public function getNameUtf8()
	{
		return $this->nameUtf8;
	}

	/**
	 * @param string $nameUtf8
	 */
	public function setNameUtf8($nameUtf8)
	{
		$this->nameUtf8 = $nameUtf8;
	}

	public function getSluggableFields()
	{
		return ['nameUtf8'];
	}

	/**
	 * __toString
	 *
	 * @return string
	 */
	public function __toString()
	{
		return ($this->getTranslatable()->getNameUtf8()) ? $this->getTranslatable()->getNameUtf8() : '';

	}



}