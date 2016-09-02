<?php

namespace Ascendens\Igcrawler\WebPageProcessor\Decorator;

use Ascendens\Igcrawler\WebPageProcessor\HtmlTagProcessorInterface;

abstract class AbstractDecorator implements HtmlTagProcessorInterface
{
    /**
     * @var HtmlTagProcessorInterface
     */
    protected $delegate = null;

    /**
     * @param HtmlTagProcessorInterface $delegate
     */
    public function __construct(HtmlTagProcessorInterface $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritdoc
     */
    public function process($code)
    {
        return $this->doProcess($this->delegate->process($code));
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->delegate->getTag();
    }

    /**
     * Makes special data processing
     *
     * @param mixed $data
     * @return mixed
     */
    public abstract function doProcess($data);
}
