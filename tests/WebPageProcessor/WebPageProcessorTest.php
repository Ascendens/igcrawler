<?php

namespace Ascendens\Igcrawler\Test\WebPageProcessor;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\WebPageProcessor\WebPageProcessor;
use Ascendens\Igcrawler\Http\HttpClientInterface;
use Ascendens\Igcrawler\WebPageProcessor\TagFinder\HtmlAFinder;
use Ascendens\Igcrawler\WebPageProcessor\TagFinder\HtmlImgFinder;

class WebPageProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function testNoProcessors()
    {
        $webPageProcessor = new WebPageProcessor($this->getHttpClient());
        $result = $webPageProcessor('http://www.some.com');
        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertArrayHasKey('duration', $result);
    }

    public function testCommonProcessing()
    {
        $webPageProcessor = new WebPageProcessor($this->getHttpClient());
        $webPageProcessor
            ->addProcessor(new HtmlAFinder())
            ->addProcessor(new HtmlImgFinder());
        $result = $webPageProcessor('http://www.some.com');
        $this->assertTrue(is_array($result));
        $this->assertEquals(3, count($result));
        $this->assertArraySubset([
            'a' => [
                '<a href="someUrl.html">Some URL</a>',
                '<A href=\'anotherUrl.html\'>Another URL</A>'
            ],
            'img' => [
                '<img src="some.jpg" />',
                '<IMG src="some1.jpg">'
            ]
        ], $result);
        $this->assertArrayHasKey('duration', $result);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->httpClient = $this->getHttpClient();
    }

    /**
     * @return HttpClientInterface
     */
    private function getHttpClient()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn('<div>
            <img src="some.jpg" />
            <div>
                <a href="someUrl.html">Some URL</a>
            </div>
            <A href=\'anotherUrl.html\'>Another URL</A>
            <abbr>Text</abbr>
            <IMG src="some1.jpg">
        </div>');

        return $httpClient;
    }
}
