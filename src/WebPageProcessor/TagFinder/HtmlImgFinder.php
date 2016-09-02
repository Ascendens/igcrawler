<?php

namespace Ascendens\Igcrawler\WebPageProcessor\TagFinder;

class HtmlImgFinder extends AbstractHtmlTagFinder
{
    /**
     * @inheritdoc
     */
    public function getTag()
    {
        return 'img';
    }

    /**
     * @inheritdoc
     */
    protected function haveClosingTag()
    {
        return false;
    }
}
