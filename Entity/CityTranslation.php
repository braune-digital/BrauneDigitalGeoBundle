<?php
namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Application\AppBundle\Entity\SeoBase;
use JMS\Serializer\Annotation as JMS;

class CityTranslation
{

	use \BrauneDigital\GeoBundle\Model\Base;
	use \BrauneDigital\GeoBundle\Model\SeoBase;

	/**
	 * Name (UTF-8 encoded)
	 * @var string
	 * @JMS\Expose
	 */
	protected $nameUtf8;


    /**
     * Description
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