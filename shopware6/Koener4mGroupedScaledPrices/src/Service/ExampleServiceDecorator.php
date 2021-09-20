<?php declare(strict_types=1);

namespace Koener4m\GroupedScaledPrices\Service;

class ExampleServiceDecorator extends AbstractExampleService
{
    /**
     * @var AbstractExampleService
     */
    private $decoratedService;

    public function __construct(AbstractExampleService $exampleService)
    {
        $this->decoratedService = $exampleService;
    }

    public function getDecorated(): AbstractExampleService
    {
        return $this->decoratedService;
    }

    public function doSomething(): string
    {
        $originalResult = $this->decoratedService->doSomething();

        return $originalResult . ' Did something additionally.';
    }
}