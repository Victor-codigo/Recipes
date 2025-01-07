<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/recipe', name: 'recipe_home')]
class RecipeHome extends AbstractController
{
    public function __invoke(): Response
    {
        return new Response('This is recipe home');
    }
}
