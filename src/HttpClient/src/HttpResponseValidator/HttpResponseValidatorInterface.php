<?php


namespace HttpClient\HttpResponseValidator;


use GuzzleHttp\Exception\RequestException;
use Zend\Validator\ValidatorInterface;

interface HttpResponseValidatorInterface extends ValidatorInterface
{
    //public function getRequestException(): RequestException;
}