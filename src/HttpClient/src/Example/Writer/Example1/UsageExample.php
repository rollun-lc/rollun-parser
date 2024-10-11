<?php


namespace HttpClient\Example\Writer\Example1;

use rollun\datastore\DataStore\Memory;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LikeNode;
use Xiag\Rql\Parser\Query;

class UsageExample
{
    public static function example1()
    {
        // create dataStore
        $dataStore = new Memory(['id', 'name']);

        // create Writer
        $writer = new SimpleWriter($dataStore);

        // usage
        $data = [
            ['id' => '00001', 'name' => 'Title1'],
            ['id' => '00002', 'name' => 'Title2'],
        ];
        $writer->write($data);

        //read data from dataStore
        $query = new Query();
        $query->setQuery(
            new LikeNode('name', '%it%')
        );
        $writtenData = $dataStore->query($query);

        return $writtenData;
    }
}