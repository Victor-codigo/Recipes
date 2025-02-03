<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait TestingHelperTrait
{
    /**
     * @template TValue
     *
     * @param array<array-key, TValue>|Collection<array-key, TValue> $array
     *
     * @return Collection<array-key, TValue>
     */
    protected function arrayToCollection(array|Collection $array): Collection
    {
        if (!$array instanceof Collection) {
            $array = new ArrayCollection($array);
        }

        return $array;
    }

    /**
     * @template TValue
     *
     * @param iterable<array-key, TValue> $iterator
     *
     * @return Collection<array-key, TValue>
     */
    protected function iteratorToCollection(iterable $iterator): Collection
    {
        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * @template TValue of object
     *
     * @param Collection<array-key, TValue> $objects
     *
     * @return Collection<int, mixed>
     *
     * @throws \LogicException
     */
    protected function getObjectPropertyValue(Collection $objects, string $propertyName): Collection
    {
        return $objects->map(fn (object $object): mixed => $object->{$propertyName});
    }

    /**
     * @template TValue of object
     *
     * @param Collection<array-key, TValue> $objects
     *
     * @return Collection<int, mixed>
     *
     * @throws \LogicException
     */
    protected function getObjectMethodValue(Collection $objects, string $methodName, mixed ...$params): Collection
    {
        return $objects->map(fn (object $object): mixed => $object->$methodName(...$params));
    }

    /**
     * @param array<array-key, mixed> $expected
     * @param array<array-key, mixed> $actual
     */
    protected function assertArrayEqualsWithDelta(array $expected, array $actual, float $delta, string $message = ''): void
    {
        static::assertCount(count($expected), $actual,
            'Arrays have different number of items'
        );

        sort($expected);
        sort($actual);

        $expectedSorted = reset($expected);
        $actualSorted = reset($actual);
        while ($expectedSorted && $actualSorted) {
            static::assertEqualsWithDelta($expectedSorted, $actualSorted, $delta, $message);

            $expectedSorted = next($expected);
            $expectedSorted = next($actual);
        }
    }
}
