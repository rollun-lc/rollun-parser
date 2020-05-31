<?php


namespace HttpClient\Example\Parser\Example1;


use Zend\Validator\ValidatorInterface;

class ParserValidator implements ValidatorInterface
{
    protected $messages;

    public function isValid($value)
    {
        $this->messages = [];
        if(!is_array($value)) {
            $this->messages[] = 'Result must be an array.';
            return false;
        }

        if(!count($value)) {
            $this->messages[] = 'Result must have one item at least.';
            return false;
        }

        return true;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}