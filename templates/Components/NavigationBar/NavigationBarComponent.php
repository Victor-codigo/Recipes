<?php

declare(strict_types=1);

namespace App\Templates\Components\NavigationBar;

use App\Entity\User;
use App\Service\UrlEncoder\UrlEncoder;
use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'NavigationBarComponent',
    template: 'Components/NavigationBar/NavigationBarComponent.html.twig'
)]
class NavigationBarComponent extends TwigComponent
{
    private readonly RouterInterface $router;
    private readonly UrlEncoder $urlEncoder;
    public NavigationBarLangDto $lang;
    public NavigationBarDto|TwigComponentDtoInterface $data;

    public readonly string $cssType;
    public readonly string $cssTextColor;

    public readonly string $languageToggleUrl;
    public readonly string $languageToggleTitle;

    public readonly string $logoTitleAttribute;
    public readonly string $backButtonTitle;

    public readonly string $themeButtonTitle;
    public readonly string $userButtonTitle;

    public readonly ?UserButtonDto $userButton;
    public readonly ?MenuButtonDto $profileButton;
    public readonly ?MenuButtonDto $logoutButton;

    protected static function getComponentName(): string
    {
        return 'NavigationBarComponent';
    }

    public function __construct(
        TranslatorInterface $translator,
        UrlEncoder $urlEncoder,
        RouterInterface $router,
    ) {
        parent::__construct($translator);

        $this->urlEncoder = $urlEncoder;
        $this->router = $router;
    }

    public function mount(NavigationBarDto $data): void
    {
        $this->data = $data;

        $this->userButton = $this->createUserButton($data->userData);
        $this->profileButton = $this->createProfileButton($data->userData);
        $this->logoutButton = $this->createLogoutButton($data->userData);
        $this->languageToggleUrl = $this->createLanguageToggleUrl($this->data->routeName, $this->data->routeParameters, $this->data->locale);
        $this->languageToggleTitle = $this->translate('navigation.language.title');
        $this->themeButtonTitle = $this->translate('navigation.theme.title');
        $this->userButtonTitle = $this->translate('navigation.user_menu.title');
        $this->logoTitleAttribute = $this->translate('navigation.logo.title', ['domain_name' => $this->data->domainName]);
        $this->backButtonTitle = $this->translate('navigation.back_button.title');
    }

    private function createLanguageToggleUrl(string $routeName, array $routeParameters, string $locale): string
    {
        unset($routeParameters['_locale']);

        return $this->router->generate($routeName, [
            '_locale' => 'en' === $locale ? 'es' : 'en',
            ...$routeParameters,
        ]);
    }

    private function createUserButton(?User $userData): ?UserButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new UserButtonDto(
            $userData->getName(),
            // $userData->image,
            'no image',
            $this->translate('navigation.user_menu.title'),
            $this->translate('navigation.user_menu.alt'),
        );
    }

    private function createProfileButton(?User $userData): ?MenuButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new MenuButtonDto(
            $this->translate('navigation.profile.label'),
            $this->translate('navigation.profile.title'),
            $this->router->generate('user_profile', [
                'user_name' => $this->urlEncoder->encodeUrl($userData->getName()),
            ]),
            // $userData->image
            'no image'
        );
    }

    private function createLogoutButton(?User $userData): ?MenuButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new MenuButtonDto(
            $this->translate('navigation.logout.label'),
            $this->translate('navigation.logout.title'),
            $this->router->generate('user_logout', []),
            null
        );
    }
}
