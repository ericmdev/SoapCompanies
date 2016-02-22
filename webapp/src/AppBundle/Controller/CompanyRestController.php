<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use AppBundle\Entity\Company;

use SoapClient;
use Exception;
use RuntimeException;

class CompanyRestController extends Controller
{
    /**
     * Get a list of available companies.
     *
     * @return array
     */
    public function getCompaniesAction()
    {
        # Retrieve available companies.
        $auth = array(
                    'username'  => $this->container->getParameter('soap_username'),
                    'password'  => $this->container->getParameter('soap_password')
                );
        $client = new SoapClient($this->container->getParameter('soap_wsdl'));

        try {
            $response = $client->__soapCall("getCompanies", [$auth]);
        } catch (Exception $e) {
            throw new RuntimeException(
                        'Failed to get companies.'
                    );
        }

        if(!is_array($response)){
            throw $this->createNotFoundException();
        }

        # Save companies to database.
        $companies = [];

        foreach ($response as $data) {
            # Determine if already exists.
            $company = $this->getDoctrine()->getRepository('AppBundle:Company')
                                        ->findOneByName($data->name);

            if (!$company) {
                $company = new Company();
                $company->setName($data->name);
                $company->setSymbol($data->symbol);

                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();

                sleep(1);
            }

            array_push($companies, $company);
        }

        return $companies;
    }
}
