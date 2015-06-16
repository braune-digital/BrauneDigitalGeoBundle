<?php

namespace BrauneDigital\GeoBundle\Model;

use Doctrine\ORM\Mapping as ORM;


trait SeoBase
{

	/**
	 * @var
	 */
    protected $seoDescription;

	/**
	 * @var
	 */
    protected $seoTags;

    /**
     * @return mixed
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    /**
     * @param mixed $seoDescription
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;
    }

    /**
     * @return mixed
     */
    public function getSeoTags()
    {
        return $this->seoTags;
    }

    /**
     * @param mixed $seoTags
     */
    public function setSeoTags($seoTags)
    {
        $this->seoTags = $seoTags;
    }



}