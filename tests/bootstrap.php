<?php

use Jardis\DotEnv\DotEnv;

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once dirname(__DIR__) . '/vendor/autoload.php';

$_ENV['APP_ENV'] = 'test';
(new DotEnv())->load(dirname(__DIR__));
