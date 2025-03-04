<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\Throwable\LoadingThrowable;

trait TestingAliceBundleTrait
{
    /**
     * @param array<int, string> $files
     *
     * @return Collection<array-key, mixed>
     *
     * @throws LoadingThrowable
     */
    protected function getAliceBundleFixtures(array $files): Collection
    {
        $loader = new NativeLoader();

        $fixtures = $loader
            ->loadFiles($files)
            ->getObjects();

        return new ArrayCollection($fixtures);
    }

    /**
     * @template TType of object
     *
     * @param class-string<TType> $classTypeFilter
     * @param array<int, string>  $files
     *
     * @return Collection<array-key, TType>
     */
    protected function getAliceBundleFixturesFilterByType(string $classTypeFilter, array $files): Collection
    {
        /** @var Collection<array-key, object> */
        $fixtures = $this->getAliceBundleFixtures($files);

        /** @var Collection<array-key, TType> */
        $fixturesFiltered = $fixtures->filter(fn (object $object) => $object instanceof $classTypeFilter);

        return $fixturesFiltered;
    }

    /**
     * @param array<int, string>      $files
     * @param array<int, string>|null $typesFiler
     *
     * @return array<string, object>
     */
    protected function getAliceBundleFixturesGroupedByType(array $files, ?array $typesFiler = null): array
    {
        /** @var Collection<array-key, object> */
        $fixtures = $this->getAliceBundleFixtures($files);
        /** @var array<int, string> */
        $fixturesTypes = $fixtures
            ->map(fn (object $object): string => $object::class)
            ->reduce(
                fn (array $typesFound, string $type): array => in_array($type, $typesFound) ? $typesFound : [...$typesFound, $type],
                []
            );

        $fixturesGrouped = [];
        foreach ($fixturesTypes as $type) {
            if (null !== $typesFiler && !in_array($type, $typesFiler)) {
                continue;
            }

            $fixturesGrouped[$type] = $fixtures->filter(fn (object $object): bool => $object::class === $type);
        }

        return $fixturesGrouped;
    }
}
