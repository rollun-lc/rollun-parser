<?php


namespace HttpClient\HttpResponseValidator;


use Psr\Http\Message\ResponseInterface;
use Zend\Validator\ValidatorInterface;

class StatusOk implements ValidatorInterface
{
    protected $message = null;

    public function isValid($value)
    {
        if (!$value instanceof ResponseInterface) {
            $this->message = "Value is not implement " . ResponseInterface::class;
            return false;
        }

        if ($value->getStatusCode() != 200) {
            $this->message = "Reason phrase is {$value->getReasonPhrase()}";
            return false;
        }

        return true;
    }

    public function getMessages()
    {
        return $this->message;
    }
}
