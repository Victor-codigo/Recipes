<?php

declare(strict_types=1);

namespace App\Repository;

use App\Repository\Exception\DBNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;
use VictorCodigo\DoctrinePaginatorAdapter\PaginatorInterface;

/**
 * @template TEntityClass of object
 *
 * @template-extends ServiceEntityRepository<TEntityClass>
 */
abstract class RepositoryBase extends ServiceEntityRepository
{
    public readonly ObjectManager $objectManager;
    public readonly EntityManagerInterface $entityManager;
    /**
     * @var PaginatorInterface<array-key, mixed>
     */
    private readonly PaginatorInterface $paginator;

    /**
     * @param PaginatorInterface<array-key, object> $doctrinePaginatorAdapter
     * @param class-string                          $entityClass
     */
    public function __construct(ManagerRegistry $managerRegistry, PaginatorInterface $doctrinePaginatorAdapter, string $entityClass)
    {
        parent::__construct($managerRegistry, $entityClass);

        $this->objectManager = $managerRegistry->getManager();
        $this->entityManager = $this->getEntityManager();
        $this->paginator = $doctrinePaginatorAdapter;
    }

    public function uuidCreate(): string
    {
        return Uuid::v4()->toRfc4122();
    }

    public function uuidIsValid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }

    /**
     * @param Query<array-key, object>|QueryBuilder $query
     *
     * @return PaginatorInterface<array-key, object>
     *
     * @throws DBNotFoundException
     */
    public function createPaginator(Query|QueryBuilder $query, int $page, int $pageItems): PaginatorInterface
    {
        /** @var PaginatorInterface<array-key, object> */
        $paginator = $this->paginator
            ->createPaginator($query)
            ->setPagination($page, $pageItems);

        if (0 === $paginator->getItemsTotal()) {
            throw DBNotFoundException::fromMessage('Entities not found');
        }

        return $paginator;
    }

    /**
     * @param Collection<array-key, TEntityClass> $entities
     */
    protected function saveEntities(Collection $entities): void
    {
        $entities->map(fn (mixed $entity) => $this->entityManager->persist($entity));

        $this->entityManager->flush();
    }
}
