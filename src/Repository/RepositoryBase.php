<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @template T of object
 *
 * @extends ServiceEntityRepository<T>
 */
abstract class RepositoryBase extends ServiceEntityRepository
{
    public function uuidCreate(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}
