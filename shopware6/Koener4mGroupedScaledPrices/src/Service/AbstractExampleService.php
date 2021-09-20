<?php declare(strict_types=1);

namespace Koener4m\GroupedScaledPrices\Service;

abstract class AbstractExampleService
{
    abstract public function getDecorated(): AbstractExampleService; 

    abstract public function doSomething(): string;

    public function doSomethingNew(): string
    {
        return $this->getDecorated()->doSomethingNew();
    }
}