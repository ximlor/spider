<?php
namespace Library;

class Curl
{
    protected $ch = null;

    protected $options = [
        'CURLOPT_RETURNTRANSFER' => true,
    ];

    protected $data = [

    ];

    protected $url = '';

    static function init()
    {
        return new self();
    }

    public function withOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function url($url)
    {
        $this->url = $url;
        return $this;
    }

    public function data(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function get()
    {
        if (is_array($this->data) && count($this->data) > 0) {
            $this->url .= '?' . http_build_query($this->data);
        }
        return $this->send();
    }

    protected function send()
    {
        $this->ch = curl_init();
        $opts = [];
        foreach ($this->options as $key => $option) {
            $opts[constant($key)] = $this->options;
        }
        curl_setopt_array($this->ch, $opts);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        $result = curl_exec($this->ch);
        curl_close($this->ch);
        return $result;
    }
}
