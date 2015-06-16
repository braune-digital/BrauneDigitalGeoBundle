<?php

namespace BrauneDigital\GeoBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AutocompleteRepository
 */
class AutoCompleteRepository extends EntityRepository
{
  public function autoComplete($property, $value, $options, $maxRows = -1) {

      if(array_key_exists('case_insensitive', $options) && $options['case_insensitive']) {
          $where_clause = 'LOWER(ctr.' . $property . ') LIKE LOWER(:like)';
      } else {
          $where_clause = 'ctr.' . $property. ' LIKE :like';
      }


      //based on translations!
      $queryBuilder = $this->createQueryBuilder('e')->leftJoin('e.translations', 'ctr')->where($where_clause)->orderBy("ctr." . $property)->setParameter("like", $value);

      if($maxRows != -1) {
          $queryBuilder->setMaxResults($maxRows);
      }
      $query = $queryBuilder->getQuery();
      return $query->getResult();
  }
}
