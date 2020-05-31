<?php


namespace rollun\parser;


trait CallableStrategyTrait
{
    /**
     * @param string $html
     * @return mixed
     */
    public function __invoke(string $html)
    {
        return $this->parse($html);
    }

    abstract public function parse(string $html);

}