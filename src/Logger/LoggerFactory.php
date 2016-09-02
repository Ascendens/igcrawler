<?php

namespace Ascendens\Igcrawler\Logger;

class LoggerFactory implements LoggerFactoryInterface
{
    /**
     * @var array
     */
    private $factories = [];

    /**
     * @inheritdoc
     */
    public function add($name, callable $factory)
    {
        $this->factories[$name] = $factory;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function exists($name)
    {
        return isset($this->factories[$name]);
    }

    /**
     * @inheritdoc
     */
    public function remove($name)
    {
        unset($this->factories[$name]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function make($name, array $factoryParameters = [])
    {
        return call_user_func_array($this->factories[$name], $factoryParameters);
    }
}
