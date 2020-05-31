<?php


namespace rollun\parser;


interface StrategyInterface
{
    /**
     * @param string $html
     * @return mixed
     */
    public function parse(string $html);

}