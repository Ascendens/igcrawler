<?php

namespace Ascendens\Igcrawler\Logger;

use InvalidArgumentException;

class FileLogger implements LoggerInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var bool
     */
    private $rewrite;

    /**
     * @param string $filename File name to save data to
     * @param bool $rewrite Should content be overwritten
     */
    public function __construct($filename, $rewrite = true)
    {
        $this->filename = (string) $filename;
        $this->rewrite = $rewrite;
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException If can't write report to the file
     */
    public function log($message)
    {
        if (false === @file_put_contents($this->filename, $message, $this->rewrite ?: FILE_APPEND)) {
            throw new InvalidArgumentException(sprintf("Can't write report to the file: %s", $this->filename));
        }
    }
}
