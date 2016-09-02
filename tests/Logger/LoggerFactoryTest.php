<?php

namespace Ascendens\Igcrawler\Test\Logger;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\Logger\LoggerFactory;
use Ascendens\Igcrawler\Logger\LoggerInterface;

class LoggerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testAddAndExists()
    {
        $loggerFactory = new LoggerFactory();
        $this->assertFalse($loggerFactory->exists('logger'));
        $loggerFactory->add('logger', $this->generateLoggerFactory());
        $this->assertTrue($loggerFactory->exists('logger'));
        $addResult = $loggerFactory->add('oneMoreLogger', $this->generateLoggerFactory());
        $this->assertEquals($loggerFactory, $addResult);
    }

    public function testRemove()
    {
        $loggerFactory = new LoggerFactory();
        $loggerFactory->add('logger', $this->generateLoggerFactory());
        $this->assertTrue($loggerFactory->exists('logger'));
        $removeResult = $loggerFactory->remove('logger');
        $this->assertFalse($loggerFactory->exists('logger'));
        $this->assertEquals($loggerFactory, $removeResult);
    }

    public function testMake()
    {
        $loggerFactory = new LoggerFactory();
        $loggerFactory->add('logger', $this->generateLoggerFactory());
        $logger = $loggerFactory->make('logger');
        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function testMakeWithParameters()
    {
        $loggerFactory = new LoggerFactory();
        $loggerFactory->add('logger', $this->generateLoggerFactory(['param1', 'param2']));
        $logger = $loggerFactory->make('logger');
        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertEquals(['param1', 'param2'], $logger->log(''));
    }

    /**
     * Creates logger mock
     *
     * @param array $parameters
     * @return callable
     */
    private function generateLoggerFactory(array $parameters = [])
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('log')->willReturn($parameters);

        return function () use ($logger) {
            return $logger;
        };
    }
}
