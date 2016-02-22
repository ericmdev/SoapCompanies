<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use AppBundle\Entity\Company;

use SoapClient;
use Exception;
use RuntimeException;

class DirectorsRestController extends Controller
{
    /**
     * Get company directors by symbol.
     *
     * @param  string $symbol Company symbol.
     *
     * @return string
     */
    public function getDirectorsAction($symbol)
    {
        # Retrieve director's name.
        $auth = array(
                    'username'  => $this->container->getParameter('soap_username'),
                    'password'  => $this->container->getParameter('soap_password')
                );
        $client = new SoapClient($this->container->getParameter('soap_wsdl'));

        try {
            $response = $client->__soapCall("getDirectorsBySymbol", [$auth, $symbol]);
        } catch (Exception $e) {
            throw new RuntimeException(
                        sprintf('Failed to get directors for symbol: %s', $symbol)
                    );
        }
        
        return $response;
    }    
}
