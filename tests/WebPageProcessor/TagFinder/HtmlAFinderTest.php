<?php

namespace Ascendens\Igcrawler\Test\WebPageProcessor\TagFinder;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\WebPageProcessor\TagFinder\HtmlAFinder;

class HtmlAFinderTest extends PHPUnit_Framework_TestCase
{
    public function testProcessing()
    {
        $finder = new HtmlAFinder();
        $result = $finder->process('<div>
            <img src="some.jpg" />
            <div>
                <a href="someUrl.html">Some URL</a>
            </div>
            <A href=\'anotherUrl.html\'>Another URL</A>
            <abbr>Text</abbr>
            <a>
        </div>');
        $this->assertEquals([
            '<a href="someUrl.html">Some URL</a>',
            '<A href=\'anotherUrl.html\'>Another URL</A>'
        ], $result);
    }

    public function testProcessingCodeWithoutRequiredTags()
    {
        $finder = new HtmlAFinder();
        $result = $finder->process('<div></div>');
        $this->assertEquals([], $result);
    }
}
