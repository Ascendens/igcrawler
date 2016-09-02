<?php

namespace Ascendens\Igcrawler\Report;

interface DataCollectorInterface
{
    /**
     * Adds new set of data
     *
     * @param mixed $data
     * @return DataCollectorInterface
     */
    public function add($data);

    /**
     * Returns collected data
     *
     * @return mixed
     */
    public function getData();
}
