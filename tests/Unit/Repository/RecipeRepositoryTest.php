<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\Exception\DBNotFoundException;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Tests\Traits\TestingAliceBundleTrait;
use App\Tests\Traits\TestingDoctrineTrait;
use App\Tests\Traits\TestingFixturesTrait;
use App\Tests\Traits\TestingHelperTrait;
use App\Tests\Traits\TestingRecipeTrait;
use App\Tests\Traits\TestingUserTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeRepositoryTest extends KernelTestCase
{
    use ReloadDatabaseTrait;
    use TestingDoctrineTrait;
    use TestingAliceBundleTrait;
    use TestingRecipeTrait;
    use TestingHelperTrait;
    use TestingFixturesTrait;
    use TestingUserTrait;

    private RecipeRepository $object;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = $this
            ->getDoctrineEntityManager()
            ->getRepository(Recipe::class);

        $this->userRepository = $this
            ->getDoctrineEntityManager()
            ->getRepository(User::class);
    }

    #[Test]
    public function itShouldGetRecipesById(): void
    {
        /**
         * @var Collection<string, User>   $usersFixtures
         * @var Collection<string, Recipe> $recipesFixtures
         */
        [User::class => $usersFixtures, Recipe::class => $recipesFixtures] = $this->getRecipesAndUsersFixtures();
        $user = false !== $usersFixtures->first()
            ? $usersFixtures->first()
            : throw new \Exception('UserId is false');
        $page = 1;
        $pageItems = 10;
        $recipesExpected = $recipesFixtures->filter(fn (Recipe $recipe): bool => $recipe->getUserId() === $user->getId());

        $return = $this->object->findRecipesByUserIdOrFail($user->getId(), null, $page, $pageItems);
        $recipesReturned = $this->iteratorToCollection($return);

        static::assertCount($recipesExpected->count(), $recipesReturned);
        $this->assertRecipesAreEqualCanonicalize($recipesExpected, $recipesReturned);
    }

    #[Test]
    public function itShouldGetRecipesByIdAndGroupId(): void
    {
        /**
         * @var Collection<string, User>   $usersFixtures
         * @var Collection<string, Recipe> $recipesFixtures
         */
        [User::class => $usersFixtures, Recipe::class => $recipesFixtures] = $this->getRecipesAndUsersFixtures();

        /** @var User */
        $user = false !== $usersFixtures->first()
            ? $usersFixtures->first()
            : throw new \Exception('UserId is false');
        $page = 1;
        $pageItems = 10;
        $recipesExpected = $recipesFixtures->filter(fn (Recipe $recipe): bool => null !== $recipe->getGroupId()
                                                                                 && $recipe->getUserId() === $user->getId()
        );
        $recipeWithGroup = $recipesExpected
            ->filter(fn (Recipe $recipe): bool => null !== $recipe->getGroupId())
            ->first() ?: throw new \Exception('Recipe with group is false');

        $return = $this->object->findRecipesByUserIdOrFail($user->getId(), $recipeWithGroup->getGroupId(), $page, $pageItems);
        $recipesReturned = $this->iteratorToCollection($return);

        static::assertCount($recipesExpected->count(), $recipesReturned);
        $this->assertRecipesAreEqualCanonicalize($recipesExpected, $recipesReturned);
    }

    #[Test]
    public function itShouldFailGetRecipesNotFound(): void
    {
        $userId = 'none existence user';
        $page = 1;
        $pageItems = 10;

        $this->expectException(DBNotFoundException::class);
        $this->object->findRecipesByUserIdOrFail($userId, null, $page, $pageItems);
    }

    #[Test]
    public function itShouldFindARecipeById(): void
    {
        /** @var Collection<int, Recipe> */
        $recipesFixtures = $this->getAliceBundleFixturesFilterByType(Recipe::class, [
            self::USERS_FIXTURES_PATH,
            self::RECIPES_FIXTURES_PATH,
            self::DATETIME_FIXTURES_PATH,
        ]);
        $recipeExpected = $recipesFixtures->filter(fn (Recipe $recipe): bool => self::RECIPE_1_FIXTURES_ID === $recipe->getId());
        $return = $this->object->findRecipeByIdOrFail(self::RECIPE_1_FIXTURES_ID);

        $this->assertRecipesAreEqualCanonicalize($recipeExpected, new ArrayCollection([$return]));
    }

    #[Test]
    public function itShouldFailFindingARecipeById(): void
    {
        $this->expectException(DBNotFoundException::class);
        $this->object->findRecipeByIdOrFail('wrong recipe id');
    }

    #[Test]
    public function itShouldSaveRecipes(): void
    {
        $user = $this->userRepository->findOneBy(['id' => self::USER_1_FIXTURES_ID]) ?: throw new \Exception('User not found');
        $recipesNew = $this->recipesCreateForAExistenceUser($user);
        $recipesNewIds = $this->getObjectMethodValue($recipesNew, 'getId');
        $this->object->save($recipesNew);

        $recipesFromDb = $this->object->findBy(['id' => $recipesNewIds->toArray()]);

        $this->assertRecipesAreEqualCanonicalize($recipesNew, new ArrayCollection($recipesFromDb));
    }

    #[Test]
    public function itShouldFailSavingRecipes(): void
    {
        $user = $this->getUsersFixtures()->first() ?: throw new \Exception('User not found');
        $recipesNew = $this->recipesCreateForAExistenceUser($user);

        $this->expectException(\Throwable::class);
        $this->object->save($recipesNew);
    }
}
