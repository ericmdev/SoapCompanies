<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Quote;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $companies = $this->getDoctrine()->getRepository('AppBundle:Company')
                                        ->findAll();

        $data = [
            'content' => [
                'title' => 'Eric Mugerwa',
                'subtitle' => 'Web Developer',
            ],
            'meta' => [
                'site_name' => 'Soap Companies',
                'title' => 'Soap Companies &#124; Web App',
                'description' => 'Simple SOAP application.',
                'author' => 'Eric Mugerwa (EricMugerwa.com)',
            ],
            'companies' => $companies
        ];

        return $this->render(
            'AppBundle:Default:index.html.twig',
            $data);
    }
}
