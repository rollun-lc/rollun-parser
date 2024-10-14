<?php

namespace HttpClient\Example\Parser\Example1;


use Laminas\Validator\ValidatorInterface;

class SimpleParser
{
    /**
     * @var callable[]
     */
    private array $strategies;

    private ValidatorInterface $validator;

    public function __construct(
        array $strategies,
        ValidatorInterface $validator
    ) {
        $this->strategies = $strategies;
        $this->validator = $validator;
    }

    /**
     * @param string $htmlData
     * @return mixed
     */
    public function __invoke(string $htmlData)
    {
        foreach ($this->strategies as $strategy) {
            $data = $strategy->parse($htmlData);
            if ($this->validator->isValid($data)) {
                return $data;
            }
        }

        if (strpos($htmlData, '</body>') !== false) {
            throw new \RuntimeException("Not found strategy for valid parse this document.");
        }
        throw new \RuntimeException('Not found end of body.');
    }
}