<?php

namespace Ascendens\Igcrawler\Test;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\Http\Utils\UrlHelper;
use InvalidArgumentException;

class UrlHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid verifiable URL: http:///example.com
     */
    public function testIncorrectVerifiableUrl()
    {
        new UrlHelper('http:///example.com', 'http://localhost');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid root URL: http:///example.com
     */
    public function testIncorrectRootUrl()
    {
        new UrlHelper('http://localhost', 'http:///example.com');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Root URL must be absolute: example.com
     */
    public function testNotAbsoluteRootUrl()
    {
        new UrlHelper('sub.html', 'example.com');
    }

    public function testIsRelative()
    {
        $urlHelper = new UrlHelper('http://localhost/section', 'http://localhost');
        $this->assertFalse($urlHelper->isRelative());

        $urlHelper = new UrlHelper('http://google.com', 'http://localhost');
        $this->assertFalse($urlHelper->isRelative());

        $urlHelper = new UrlHelper('/section', 'http://google.com');
        $this->assertTrue($urlHelper->isRelative());

        $urlHelper = new UrlHelper('/section.php', 'http://google.com');
        $this->assertTrue($urlHelper->isRelative());

        $urlHelper = new UrlHelper('?section', 'http://google.com');
        $this->assertTrue($urlHelper->isRelative());

        $urlHelper = new UrlHelper('/section?param=1', 'http://google.com');
        $this->assertTrue($urlHelper->isRelative());
    }

    public function testIsExternal()
    {
        $urlHelper = new UrlHelper('http://localhost/section', 'http://localhost');
        $this->assertFalse($urlHelper->isExternal());

        $urlHelper = new UrlHelper('http://google.com', 'http://localhost');
        $this->assertTrue($urlHelper->isExternal());

        $urlHelper = new UrlHelper('/section', 'http://google.com');
        $this->assertFalse($urlHelper->isExternal());

        $urlHelper = new UrlHelper('/section.php', 'http://google.com');
        $this->assertFalse($urlHelper->isExternal());

        $urlHelper = new UrlHelper('?section', 'http://google.com');
        $this->assertFalse($urlHelper->isExternal());

        $urlHelper = new UrlHelper('/section?param=1', 'http://google.com');
        $this->assertFalse($urlHelper->isExternal());
    }

    public function testGetFormatted()
    {
        $urlHelper = new UrlHelper('/section', 'http://localhost');

        $formatted = $urlHelper->getFormatted('{scheme}://{host}');
        $this->assertEquals('http://localhost', $formatted);

        $formatted = $urlHelper->getFormatted('{scheme}://{host}{path}?{query}');
        $this->assertEquals('http://localhost/section?', $formatted);

        $urlHelper = new UrlHelper('section.html?param1=value1&param2=value2', 'http://localhost');

        $formatted = $urlHelper->getFormatted('//{host}/{path}?{query}');
        $this->assertEquals('//localhost/section.html?param1=value1&param2=value2', $formatted);
    }
}
