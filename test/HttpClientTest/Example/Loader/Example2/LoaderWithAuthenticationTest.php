<?php


namespace HttpClientTest\Example\Loader\Example2;


use HttpClient\Example\Loader\Example2\UsageExample;
use PHPUnit\Framework\TestCase;

class LoaderWithAuthenticationTest extends TestCase
{
    public function testUsage()
    {
        $html = UsageExample::example1();
        $this->assertEquals('test body', $html);
    }
}