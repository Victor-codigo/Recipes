<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\Exception\DBNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
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
     * @param Collection<int, string> $recipesId
     *
     * @return Collection<int, Recipe>
     *
     * @throws DBNotFoundException
     */
    public function findRecipesByIdAndGroupIdOrFail(Collection $recipesId, ?string $groupId): Collection
    {
        /** @var array<int, Recipe> */
        $recipes = $this->findBy([
            'id' => $recipesId->toArray(),
            'groupId' => $groupId,
        ]);

        if (empty($recipes)) {
            $recipesIdToString = ltrim(
                $recipesId->reduce(fn (?string $accumulator, string $recipeId): string => "{$accumulator}, {$recipeId}") ?? '',
                ','
            );
            throw DBNotFoundException::fromMessage(sprintf('Recipe [%s], with group [%s] not found', $recipesIdToString, $groupId))->log();
        }

        return new ArrayCollection($recipes);
    }

    /**
     * @return Collection<int, Recipe>
     *
     * @throws DBNotFoundException
     */
    public function findCriteria(Criteria $criteria): Collection
    {
        $query = $this->entityManager
           ->createQueryBuilder()
           ->select('recipe')
           ->from(Recipe::class, 'recipe')
           ->leftJoin(User::class, 'user', Join::WITH, 'recipe.userId = user.id')
           ->addCriteria($criteria)
           ->getQuery();

        /** @var array<int, Recipe> */
        $recipes = $query->getResult();

        return new ArrayCollection($recipes);
    }

    /**
     * @param Collection<int, Recipe>|Recipe $recipes
     */
    public function save(Collection|Recipe $recipes): void
    {
        parent::saveEntities($recipes);
    }

    /**
     * @param Collection<int, Recipe>|Recipe $recipes
     */
    public function remove(Collection|Recipe $recipes): void
    {
        parent::removeEntities($recipes);
    }
}
