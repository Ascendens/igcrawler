<?php

namespace Ascendens\Igcrawler\WebPageProcessor;

interface HtmlTagProcessorInterface
{
    /**
     * @return string
     */
    public function getTag();

    /**
     * @param $code
     * @return mixed
     */
    public function process($code);
}
