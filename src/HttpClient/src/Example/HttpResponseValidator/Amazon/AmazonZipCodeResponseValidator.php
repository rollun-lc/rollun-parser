<?php


namespace HttpClient\Example\HttpResponseValidator\Amazon;


use HttpClient\HttpResponseValidator\AbstractHttpResponseValidator;
use Psr\Http\Message\ResponseInterface;

class AmazonZipCodeResponseValidator extends AbstractHttpResponseValidator
{
    /**
     * @var string
     */
    protected $zipCode;

    /**
     * AmazonZipCodeResponseValidator constructor.
     *
     * @param $zipCode
     */
    public function __construct($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @param ResponseInterface $value
     *
     * @return bool
     */
    public function isValid($value)
    {

        if (!$this->checkOnZipCode($value->getBody()->__toString())) {
            $this->messages[] = 'Wrong zipCode';
            return false;
        }
        return true;
    }
    /**
     * check page on Captcha
     *
     * @param string $html
     *
     * @return bool
     */
    private function checkOnZipCode(string $html): bool
    {
        if (strlen($this->zipCode) && stripos($html, "Orange {$this->zipCode}") === false) {
            return false;
        }

        return true;
    }

}