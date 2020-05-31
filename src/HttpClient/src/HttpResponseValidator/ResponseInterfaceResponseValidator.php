<?php


namespace HttpClient\HttpResponseValidator;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseInterfaceResponseValidator
 *
 * @package HttpClient\HttpResponseValidator
 */
class ResponseInterfaceResponseValidator extends AbstractHttpResponseValidator
{
    /**
     * @param $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        if (!$value instanceof ResponseInterface) {
            $this->messages[] = "Value is not implement " . ResponseInterface::class;
            return false;
        }
        return true;
    }

}