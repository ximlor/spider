<?php

$type = [
    'all' => '3',
];

return [
    'name' => 'ziroom',
    'domain' => 'http://www.ziroom.com',
    'start_url' => 'http://www.ziroom.com/z/nl/z' . $type['all'] . '.html',
    'items' => "//ul[@id='houseList']/li",
    'parse' => [
        [
            'name' => 'url',
            'selector' => "div[contains(@class,'priceDetail')]/p[contains(@class,'more')]/a/@href",
        ],
        [
            'name' => 'oid',
            'selector' => "div[contains(@class,'priceDetail')]/p/a/@href",
            'regexp' => '|www.ziroom.com/[\w]+/[\w]+/(\d+)|i',
        ],
        [
            'name' => 'title',
            'selector' => "div[contains(@class,'txt')]/h3/a",
        ],
        [
            'name' => 'location',
            'selector' => "div[contains(@class,'txt')]/h4/a",
        ],
        [
            'name' => 'price',
            'selector' => "div[contains(@class,'priceDetail')]/p[contains(@class,'price')]",
        ],
    ],
];
