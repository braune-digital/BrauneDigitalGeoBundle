<?php
namespace BrauneDigital\GeoBundle\Request\ParamConverter;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CountryParamConverter implements ParamConverterInterface {

	private $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}

    public function apply(Request $request, ParamConverter $configuration)
    {

        if ($request->attributes->get('country_id')) {
            $city = $this->em->getRepository('BrauneDigitalGeoBundle:Country')->find($request->attributes->get('country_id'));
        } else {

            $slug = $request->attributes->get('country');
            $query = $this->em
                ->createQuery(
                    'SELECT c FROM BrauneDigitalGeoBundle:Country c '
                    . 'JOIN c.translations ctr '
                    . 'WHERE ctr.slug = :slug OR c.id = :id '
                    . 'ORDER BY c.id asc ')
                ->setParameters(array(
                    'slug' => $slug,
                    'id' => $slug
                ));
            $countries = $query->getResult();

            // Care for old slugs
            if (count($countries) > 0) {
                $country = $countries[0];
            } else {
                $country = null;
            }
        }

        $param = $configuration->getName();
        $request->attributes->set($param, $country);

        return true;
    }

	public function supports(ParamConverter $configuration)
	{
		return "Application\BrauneDigital\GeoBundle\Entity\Country" === $configuration->getClass();
	}


}