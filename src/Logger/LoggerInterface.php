<?php

namespace Ascendens\Igcrawler\Logger;

interface LoggerInterface
{
    /**
     * @param string $message
     * @return mixed
     */
    public function log($message);
}
