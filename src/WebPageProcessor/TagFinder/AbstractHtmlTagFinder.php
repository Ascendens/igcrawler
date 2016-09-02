<?php

namespace Ascendens\Igcrawler\WebPageProcessor\TagFinder;

use Ascendens\Igcrawler\WebPageProcessor\HtmlTagProcessorInterface;

abstract class AbstractHtmlTagFinder implements HtmlTagProcessorInterface
{
    /**
     * Finds tags in HTML code
     *
     * @param string $code Source HTML code
     * @return array Array of found tags
     */
    public function process($code)
    {
        if(!preg_match_all($this->getTagPattern(), (string) $code, $matches)) {
            return [];
        }

        return $matches[0];
    }

    /**
     * Does tag should be closed
     *
     * @return bool
     */
    protected abstract function haveClosingTag();

    /**
     * @inheritdoc
     */
    private function getTagPattern()
    {
        $pattern = sprintf('/<%s\b[^>]*', $this->getTag());
        if ($this->haveClosingTag()) {
            $pattern .= sprintf('>(.*?)<\/%s>', $this->getTag());
        } else {
            $pattern .= '\/?>';
        }
        $pattern .= '/i';

        return $pattern;
    }
}
