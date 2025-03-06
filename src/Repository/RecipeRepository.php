<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Repository\Exception\DBNotFoundException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use VictorCodigo\DoctrinePaginatorAdapter\PaginatorInterface;

/**
 * @template-extends RepositoryBase<Recipe>
 */
class RecipeRepository extends RepositoryBase
{
    /**
     * @param PaginatorInterface<array-key, Recipe> $paginator
     */
    public function __construct(ManagerRegistry $managerRegistry, PaginatorInterface $paginator)
    {
        parent::__construct($managerRegistry, $paginator, Recipe::class);
    }

    /**
     * @return PaginatorInterface<array-key, Recipe>
     *
     * @throws DBNotFoundException
     */
    public function findRecipesByUserIdOrFail(string $userId, ?string $groupId, int $page, int $pageItems): PaginatorInterface
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('recipe')
            ->from(Recipe::class, 'recipe')
            ->where('recipe.userId = :userId')
            ->setParameter('userId', $userId);

        if (null !== $groupId) {
            $query
                ->andWhere('recipe.groupId = :groupId')
                ->setParameter('groupId', $groupId);
        }

        /** @var PaginatorInterface<int, Recipe> */
        $recipesPaginator = $this->createPaginator($query, $page, $pageItems);

        return $recipesPaginator;
    }

    /**
     * @throws DBNotFoundException
     */
    public function findRecipeByIdOrFail(string $recipeId): Recipe
    {
        /** @var Recipe|null */
        $result = $this->findOneBy(['id' => $recipeId]);

        if (null === $result) {
            throw DBNotFoundException::fromMessage('Recipe not found');
        }

        return $result;
    }

    /**
     * @param Collection<int, Recipe>|Recipe $recipes
     */
    public function save(Collection|Recipe $recipes): void
    {
        parent::saveEntities($recipes);
    }
}
