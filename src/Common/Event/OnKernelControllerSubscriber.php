<?php

declare(strict_types=1);

namespace App\Common\Event;

use App\Templates\Components\NavigationBar\NavigationBarDto;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class OnKernelControllerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Environment $twig,
        private Security $security,
        private readonly string $appConfigUrlDomainName,
    ) {
    }

    /**
     * @return array{ "kernel.controller": array<int, string> }
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => ['__invoke']];
    }

    public function __invoke(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        $this->loadTwigGlobals($request);
    }

    private function loadTwigGlobals(Request $request): void
    {
        $navigationBarDto = $this->load($request);

        $this->twig->addGlobal('NavigationBarComponentDto', $navigationBarDto);
    }

    private function load(Request $request): NavigationBarDto
    {
        $route = is_string($request->attributes->get('_route'))
            ? $request->attributes->get('_route')
            : '';

        $routeParams = is_array($request->attributes->get('_route_params'))
            ? $request->attributes->get('_route_params')
            : [];

        return new NavigationBarDto(
            $request->getHost(),
            $this->appConfigUrlDomainName,
            'Recipes',
            $this->security->getUser(),
            $request->getLocale(),
            $route,
            $routeParams
        );
    }
}
