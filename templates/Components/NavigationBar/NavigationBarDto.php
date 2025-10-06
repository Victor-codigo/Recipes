<?php

declare(strict_types=1);

namespace App\Templates\Components\NavigationBar;

use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NavigationBarDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $domain,
        public readonly string $domainName,
        public readonly string $title,
        public readonly ?UserInterface $userData,
        public readonly string $locale,
        public readonly string $routeName,
        public readonly array $routeParameters,
    ) {
    }
}
