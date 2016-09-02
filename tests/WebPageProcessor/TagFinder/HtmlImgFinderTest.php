<?php

namespace Ascendens\Igcrawler\Test\WebPageProcessor\TagFinder;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\WebPageProcessor\TagFinder\HtmlImgFinder;

class HtmlImgFinderTest extends PHPUnit_Framework_TestCase
{
    public function testProcessing()
    {
        $finder = new HtmlImgFinder();
        $result = $finder->process('<div>
            <img src="some.jpg" />
            <div>
                <a href="someUrl.html">Some URL</a>
            </div>
            <A href=\'anotherUrl.html\'>Another URL</A>
            <IMG src="some1.jpg">
        </div>');
        $this->assertEquals([
            '<img src="some.jpg" />',
            '<IMG src="some1.jpg">'
        ], $result);
    }

    public function testProcessingCodeWithoutRequiredTags()
    {
        $finder = new HtmlImgFinder();
        $result = $finder->process('<div></div>');
        $this->assertEquals([], $result);
    }
}
