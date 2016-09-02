<?php

namespace Ascendens\Igcrawler\Test\Http;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\Http\PrimitiveHttpClient;
use InvalidArgumentException;

class PrimitiveHttpClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage URL is invalid: http:///example.com
     */
    public function testInvalidUrl()
    {
        $httpClient = new PrimitiveHttpClient();
        $httpClient->request('http:///example.com');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage URL scheme is missing: //example.com
     */
    public function testMissingScheme()
    {
        $httpClient = new PrimitiveHttpClient();
        $httpClient->request('//example.com');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Can't get access to the URL: http://example.none
     */
    public function testNonExistentHost()
    {
        $httpClient = new PrimitiveHttpClient();
        $httpClient->request('http://example.none');
    }

    public function testSuccessRequest()
    {
        $httpClient = new PrimitiveHttpClient();
        $data = $httpClient->request('http://google.com'); // 99% it works
        $this->assertContains('Google', $data);
    }
}
