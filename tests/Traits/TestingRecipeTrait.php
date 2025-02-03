<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait TestingRecipeTrait
{
    use TestingHelperTrait;
    use TestingFixturesTrait;

    /**
     * @param Collection<array-key, Recipe> $expected
     * @param Collection<array-key, Recipe> $actual
     */
    protected function assertRecipesAreEqualCanonicalize(Collection $expected, Collection $actual): void
    {
        static::assertCount($expected->count(), $actual,
            'Expected and Actual has not the same number of recipes'
        );

        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getId'), $this->getObjectMethodValue($actual, 'getId'),
            'Property getId is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getUserId'), $this->getObjectMethodValue($actual, 'getUserId'),
            'Property getUserId is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getGroupId'), $this->getObjectMethodValue($actual, 'getGroupId'),
            'Property getGroupId is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getName'), $this->getObjectMethodValue($actual, 'getName'),
            'Property getName is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getCategory'), $this->getObjectMethodValue($actual, 'getCategory'),
            'Property getCategory is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getDescription'), $this->getObjectMethodValue($actual, 'getDescription'),
            'Property getDescription is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getPreparationTime'), $this->getObjectMethodValue($actual, 'getPreparationTime'),
            'Property getPreparationTime is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getIngredients'), $this->getObjectMethodValue($actual, 'getIngredients'),
            'Property getIngredients is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getSteps'), $this->getObjectMethodValue($actual, 'getSteps'),
            'Property getSteps is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getImage'), $this->getObjectMethodValue($actual, 'getImage'),
            'Property getImage is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getRating'), $this->getObjectMethodValue($actual, 'getRating'),
            'Property getRating is not equal in all recipes'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($expected, 'getPublic'), $this->getObjectMethodValue($actual, 'getPublic'),
            'Property getPublic is not equal in all recipes'
        );

        $this->assertArrayEqualsWithDelta(
            $this->getObjectMethodValue($expected, 'getCreatedOn')->toArray(),
            $this->getObjectMethodValue($actual, 'getCreatedOn')->toArray(),
            1,
            'Property getCreatedOn is not equal in all recipes'
        );
    }

    /**
     * @return array<string, Collection<int, Recipe|User>>
     */
    protected function getRecipesAndUsersFixtures(): array
    {
        /** @var array<string, Collection<int, Recipe|User>> */
        $fixtures = $this->getAliceBundleFixturesGroupedByType([
            self::RECIPES_FIXTURES_PATH,
            self::DATETIME_FIXTURES_PATH,
            self::USERS_FIXTURES_PATH,
        ], [
            Recipe::class,
            User::class,
        ]);

        return $fixtures;
    }

    /**
     * @return Collection<int, Recipe>
     */
    protected function recipesCreateForAExistenceUser(User $user): Collection
    {
        return new ArrayCollection([
            new Recipe(
                'fb51f98c-622a-4539-864a-20af8de1db87',
                $user,
                null,
                'Recipe new 1',
                'DESSERT',
                'Description of recipe new 1',
                new \DateTimeImmutable(),
                [
                    'Ingredient new 1',
                    'Ingredient new 2',
                    'Ingredient new 3',
                ],
                [
                    'Step new 1',
                    'Step new 2',
                    'Step new 3',
                ],
                null,
                4,
                true,
            ),
            new Recipe(
                'd2e25b12-a7ef-4cc0-b8c2-5f356fe527c7',
                $user,
                null,
                'Recipe new 2',
                'LUNCH',
                'Description of recipe new 2',
                new \DateTimeImmutable(),
                [
                    'Ingredient new 1',
                    'Ingredient new 2',
                ],
                [
                    'Step new 1',
                    'Step new 2',
                    'Step new 3',
                    'Step new 4',
                ],
                null,
                5,
                false,
            ),
            new Recipe(
                'd4f73de1-9a6b-48c9-ada1-15ef4fa89874',
                $user,
                '19c8530f-61cc-45db-96d7-7141ef3a8896',
                'Recipe new 3',
                'SNACK',
                'Description of recipe new 3',
                new \DateTimeImmutable(),
                [
                    'Ingredient new 1',
                    'Ingredient new 2',
                    'Ingredient new 3',
                    'Ingredient new 4',
                ],
                [
                    'Step new 1',
                    'Step new 2',
                    'Step new 3',
                ],
                null,
                6,
                true,
            ),
            new Recipe(
                '43cb97a1-4712-468f-9647-4aa65804d9e8',
                $user,
                '19c8530f-61cc-45db-96d7-7141ef3a8896',
                'Recipe new 4',
                'SOUP',
                'Description of recipe new 4',
                new \DateTimeImmutable(),
                [
                    'Ingredient new 1',
                ],
                [
                    'Step new 1',
                ],
                null,
                7,
                false,
            ),
        ]);
    }
}
