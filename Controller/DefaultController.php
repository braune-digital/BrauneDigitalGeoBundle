<?php

namespace BrauneDigital\GeoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BrauneDigitalGeoBundle:Default:index.html.twig', array('name' => $name));
    }
}
