<?php

namespace Ascendens\Igcrawler\Http;

use InvalidArgumentException;

/**
 * Very simple HTTP request client interface
 */
interface HttpClientInterface
{
    /**
     * Makes request to the URL and returns content
     *
     * @param string $url
     * @param string $method Valid
     * @return mixed
     * @throws InvalidArgumentException If can't get content
     */
    public function request($url, $method = 'GET');
}
