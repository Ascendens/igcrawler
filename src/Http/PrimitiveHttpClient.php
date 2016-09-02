<?php

namespace Ascendens\Igcrawler\Http;

use InvalidArgumentException;

class PrimitiveHttpClient implements HttpClientInterface
{
    /**
     * Makes request to the URL and returns content
     *
     * @param string $url Scheme and host is required
     * @param string $method Not used here
     * @return string
     */
    public function request($url, $method = 'GET')
    {
        $this->validateUrl($url);
        $content = @file_get_contents($url);
        if (false === $content) {
            throw new InvalidArgumentException(sprintf("Can't get access to the URL: %s", $url));
        }

        return $content;
    }

    /**
     * @param string $url
     * @throws InvalidArgumentException
     */
    private function validateUrl($url)
    {
        $urlData = parse_url($url);
        if (false === $urlData) {
            throw new InvalidArgumentException(sprintf("URL is invalid: %s", $url));
        }
        if (!isset($urlData['scheme'])) {
            throw new InvalidArgumentException(sprintf("URL scheme is missing: %s", $url));
        }
    }
}
