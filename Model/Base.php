<?php

namespace BrauneDigital\GeoBundle\Model;

trait Base
{

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->title) ? $this->title : "";
    }

	/**
	 * @return string
	 */
	public function createUniqueToken() {
		return md5($this->getId() . time());
	}
}
