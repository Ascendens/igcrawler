<?php

namespace Ascendens\Igcrawler\Logger;

use RuntimeException;

/**
 * Very very simple output to console
 */
class ConsoleLogger implements LoggerInterface
{
    /**
     * @var resource
     */
    private $outputStream;

    public function __construct()
    {
        $this->outputStream = @fopen('php://stdout', 'w') ?: fopen('php://output', 'w');
    }

    /**
     * @inheritdoc
     */
    public function log($message)
    {
        if (false === @fwrite($this->outputStream, sprintf('%s%s', $message, PHP_EOL))) {
            throw new RuntimeException('Unable to write output.');
        }
        fflush($this->outputStream);
    }
}
