<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Entity\User;
use App\Repository\Exception\DBNotFoundException;
use App\Repository\UserRepository;
use App\Tests\Traits\TestingAliceBundleTrait;
use App\Tests\Traits\TestingDoctrineTrait;
use App\Tests\Traits\TestingFixturesTrait;
use App\Tests\Traits\TestingHelperTrait;
use App\Tests\Traits\TestingUserTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use TestingDoctrineTrait;
    use TestingAliceBundleTrait;
    use TestingHelperTrait;
    use TestingUserTrait;
    use TestingFixturesTrait;

    private UserRepository $object;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = $this
            ->getDoctrineEntityManager()
            ->getRepository(User::class);
    }

    #[Test]
    public function itShouldFindUsersById(): void
    {
        /** @var Collection<int, User> */
        $usersFixtures = $this->getAliceBundleFixtures([self::USERS_FIXTURES_PATH]);
        /** @var Collection<int, string> */
        $usersId = $this->getObjectMethodValue($usersFixtures, 'getId');
        $page = 1;
        $pageItems = 10;

        $return = $this->object->findUsersByIdOrFail($usersId, $page, $pageItems);
        $usersReturned = $this->iteratorToCollection($return);

        static::assertCount($usersFixtures->count(), $usersReturned->toArray());
        $this->assertUsersAreEqualCanonicalizing($usersFixtures, $usersReturned);
    }

    #[Test]
    public function itShouldFailFindUsersByIdNotFound(): void
    {
        /** @var Collection<int, string> */
        $usersId = new ArrayCollection([
            'user id 1',
            'user id 2',
        ]);
        $page = 1;
        $pageItems = 10;

        $this->expectException(DBNotFoundException::class);
        $this->object->findUsersByIdOrFail($usersId, $page, $pageItems);
    }
}
