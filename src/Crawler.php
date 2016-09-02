<?php

namespace Ascendens\Igcrawler;

use Ascendens\Igcrawler\WebPageProcessor\WebPageProcessorInterface;
use Ascendens\Igcrawler\Report\ReportGeneratorInterface;
use Ascendens\Igcrawler\Logger\LoggerFactoryInterface;
use Ascendens\Igcrawler\WebPageProcessor\TagFinder;
use Ascendens\Igcrawler\WebPageProcessor\Decorator;
use Ascendens\Igcrawler\Http\Utils\UrlHelper;
use InvalidArgumentException;

class Crawler
{
    /**
     * @var WebPageProcessorInterface
     */
    private $webPageProcessor = null;

    /**
     * @var ReportGeneratorInterface
     */
    private $reportGenerator = null;

    /**
     * @var LoggerFactoryInterface
     */
    private $loggerFactory;

    /**
     * @var string
     */
    private $currentUrl;

    /**
     * @var array
     */
    private $remainingUrls = [];

    /**
     * @var array
     */
    private $processedUrls = [];

    /**
     * @param WebPageProcessorInterface $webPageProcessor
     * @param ReportGeneratorInterface $reportGenerator
     * @param LoggerFactoryInterface $loggerFactory Must gave "console" and "file" factories
     */
    public function __construct(
        WebPageProcessorInterface $webPageProcessor,
        ReportGeneratorInterface $reportGenerator,
        LoggerFactoryInterface $loggerFactory
    ) {
        $this->webPageProcessor = $webPageProcessor;
        $this->reportGenerator = $reportGenerator;
        $this->loggerFactory = $loggerFactory;
        $this->init();
    }

    /**
     * @param string $url
     * @param string $reportFilename Where to save report
     */
    public function crawl($url, $reportFilename = null)
    {
        $this->currentUrl = $url;
        $this->remainingUrls[] = $this->currentUrl;
        $this->run();
        $this->saveReport($reportFilename);
    }

    /**
     * Adds required tag processors and decorators
     */
    protected function init()
    {
        if (!$this->loggerFactory->exists('console') || !$this->loggerFactory->exists('file')) {
            throw new InvalidArgumentException('Logger factory must have defined "console" and "file" factories');
        }
        $this->webPageProcessor
            ->addProcessor(new Decorator\Href(new TagFinder\HtmlAFinder()))
            ->addProcessor(new Decorator\Count(new TagFinder\HtmlImgFinder()));
    }

    /**
     * Makes crawling of remaining URLs
     */
    private function run()
    {
        while (count($this->remainingUrls)) {
            $url = array_shift($this->remainingUrls);
            try {
                $urlHelper = new UrlHelper($url, $this->currentUrl);
            } catch (InvalidArgumentException $e) {
                $this->log('console', sprintf("Can't process url: %s", $url));
                continue;
            }
            if ($urlHelper->isExternal() || in_array($url, $this->remainingUrls)) {
                continue;
            }
            $normalizedUrl = $this->getNormalizedUrl($urlHelper);
            if (in_array($normalizedUrl, $this->processedUrls)) {
                continue;
            }
            $this->processedUrls[] = $normalizedUrl;
            try {
                $data = call_user_func($this->webPageProcessor, $normalizedUrl);
                $this->remainingUrls += $data['a'];
            } catch (InvalidArgumentException $e) {
                $this->log('console', sprintf("Can't load url: %s", $normalizedUrl));
                continue;
            }
            $this->reportGenerator->add([
                'url' => $normalizedUrl,
                'imgCount' => $data['img'],
                'duration' => $data['duration']
            ]);
            $this->log('console', sprintf("URL has been processed: %s", $normalizedUrl));
        }
    }

    /**
     * @param string|null $reportFilename
     * @return void
     */
    private function saveReport($reportFilename)
    {
        if (count($this->reportGenerator->getData())) {
            if (is_null($reportFilename)) {
                $reportFilename = sprintf('report_%s.html', date('d.m.Y'));
            }
            try {
                $this->log(
                    'file',
                    $this->reportGenerator->generate([
                        'template' => sprintf(
                            '<html><head><meta charset="utf-8"><title>Report %s</title></head>' .
                            '<body>{body}</body></html>',
                            date('d.m.Y')
                        )
                    ]),
                    [$reportFilename]
                );
                $this->log('console', sprintf('Report has been successfully saved to "%s"', $reportFilename));
            } catch (InvalidArgumentException $e) {
                $this->log('console', sprintf('Error on saving report to "%s"', $reportFilename));
            }
        } else {
            $this->log('console', 'Nothing to save');
        }
    }

    /**
     * Returns URL in internal format
     *
     * @param UrlHelper $urlHelper
     * @return string
     */
    private function getNormalizedUrl(UrlHelper $urlHelper)
    {
        return preg_replace(
            '/(?<!:)\/{2,}/',
            '/',
            $urlHelper->getFormatted('{scheme}://{host}/{path}?{query}')
        );
    }

    /**
     * Logging message
     *
     * @param string $loggerName
     * @param string $message
     * @param array $factoryParameters
     */
    private function log($loggerName, $message, $factoryParameters = [])
    {
        $this->loggerFactory->make($loggerName, $factoryParameters)->log($message);
    }
}
