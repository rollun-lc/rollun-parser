<?php


namespace HttpClient\Example\ParserCallback\Example1;


class SimpleParserCallback
{
    /**
     * @var callable
     */
    protected $loader;
    /**
     * @var callable
     */
    protected $parser;


    /**
     * SimpleParserCallback constructor.
     *
     * @param callable $loader
     * @param callable $parser
     */
    public function __construct(callable $loader, callable $parser)
    {
        $this->loader = $loader;
        $this->parser = $parser;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function __invoke($value)
    {
        try {
            $result = call_user_func($this->loader, $value);
            return call_user_func($this->parser, $result);
        } catch (\Throwable $exception) {
            throw new \RuntimeException("Has error by call callable.", $exception->getCode(), $exception);
        }
    }
}