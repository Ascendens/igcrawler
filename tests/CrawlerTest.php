<?php

namespace Ascendens\Igcrawler\Test;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\Crawler;
use Ascendens\Igcrawler\WebPageProcessor\WebPageProcessor;
use Ascendens\Igcrawler\Http\HttpClientInterface;
use Ascendens\Igcrawler\Report\ReportGeneratorInterface;
use Ascendens\Igcrawler\Logger\LoggerFactory;
use Ascendens\Igcrawler\Logger\LoggerInterface;
use ArrayObject;
use Closure;
use InvalidArgumentException;

class CrawlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $httpResponses = [
        'http://someUrl.com/?' => '<div>
                    <img src="some.jpg" />
                    <div>
                        <a href="internalUrl.html">Internal URL</a>
                    </div>
                    <A href=\'http://www.google.com\'>Another URL</A>
                    <abbr>Text</abbr>
                    <IMG src="some1.jpg">
                </div>',
        'http://someUrl.com/internalUrl.html?' => '<div>
                <img src="some.jpg" />
                <a href="/">Some URL</a>
                <a href="http://someUrl.com">Some URL</a>
                <a href="errorInternalUrl">Error URL</a>
            </div>',
        'http://emptyContent.com/?' => '',
    ];

    /**
     * @var LoggerFactory
     */
    private $loggerFactory;

    /**
     * @var string
     */
    private $loggedMessages;

    /**
     * @var ArrayObject
     */
    private $reportData;


    public function testInvalidUrl()
    {
        $crawler = $this->getCrawler();
        $crawler->crawl('http:///example.com');
        $this->assertEquals('Can\'t process url: http:///example.com', $this->loggedMessages[0]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Logger factory must have defined "console" and "file" factories
     */
    public function testMissingLoggers()
    {
        $this->loggerFactory->remove('file');
        $crawler = $this->getCrawler();
        $crawler->crawl('http:///example.com');
    }

    public function testCommonProcessing()
    {
        $crawler = $this->getCrawler();
        $crawler->crawl('http://someUrl.com');
        $this->assertEquals(2, count($this->reportData));
        $this->assertArraySubset([
            'url' => 'http://someUrl.com/?',
            'imgCount' => 2
        ], $this->reportData[0]);
        $this->assertEquals('URL has been processed: http://someUrl.com/?', $this->loggedMessages[0]);
        $this->assertArraySubset([
            'url' => 'http://someUrl.com/internalUrl.html?',
            'imgCount' => 1
        ], $this->reportData[1]);
        $this->assertEquals('URL has been processed: http://someUrl.com/internalUrl.html?', $this->loggedMessages[1]);
        $this->assertEquals('Can\'t load url: http://someUrl.com/errorInternalUrl?', $this->loggedMessages[2]);
    }

    public function testNoDataToSave()
    {
        $crawler = $this->getCrawler();
        $crawler->crawl('///');
        $this->assertEquals('Can\'t process url: ///', $this->loggedMessages[0]);
        $this->assertEquals('Nothing to save', $this->loggedMessages[1]);
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loggedMessages = [];
        $this->reportData = new ArrayObject();
        $this->loggerFactory = $this->getLoggerFactory();
    }

    /**
     * @return Crawler
     */
    private function getCrawler()
    {
        $crawler = new Crawler(
            new WebPageProcessor($this->getHttpClient()),
            $this->getReportGenerator(),
            $this->loggerFactory
        );

        return $crawler;
    }

    /**
     * @return HttpClientInterface
     */
    private function getHttpClient()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $callback = Closure::bind(function ($url) {
            if(!array_key_exists($url, $this->httpResponses)) {
                throw new InvalidArgumentException();
            }

            return $this->httpResponses[$url];
        }, $this);
        $httpClient->method('request')->willReturnCallback($callback);

        return $httpClient;
    }

    /**
     * @return ReportGeneratorInterface
     */
    private function getReportGenerator()
    {
        $reportGenerator = $this->createMock(ReportGeneratorInterface::class);
        $callback = Closure::bind(function ($data) {
            $this->reportData->append($data);
        }, $this);
        $reportGenerator->method('add')->willReturnCallback($callback);
        $reportGenerator->method('getData')->willReturn($this->reportData);
        $reportGenerator->method('generate')->willReturn(true);

        return $reportGenerator;
    }

    /**
     * @return LoggerFactory
     */
    private function getLoggerFactory()
    {
        $factory = new LoggerFactory();
        // Console logger
        $consoleLogger = $this->createMock(LoggerInterface::class);
        $callback = Closure::bind(function ($message) {
            $this->loggedMessages[] = $message;
        }, $this);
        $consoleLogger->method('log')->willReturnCallback($callback);
        $factory->add('console', function () use ($consoleLogger) {
            return $consoleLogger;
        });
        // File logger
        $fileLogger = $this->createMock(LoggerInterface::class);
        $fileLogger->method('log')->willReturn(true);
        $factory->add('file', function () use ($fileLogger) {
            return $fileLogger;
        });

        return $factory;
    }
}
