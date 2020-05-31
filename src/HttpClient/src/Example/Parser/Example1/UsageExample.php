<?php


namespace HttpClient\Example\Parser\Example1;

class UsageExample
{
    public static function example1()
    {
        // create Parser Strategies
        $strategies = [
            new ParserStrategyDesktopLayout(),
            new ParserStrategyMobileLayout(),
        ];

        // create validator
        $validator = new ParserValidator();

        // create Parser
        $parser = new SimpleParser(
            $strategies,
            $validator
        );

        // usage
        $html = '<html lang="en"><body><item class="desktop">ItemName</body></html>';
        $parsedData = $parser->__invoke($html);
        return $parsedData;
    }
}