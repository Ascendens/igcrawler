<?php

use Ascendens\Igcrawler\WebPageProcessor\WebPageProcessor;
use Ascendens\Igcrawler\Http\PrimitiveHttpClient;
use Ascendens\Igcrawler\Report\CrawlingTableReportGenerator;
use Ascendens\Igcrawler\Logger\LoggerFactory;
use Ascendens\Igcrawler\Logger\ConsoleLogger;
use Ascendens\Igcrawler\Logger\FileLogger;
use Ascendens\Igcrawler\Crawler;

$config = require_once 'config.php';

$webPageProcessor = new WebPageProcessor(new PrimitiveHttpClient());
$reportGenerator = new CrawlingTableReportGenerator(
    $config['table']['headers'],
    $config['table']['sortBy'],
    constant(sprintf('%s::SORT_%s', CrawlingTableReportGenerator::class, strtoupper($config['table']['sortDirection']))),
    $config['table']['attributes']
);
$loggerFactory = new LoggerFactory();
$loggerFactory
    ->add('console', function () {
        return new ConsoleLogger();
    })
    ->add('file', function ($filename) {
        return new FileLogger($filename);
    })
;
$crawler = new Crawler($webPageProcessor, $reportGenerator, $loggerFactory);