<?php

namespace Core;

use \Library\Curl;

class Application
{
    protected $config;

    protected $content;

    protected $items;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->bootstrap();
    }

    public function bootstrap()
    {
        $this->run();
    }

    protected function run()
    {
        $this->content = Curl::init()->url($this->config['start_url'])->data(['qwd' => '16号线'])->get();
        $this->getItems();
    }

    protected function getItems()
    {
        $selector = new Selector($this->content);
        $this->items = $selector->xPathSelector($this->config['items']);
        $datas = [];
        foreach ($this->items as $node) {
            $data = [];
            foreach ($this->config['parse'] as $filed) {
                $name = $filed['name'];
                $result = $selector->queryByNode($filed['selector'], $node);

                if (isset($filed['regexp'])) {
                    if (!preg_match($filed['regexp'], $result, $matches)) {
                        throw new \Error('parse error');
                    }

                    if (!is_array($matches)) {
                        throw new \Error('parse error');
                    }

                    list(, $result) = $matches;
                }
                $data[$name] = $result;
            }
            $datas[] = $data;
        }
        print_r($datas);
    }
}