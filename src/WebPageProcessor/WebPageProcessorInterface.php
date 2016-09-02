<?php

namespace Ascendens\Igcrawler\WebPageProcessor;

interface WebPageProcessorInterface
{
    /**
     * Adds new processor to the set
     *
     * @param HtmlTagProcessorInterface $processor
     * @return WebPageProcessorInterface
     */
    public function addProcessor(HtmlTagProcessorInterface $processor);

    /**
     * Removes processor from the set
     *
     * @param HtmlTagProcessorInterface $processor
     * @return $this
     */
    public function removeProcessor(HtmlTagProcessorInterface $processor);

    /**
     * Page processing
     *
     * @param string $url
     * @return mixed
     */
    public function __invoke($url);
}
