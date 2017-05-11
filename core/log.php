<?php

namespace Core;

class Log
{
    static protected $dir = 'data';

    protected $test;

    static public function write($msg, $file)
    {
        $path = self::getPath(static::$dir, $file);
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, $msg, FILE_APPEND);
    }

    static public function getPath($dir, $filename)
    {
        return BASE_PATH . DIRECTORY_SEPARATOR . trim($dir, '/') . DIRECTORY_SEPARATOR . trim($filename, '/');
    }

    static public function stage($msg)
    {
        self::write($msg, 'html/html-' . time());
    }
}