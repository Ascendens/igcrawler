<?php

namespace Ascendens\Igcrawler\Logger;

interface LoggerFactoryInterface
{
    /**
     * Adds new factory under given name
     *
     * @param string $name
     * @param callable $factory
     * @return LoggerFactoryInterface
     */
    public function add($name, callable $factory);

    /**
     * Checks existence of factory under given name
     *
     * @param string $name
     * @return bool
     */
    public function exists($name);

    /**
     * Removes factory under given name
     *
     * @param string $name
     * @return LoggerFactoryInterface
     */
    public function remove($name);

    /**
     * Initializes logger via factory and returns it
     *
     * @param string $name
     * @param array $factoryParameters
     * @return LoggerInterface
     */
    public function make($name, array $factoryParameters = []);
}
