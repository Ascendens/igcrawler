<?php

namespace Ascendens\Igcrawler\Test\Logger;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\Logger\FileLogger;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FileLoggerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    private $virtualFs;


    public function testCreateFile()
    {
        $file = vfsStream::url('root/report.html');
        $this->assertFileNotExists($file);
        $logger = new FileLogger($file);
        $logger->log('Some message');
        $this->assertFileExists($file);
        $this->assertEquals('Some message', file_get_contents($file));
    }

    public function testRewriteFile()
    {
        $file = vfsStream::url('root/existentReport.txt');
        $this->assertFileExists($file);
        $logger = new FileLogger($file);
        $logger->log('New report data');
        $this->assertEquals('New report data', file_get_contents($file));
    }

    public function testAppendFile()
    {
        $file = vfsStream::url('root/appendReport.txt');
        $content = file_get_contents($file);
        $this->assertFileExists($file);
        $logger = new FileLogger($file, false);
        $logger->log('. And one more sentence.');
        $this->assertEquals($content . '. And one more sentence.', file_get_contents($file));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLogToIncorrectLocation()
    {
        $file = vfsStream::url('nowhere/report.txt');
        $this->assertFileNotExists($file);
        $logger = new FileLogger($file);
        $logger->log('New report data');
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->virtualFs = vfsStream::setup('root', null, [
            'existentReport.txt' => 'Existent report data',
            'appendReport.txt' => 'Append report data'
        ]);
    }
}
