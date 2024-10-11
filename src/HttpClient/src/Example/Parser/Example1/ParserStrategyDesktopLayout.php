<?php


namespace HttpClient\Example\Parser\Example1;

use rollun\utils\HtmlParser\Simple;

class ParserStrategyDesktopLayout
{
    public function parse($html)
    {
        // create simple_html_dom object
        $document = new Simple($html);

        // find all elements with class '.desktop'
        $items = $document->find('.desktop');
        $products = [];
        foreach ($items as $item) {
            $products[] =  trim(str_replace([PHP_EOL, '  '], '', $item->plaintext));
        }
        return $products;
    }
}