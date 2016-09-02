<?php

namespace Ascendens\Igcrawler\Test\WebPageProcessor\Decorator;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\WebPageProcessor\HtmlTagProcessorInterface;
use Ascendens\Igcrawler\WebPageProcessor\Decorator\Href;
use InvalidArgumentException;

class HrefTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Input must be an array
     */
    public function testIncorrectDelegateResult()
    {
        $processor = new Href($this->getDelegateMock('div', 'result'));
        $this->assertEquals('div', $processor->getTag());
        $processor->process('Some code');
    }

    public function testCorrectProcessing()
    {
        $processor = new Href($this->getDelegateMock('a', [
            '<a href="http://google.com">Google</a>',
            '<A href=\'http://yahoo.com\'>Yahoo</A>',
            '<A href=\'http://example.com\Yahoo</A>',
        ]));
        $result = $processor->process('Some code');
        $this->assertEquals([
            'http://google.com',
            'http://yahoo.com',
            null
        ], $result);
    }

    /**
     * @param string $tag
     * @param mixed $willReturn
     * @return HtmlTagProcessorInterface
     */
    private function getDelegateMock($tag, $willReturn = null)
    {
        $delegate = $this->createMock(HtmlTagProcessorInterface::class);
        $delegate->method('getTag')->willReturn($tag);
        $delegate->method('process')->willReturn($willReturn);

        return $delegate;
    }
}
