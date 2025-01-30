<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository\Fixtures;

use App\Repository\RepositoryBase;
use Doctrine\Common\Collections\Collection;

/**
 * @phpstan-extends RepositoryBase<EntityClassForTesting>
 */
class RepositoryForTesting extends RepositoryBase
{
    /**
     * @param Collection<array-key, EntityClassForTesting> $entities
     */
    public function saveEntitiesProxy(Collection $entities): void
    {
        parent::saveEntities($entities);
    }
}
