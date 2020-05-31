<?php


namespace HttpClient\Example\HttpResponseValidator\Amazon;


use HttpClient\HttpResponseValidator\AbstractHttpResponseValidator;
use Psr\Http\Message\ResponseInterface;

class AmazonBotDetectionResponseValidator extends AbstractHttpResponseValidator
{
    /**
     * @param ResponseInterface $value
     *
     * @return bool
     */
    public function isValid($value)
    {

        if ($this->checkOnAmazonCaptcha($value->getBody()->__toString())) {
            $this->messages[] = 'Amazon detects us as bot';
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
        $botDetectionTextSignature = 'To discuss automated access to Amazon data please contact';
        if (stripos($html, $botDetectionTextSignature) === false) {
            return false;
        }

        return true;
    }

}