<?php


namespace HttpClient\Example\HttpResponseValidator;


use HttpClient\HttpResponseValidator\AbstractHttpResponseValidator;

use Psr\Http\Message\ResponseInterface;

class RollunComNotFoundResponseValidator extends AbstractHttpResponseValidator
{
    /**
     * @param ResponseInterface $value
     *
     * @return bool
     */
    public function isValid($value)
    {

        if ($this->checkPageNotFound($value->getBody()->__toString())) {
            $this->messages[] = 'Page not found';
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
    private function checkPageNotFound(string $html): bool
    {
        if (stripos($html, "Oops! That page can&rsquo;t be found.") === false) {
            return false;
        }

        return true;
    }

}