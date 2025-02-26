<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait TestingFirewallTrait
{
    protected function getNewClientAuthenticated(string $userId, string $firewall = 'main'): KernelBrowser
    {
        $client = self::createClient();
        /** @var UserRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);
        /** @var User */
        $user = $userRepository->findOneBy(['id' => $userId]);
        $client->loginUser($user, $firewall);

        return $client;
    }
}
