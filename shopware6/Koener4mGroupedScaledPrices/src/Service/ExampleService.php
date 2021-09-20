<?php declare(strict_types=1);

namespace Koener4m\GroupedScaledPrices\Service;

use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;

class ExampleService extends AbstractExampleService
{
    public function getDecorated(): AbstractExampleService
    {
        throw new DecorationPatternException(self::class);
    }

    public function doSomething(): string
    {
        return 'Did something.';
    }

    public function doSomethingNew(): string
    {
        return 'Did something new.';
    }
}