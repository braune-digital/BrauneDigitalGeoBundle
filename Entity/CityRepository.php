<?php

namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\EntityRepository;


class CityRepository extends AutoCompleteRepository
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

    public function autoComplete($property, $value, $options, $maxRows = -1) {

        if(array_key_exists('case_insensitive', $options) && $options['case_insensitive']) {
            $where_clause = 'LOWER(ctr.' . $property . ') LIKE LOWER(:like)';
        } else {
            $where_clause = 'ctr.' . $property. ' LIKE :like';
        }

        $whereClauseFcode = "(e.fcode <> 'PPLX' OR e.fcode IS NULL)";
        //based on translations!
        $queryBuilder = $this->createQueryBuilder('e')->leftJoin('e.translations', 'ctr')->where($where_clause)->andWhere($whereClauseFcode)->orderBy("ctr." . $property)->setParameter("like", $value);

        if($maxRows != -1) {
            $queryBuilder->setMaxResults($maxRows);
        }
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

}