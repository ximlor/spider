<?php

ini_set("memory_limit", "1024M");

error_reporting(-1);

define('BASE_PATH', realpath(dirname(dirname(__FILE__))));

require __DIR__ . '/../package/autoload/autoloader.php';

$cfg = require BASE_PATH . '/config/ziroom.php';

$app = new \Core\Application($cfg);
