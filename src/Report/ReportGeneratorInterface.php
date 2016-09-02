<?php

namespace Ascendens\Igcrawler\Report;

interface ReportGeneratorInterface extends DataCollectorInterface
{
    /**
     * Makes report generation
     *
     * @param array $parameters Extra parameters
     * @return string
     */
    public function generate(array $parameters = []);
}
