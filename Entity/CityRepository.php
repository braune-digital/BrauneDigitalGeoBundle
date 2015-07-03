<?php

namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\EntityRepository;


class CityRepository
{
    public function findAll($showAll = false){
        if($showAll)
        {
            return parent::findAll();
        }
        else{
            //filter sub regions
            $query = $this->getEntityManager()->createQuery("SELECT c FROM ApplicationBrauneDigitalGeoBundle:City c WHERE c.fcode <> 'PPLX' OR c.fcode IS NULL");
            return $query->getResult();
        }
    }
}