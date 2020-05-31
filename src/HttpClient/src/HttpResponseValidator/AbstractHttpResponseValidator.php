<?php


namespace HttpClient\HttpResponseValidator;

abstract class AbstractHttpResponseValidator implements HttpResponseValidatorInterface
{
    /**
     * @var array
     */
    protected $messages;

    abstract public function isValid($value);

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}