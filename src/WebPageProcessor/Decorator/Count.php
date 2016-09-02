<?php

namespace Ascendens\Igcrawler\WebPageProcessor\Decorator;

class Count extends AbstractDecorator
{
    /**
     * @inheritdoc
     */
    public function doProcess($data)
    {
        return count($data);
    }
}
