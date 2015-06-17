<?php
namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

class StateTranslation
{
	/**
	 * Name (UTF-8 encoded)
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