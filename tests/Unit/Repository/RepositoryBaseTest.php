<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Repository\Exception\DBNotFoundException;
use App\Tests\Traits\DoctrineTrait;
use App\Tests\Unit\Repository\Fixtures\EntityClassForTesting;
use App\Tests\Unit\Repository\Fixtures\RepositoryForTesting;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use VictorCodigo\DoctrinePaginatorAdapter\PaginatorInterface;

class RepositoryBaseTest extends KernelTestCase
{
    use DoctrineTrait;

    private RepositoryForTesting $object;
    private ManagerRegistry&MockObject $managerRegistry;
    private EntityManagerInterface&MockObject $entityManager;
    private ObjectManager&MockObject $objectManager;
    /**
     * @var PaginatorInterface<int, object>&MockObject
     */
    private PaginatorInterface&MockObject $paginator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->managerRegistry = $this->getDoctrineManagerRegistryMockForRepository($this->objectManager, $this->entityManager);
        $this->object = new RepositoryForTesting(
            $this->managerRegistry,
            $this->paginator,
            EntityClassForTesting::class
        );
    }

    #[Test]
    public function ItShouldCheckTheUuidAsValid(): void
    {
        $uuid = $this->object->uuidCreate();

        $return = $this->object->uuidIsValid($uuid);

        static::assertTrue($return);
    }

    #[Test]
    public function ItShouldCheckTheUuidAsWrong(): void
    {
        $uuid = 'not valid uuid';

        $return = $this->object->uuidIsValid($uuid);

        static::assertFalse($return);
    }

    #[Test]
    public function ItShouldReturnAUuid(): void
    {
        $return = $this->object->uuidCreate();

        static::assertTrue($this->object->uuidIsValid($return));
    }

    #[Test]
    public function itShouldCreateANewPaginatorWithQuery(): void
    {
        $dql = <<<DQL
            SELECT user
            FROM user
        DQL;
        /** @var Query<array-key, object> */
        $query = $this->entityManager->createQuery($dql);
        $page = 1;
        $pageItems = 10;
        $paginatorNew = $this->createMock(PaginatorInterface::class);

        $this->paginator
            ->expects($this->once())
            ->method('createPaginator')
            ->with($query)
            ->willReturn($paginatorNew);

        $paginatorNew
            ->expects($this->once())
            ->method('setPagination')
            ->with($page, $pageItems)
            ->willReturn($paginatorNew);

        $paginatorNew
            ->expects($this->once())
            ->method('getItemsTotal')
            ->willReturn(10);

        $return = $this->object->createPaginator($query, $page, $pageItems);

        static::assertSame($paginatorNew, $return);
    }

    #[Test]
    public function itShouldCreateANewPaginatorWithQueryBuilder(): void
    {
        $queryBuilder = $this->entityManager
            ->createQueryBuilder()
            ->select('entity')
            ->from('entity', 'entity');
        $page = 1;
        $pageItems = 10;
        $paginatorNew = $this->createMock(PaginatorInterface::class);

        $this->paginator
            ->expects($this->once())
            ->method('createPaginator')
            ->with($queryBuilder)
            ->willReturn($paginatorNew);

        $paginatorNew
            ->expects($this->once())
            ->method('setPagination')
            ->with($page, $pageItems)
            ->willReturn($paginatorNew);

        $paginatorNew
            ->expects($this->once())
            ->method('getItemsTotal')
            ->willReturn(10);

        $return = $this->object->createPaginator($queryBuilder, $page, $pageItems);

        static::assertSame($paginatorNew, $return);
    }

    #[Test]
    public function itShouldFailCreateANewPaginatorNoFoundResults(): void
    {
        $dql = <<<DQL
            SELECT user
            FROM user
        DQL;
        /** @var Query<array-key, object> */
        $query = $this->entityManager->createQuery($dql);
        $page = 1;
        $pageItems = 10;
        $paginatorNew = $this->createMock(PaginatorInterface::class);

        $this->paginator
            ->expects($this->once())
            ->method('createPaginator')
            ->with($query)
            ->willReturn($paginatorNew);

        $paginatorNew
            ->expects($this->once())
            ->method('setPagination')
            ->with($page, $pageItems)
            ->willReturn($paginatorNew);

        $paginatorNew
            ->expects($this->once())
            ->method('getItemsTotal')
            ->willReturn(0);

        $this->expectException(DBNotFoundException::class);
        $this->object->createPaginator($query, $page, $pageItems);
    }

    #[Test]
    public function itShouldSaveACollectionOfEntities(): void
    {
        $entities = new ArrayCollection([
            new EntityClassForTesting(),
            new EntityClassForTesting(),
            new EntityClassForTesting(),
        ]);

        $invokerCounter = $this->exactly($entities->count());
        $this->entityManager
            ->expects($invokerCounter)
            ->method('persist')
            ->with($this->callback(function (EntityClassForTesting $entity) use ($invokerCounter, $entities): bool {
                match ($invokerCounter->numberOfInvocations()) {
                    1 => static::assertSame($entities->get(0), $entity),
                    2 => static::assertSame($entities->get(1), $entity),
                    3 => static::assertSame($entities->get(2), $entity),
                    default => throw new \Exception('Method persist is called more times than expected'),
                };

                return true;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->object->saveEntitiesProxy($entities);
    }

    #[Test]
    public function itShouldFailSavingACollectionOfEntitiesPersistError(): void
    {
        $entities = new ArrayCollection([
            new EntityClassForTesting(),
            new EntityClassForTesting(),
            new EntityClassForTesting(),
        ]);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($entities->get(0))
            ->willThrowException(new \Exception());

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->expectException(\Exception::class);
        $this->object->saveEntitiesProxy($entities);
    }

    #[Test]
    public function itShouldFailSavingACollectionOfEntitiesFlushingError(): void
    {
        $entities = new ArrayCollection([
            new EntityClassForTesting(),
            new EntityClassForTesting(),
            new EntityClassForTesting(),
        ]);

        $invokerCounter = $this->exactly($entities->count());
        $this->entityManager
            ->expects($invokerCounter)
            ->method('persist')
            ->with($this->callback(function (EntityClassForTesting $entity) use ($invokerCounter, $entities): bool {
                match ($invokerCounter->numberOfInvocations()) {
                    1 => static::assertSame($entities->get(0), $entity),
                    2 => static::assertSame($entities->get(1), $entity),
                    3 => static::assertSame($entities->get(2), $entity),
                    default => throw new \Exception('Method persist is called more times than expected'),
                };

                return true;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush')
            ->willThrowException(new \Exception());

        $this->expectException(\Exception::class);
        $this->object->saveEntitiesProxy($entities);
    }
}
