<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;

trait TestingUserTrait
{
    use TestingHelperTrait;
    use TestingFixturesTrait;
    use TestingAliceBundleTrait;

    /**
     * @param Collection<int, User> $usersExpected
     * @param Collection<int, User> $usersActual
     */
    private function assertUsersAreEqualCanonicalizing(Collection $usersExpected, Collection $usersActual): void
    {
        static::assertCount($usersExpected->count(), $usersActual);
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($usersExpected, 'getId'), $this->getObjectMethodValue($usersActual, 'getId'),
            'Property getId is not equal in all users'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($usersExpected, 'getEmail'), $this->getObjectMethodValue($usersActual, 'getEmail'),
            'Property getEmail is not equal in all users'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($usersExpected, 'getRoles'), $this->getObjectMethodValue($usersActual, 'getRoles'),
            'Property getRoles is not equal in all users'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($usersExpected, 'getPassword'), $this->getObjectMethodValue($usersActual, 'getPassword'),
            'Property getPassword is not equal in all users'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($usersExpected, 'getName'), $this->getObjectMethodValue($usersActual, 'getName'),
            'Property getName is not equal in all users'
        );
        static::assertEqualsCanonicalizing($this->getObjectMethodValue($usersExpected, 'isVerified'), $this->getObjectMethodValue($usersActual, 'isVerified'),
            'Property isVerified is not equal in all users'
        );
    }

    /**
     * @return Collection<int, User>
     */
    protected function getUsersFixtures(): Collection
    {
        return $this->getAliceBundleFixturesFilterByType(User::class, [
            self::USERS_FIXTURES_PATH,
        ]);
    }
}
