<?php

error_reporting(0);

try {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
    require_once dirname(__FILE__) . '/app/bootstrap.php';
} catch (Exception $e) {
    die('Error on initialization');
}

if ($argc < 2) {
    echo 'URL is required and must be first CLI parameter';
    die();
}
$crawler->crawl($argv[1], isset($argv[2]) ? $argv[2] : null);
