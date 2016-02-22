<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="quote")
 */
class Quote
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="company_id")
     */
    protected $companyId;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $value;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datetime;

    public function __construct()
    {
        $this->datetime = new DateTime(); 
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set companyId
     *
     * @param integer $companyId
     *
     * @return Quote
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get companyId
     *
     * @return integer
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set quote
     *
     * @param string $quote
     *
     * @return Quote
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return string
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return Quote
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Quote
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
