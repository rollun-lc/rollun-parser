<?php


namespace HttpClientTest\HttpClientMiddleware;

use Psr\Http\Message\ResponseInterface;
use Zend\Validator\ValidatorInterface;

class GoodBodyBadStatusCodeResponseValidator implements ValidatorInterface
{
    protected $messages;

    public function isValid($value)
    {
        if (!$value instanceof ResponseInterface) {
            $this->messages = "Value is not implement " . ResponseInterface::class;
            return false;
        }

        if ($value->getStatusCode() == 500) {
            $this->messages = "Bad StatusCode";
            return false;
        }

        if ($value->getBody()->__toString() != 'good') {
            $this->messages = "Bad Body";
            return false;
        }

        return true;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
