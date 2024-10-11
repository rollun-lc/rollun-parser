<?php


namespace HttpClientTest\Example\Writer\Example1;


use HttpClient\Example\Writer\Example1\SimpleWriter;
use HttpClient\Example\Writer\Example1\UsageExample;
use PHPUnit\Framework\TestCase;
use rollun\datastore\DataStore\Memory;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LikeNode;
use Xiag\Rql\Parser\Query;

class SimpleWriterTest extends TestCase
{

    protected function createObject($dataStore)
    {
        return new SimpleWriter($dataStore);
    }

    public function testUsage()
    {
        $expectedResult = [
            ['id' => '00001', 'name' => 'Title1'],
            ['id' => '00002', 'name' => 'Title2'],
        ];
        $result = UsageExample::example1();
        $this->assertEquals($expectedResult, $result);
    }

    public function testCreate()
    {
        $writer = $this->createObject(new Memory());

        $this->assertInstanceOf(SimpleWriter::class, $writer);
    }
}