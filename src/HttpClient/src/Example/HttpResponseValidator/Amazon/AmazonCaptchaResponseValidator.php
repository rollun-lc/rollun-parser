<?php


namespace HttpClient\Example\HttpResponseValidator\Amazon;


use HttpClient\HttpResponseValidator\AbstractHttpResponseValidator;
use Psr\Http\Message\ResponseInterface;

class AmazonCaptchaResponseValidator extends AbstractHttpResponseValidator
{
    /**
     * @param ResponseInterface $value
     *
     * @return bool
     */
    public function isValid($value)
    {

        if ($this->checkOnAmazonCaptcha($value->getBody()->__toString())) {
            $this->messages[] = 'Amazon returns captcha';
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
    protected function checkOnAmazonCaptcha(string $html): bool
    {
        $captchaMarkOnPage = "Type the characters you see in this image";
        if (stripos($html, $captchaMarkOnPage) === false) {
            return false;
        }

        return true;
    }

}