<?php

namespace HttpClientTest\Example\Parser\Example1;

use HttpClient\Example\Parser\Example1\ParserStrategyDesktopLayout;
use HttpClient\Example\Parser\Example1\ParserStrategyMobileLayout;
use HttpClient\Example\Parser\Example1\ParserValidator;
use HttpClient\Example\Parser\Example1\SimpleParser;
use HttpClient\Example\Parser\Example1\UsageExample;
use PHPUnit\Framework\TestCase;

class SimpleParserTest extends TestCase
{
    protected function createObject()
    {
        return new SimpleParser(
            [
                new ParserStrategyDesktopLayout(),
                new ParserStrategyMobileLayout(),
            ],
            new ParserValidator()
        );
    }

    public function testUsage()
    {
        $parsedData = UsageExample::example1();
        $this->assertIsArray($parsedData);
    }

    public function testWithDesktopLayoutHtml()
    {
        $parser = $this->createObject();
        // usage
        $html = '<html lang="en"><body><item class="desktop">ItemName</body></html>';
        $parsedData = $parser->__invoke($html);
        $this->assertEquals(['ItemName'], $parsedData);
    }

    public function testWithMobileLayoutHtml()
    {
        $parser = $this->createObject();
        // usage
        $html = '<html lang="en"><body><item class="mobile">ItemName</body></html>';
        $parsedData = $parser->__invoke($html);
        $this->assertEquals(['ItemName'], $parsedData);
    }

    public function testWithUnknownLayoutHtml()
    {
        $parser = $this->createObject();
        // usage
        $html = '<html lang="en"><body><item class="">ItemName</body></html>';
        $message = null;
        try {
            $parsedData = $parser->__invoke($html);
        } catch (\RuntimeException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals('Not found strategy for valid parse this document.', $message);
    }
}
