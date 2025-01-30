<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;

trait DoctrineTrait
{
    private function getDoctrineEntityManager(?string $name = null): EntityManagerInterface
    {
        /** @var EntityManagerInterface */
        $entityManager = $this
            ->getDoctrineManagerRegistry()
            ->getManager($name);

        return $entityManager;
    }

    private function getDoctrineManagerRegistry(): ManagerRegistry
    {
        /** @var ManagerRegistry */
        $doctrineManagerRegistry = static::getContainer()->get('doctrine');

        return $doctrineManagerRegistry;
    }

    /**
     * Creates a ManagerRegistry mock, to be able to inject a mock of ObjectRepository
     * and EntityManager classes, in the repository.
     */
    private function getDoctrineManagerRegistryMockForRepository(ObjectManager $objectManager, EntityManagerInterface&MockObject $entityManager): ManagerRegistry&MockObject
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $classMetaData = $this
            ->getMockBuilder(ClassMetadata::class)
            ->setConstructorArgs(['ClassMetadataName'])
            ->getMock();

        $managerRegistry
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($objectManager);

        $managerRegistry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->willReturn($entityManager);

        $entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetaData);

        return $managerRegistry;
    }
}
