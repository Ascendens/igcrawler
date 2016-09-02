<?php

namespace Ascendens\Igcrawler\Test\WebPageProcessor\Decorator;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\WebPageProcessor\HtmlTagProcessorInterface;
use Ascendens\Igcrawler\WebPageProcessor\Decorator\Count;

class CountTest extends PHPUnit_Framework_TestCase
{
    public function testArrayResult()
    {
        $processor = new Count($this->getDelegateMock('div', [1, 2, 3]));
        $this->assertEquals('div', $processor->getTag());
        $result = $processor->process('Some code');
        $this->assertEquals(3, $result);
    }

    public function testStringDelegateResult()
    {
        $processor = new Count($this->getDelegateMock('div', 'result'));
        $this->assertEquals('div', $processor->getTag());
        $result = $processor->process('Some code');
        $this->assertEquals(1, $result);
    }

    public function testNullDelegateResult()
    {
        $processor = new Count($this->getDelegateMock('div', null));
        $this->assertEquals('div', $processor->getTag());
        $result = $processor->process('Some code');
        $this->assertEquals(0, $result);
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
