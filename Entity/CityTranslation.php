<?php
namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Application\AppBundle\Entity\SeoBase;


use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity
 * @ORM\Table(name="geo_city_translation")
 */
class CityTranslation extends SeoBase
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
	 * @Expose
	 */
	protected $nameUtf8;


    /**
     * Description
     * @ORM\Column(name="description", type="text")
     * @var string
     */
    protected $description;

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

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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