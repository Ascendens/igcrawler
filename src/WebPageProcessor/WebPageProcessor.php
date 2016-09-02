<?php

namespace Ascendens\Igcrawler\WebPageProcessor;

use Ascendens\Igcrawler\Http\HttpClientInterface;
use SplObjectStorage;
use Ascendens\Igcrawler\WebPageProcessor\TagFinder\AbstractHtmlTagFinder;

class WebPageProcessor implements WebPageProcessorInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var SplObjectStorage
     */
    private $tagProcessors;

    /**
     * @var array
     */
    private $result = [];

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->tagProcessors = new SplObjectStorage();
    }

    /**
     * Loads code of web page and processes it with HtmlTagProcessors. Result of single processors will be under HTML
     * tag key
     *
     * @param string $url
     * @return array Results of processing and processing duration
     */
    public function __invoke($url)
    {
        $this->result = [];
        $start = microtime(true);
        $code = $this->httpClient->request($url);
        $this->tagProcessors->rewind();
        while ($this->tagProcessors->valid()) {
            /**
             * @var AbstractHtmlTagFinder $processor
             */
            $processor = $this->tagProcessors->current();
            $this->result[$processor->getTag()] = $processor->process($code);
            $this->tagProcessors->next();
        }
        $this->result['duration'] = microtime(true) - $start;

        return $this->result;
    }

    /**
     * Adds new processor to the set
     *
     * @param HtmlTagProcessorInterface $processor
     * @return $this
     */
    public function addProcessor(HtmlTagProcessorInterface $processor)
    {
        $this->tagProcessors->attach($processor);

        return $this;
    }

    /**
     * Removes processor from the set
     *
     * @param HtmlTagProcessorInterface $processor
     * @return $this
     */
    public function removeProcessor(HtmlTagProcessorInterface $processor)
    {
        $this->tagProcessors->detach($processor);

        return $this;
    }
}
