<?php

namespace Ascendens\Igcrawler\WebPageProcessor\TagFinder;

class HtmlAFinder extends AbstractHtmlTagFinder
{
    /**
     * @inheritdoc
     */
    public function getTag()
    {
        return 'a';
    }

    /**
     * @inheritdoc
     */
    protected function haveClosingTag()
    {
        return true;
    }
}
