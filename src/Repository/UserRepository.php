<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\Exception\DBNotFoundException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use VictorCodigo\DoctrinePaginatorAdapter\PaginatorInterface;

/**
 * @implements PasswordUpgraderInterface<User>
 *
 * @template-extends RepositoryBase<User>
 */
class UserRepository extends RepositoryBase implements PasswordUpgraderInterface
{
    /**
     * @param PaginatorInterface<array-key, Recipe> $paginator
     */
    public function __construct(ManagerRegistry $managerRegistry, PaginatorInterface $paginator)
    {
        parent::__construct($managerRegistry, $paginator, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (!$user instanceof User) {
            // @phpstan-ignore argument.type
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param Collection<int, string> $usersId
     *
     * @return PaginatorInterface<int, User>
     *
     * @throws DBNotFoundException
     */
    public function findUsersByIdOrFail(Collection $usersId, int $page, int $pageItems): PaginatorInterface
    {
        $userEntity = User::class;
        $sql = <<<DQL
            SELECT user
            FROM {$userEntity} user
            WHERE user.id IN (:usersId)
        DQL;

        /** @var Query<int, User> */
        $query = $this->entityManager
            ->createQuery($sql)
            ->setParameter('usersId', $usersId->getValues());

        /** @var PaginatorInterface<int, User> */
        $usersPaginator = $this->createPaginator($query, $page, $pageItems);

        return $usersPaginator;
    }
}
