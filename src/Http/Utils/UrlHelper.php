<?php

namespace Ascendens\Igcrawler\Http\Utils;

use InvalidArgumentException;

/**
 * Some specific functions for URL
 */
class UrlHelper
{
    /**
     * @var array
     */
    private $verifiableUrlData;

    /**
     * @var array
     */
    private $rootUrlData;

    /**
     * @param string $verifiableURL
     * @param string $rootUrl Absolute URL to compare
     * @throws InvalidArgumentException If any URL is invalid or root URL is not absolute
     */
    public function __construct($verifiableURL, $rootUrl)
    {
        $this->verifiableUrlData = parse_url($verifiableURL);
        if (false === $this->verifiableUrlData) {
            throw new InvalidArgumentException(sprintf("Invalid verifiable URL: %s", $verifiableURL));
        }
        $this->rootUrlData = parse_url($rootUrl);
        if (false === $this->rootUrlData) {
            throw new InvalidArgumentException(sprintf("Invalid root URL: %s", $rootUrl));
        }
        if (!isset($this->rootUrlData['scheme'], $this->rootUrlData['host'])) {
            throw new InvalidArgumentException(sprintf("Root URL must be absolute: %s", $rootUrl));
        }
    }

    /**
     * Is verifiable URL relative
     *
     * @return bool
     */
    public function isRelative()
    {
        return !isset($this->verifiableUrlData['scheme'], $this->verifiableUrlData['host']);
    }

    /**
     * Is verifiable URL outside of root URL domain
     *
     * @return bool
     */
    public function isExternal()
    {
        return isset($this->verifiableUrlData['host']) && $this->verifiableUrlData['host'] != $this->rootUrlData['host'];
    }

    /**
     * Combines new URL according to given format. Keys from result of native "parse_url" is used as placeholders.
     * Example: {scheme}://{host}:80/{path}?{query}
     *
     * @param string $format
     * @param bool $fallbackWithRootUrlParts Should be missing parts given from root URL
     * @return string
     */
    public function getFormatted($format, $fallbackWithRootUrlParts = true)
    {
        preg_match_all('/{([a-z]+)}/i', $format, $matches);
        foreach ($matches[1] as $k => $component) {
            $format = str_replace($matches[0][$k], $this->getReplacement($component, $fallbackWithRootUrlParts), $format);
        }

        return $format;
    }

    /**
     * Finds replacement for required component
     *
     * @param string $component
     * @param bool $fallbackWithRootUrlParts Should be missing parts given from root URL
     * @return string
     */
    private function getReplacement($component, $fallbackWithRootUrlParts = true)
    {
        if(array_key_exists($component, $this->verifiableUrlData)) {
            return $this->verifiableUrlData[$component];
        } elseif ($fallbackWithRootUrlParts && array_key_exists($component, $this->rootUrlData)) {
            return $this->rootUrlData[$component];
        }

        return '';
    }
}
