<?php
namespace BrauneDigital\GeoBundle\Request\ParamConverter;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityParamConverter implements ParamConverterInterface {


	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}

	public function apply(Request $request, ParamConverter $configuration)
	{

		if ($request->attributes->get('city_id')) {
			$city = $this->em->getRepository('ApplicationBrauneDigitalGeoBundle:City')->find($request->attributes->get('city_id'));
		} else {

			$slug = $request->attributes->get('city');

			$query = $this->em
				->createQuery(
					'SELECT c FROM ApplicationBrauneDigitalGeoBundle:City c '
					. 'JOIN c.translations ctr '
					. 'WHERE ctr.slug = :slug OR c.id = :id '
					. 'ORDER BY c.id asc ')
				->setParameters(array(
					'slug' => $slug,
					'id' => $slug
				));
			$cities = $query->getResult();

			// Care for old slugs
			if (count($cities) == 0) {
				$query = $this->em
					->createQuery(
						'SELECT c FROM ApplicationBrauneDigitalGeoBundle:City c '
						. 'WHERE c.oldSlug = :slug '
						. 'ORDER BY c.id asc ')
					->setParameters(array(
						'slug' => $slug
					));
				$cities = $query->getResult();
				if (count($cities) > 0) {
					$city = $cities[0];
					$request->attributes->set('redirect', true);
				} else {
					throw new NotFoundHttpException();
				}
			} else {
				$city = $cities[0];
			}
		}

		$param = $configuration->getName();
		$request->attributes->set($param, $city);

		return true;
	}

	public function supports(ParamConverter $configuration)
	{
		return "Application\BrauneDigital\GeoBundle\Entity\City" === $configuration->getClass();
	}


}