<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use AppBundle\Entity\Quote;

use SoapClient;

class QuoteRestController extends Controller
{
    /**
     * Get stock quote for a company.
     *
     * @param  string $symbol Company symbol.
     *
     * @return Quote
     */
    public function getQuoteAction($symbol)
    {
        # Retrieve company.
        $company = $this->getDoctrine()->getRepository('AppBundle:Company')
                                    ->findOneBySymbol($symbol);
        if (!$company) {
            throw $this->createNotFoundException();
        }

        # Retrieve director's name.
        $auth = array(
                    'username'  => $this->container->getParameter('soap_username'),
                    'password'  => $this->container->getParameter('soap_password')
                );
        $client = new SoapClient($this->container->getParameter('soap_wsdl'));

        try {
            $response = $client->__soapCall("getQuote", [$auth, $symbol]);
        } catch (Exception $e) {
            throw new RuntimeException(
                        sprintf('Failed to get quote for symbol: %s', $symbol)
                    );
        }

        if(!is_string($response)){
            throw $this->createNotFoundException();
        }

        # Save quote to database.
        $quote = new Quote();
        $quote->setValue($response);
        $quote->setCompanyId($company->getId());
        $em = $this->getDoctrine()->getManager();
        $em->persist($quote);
        $em->flush();

        return $quote;
    }

    /**
     * Get history of 5 previous quotes for a company.
     *
     * @param  string $symbol Company symbol.
     *
     * @return array
     */
    public function getQuoteHistoryAction($symbol)
    {
        # Retrieve company.
        $company = $this->getDoctrine()->getRepository('AppBundle:Company')
                                    ->findOneBySymbol($symbol);
        if (!$company) {
            throw $this->createNotFoundException();
        }

        # Get current quote.
        $current_quote = $this->getQuoteAction($symbol);

        # Retrieve 5 previous quotes.
        $quotes = $this->getDoctrine()
                        ->getRepository('AppBundle:Quote')->findBy(
                            array('companyId' => $company->getId()),
                            array('datetime' => 'DESC'),
                            5,
                            0);

        return $quotes;
    }
}
