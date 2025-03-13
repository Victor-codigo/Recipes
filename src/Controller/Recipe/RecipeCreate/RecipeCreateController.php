<?php

declare(strict_types=1);

namespace App\Controller\Recipe\RecipeCreate;

use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormType;
use App\Service\Exception\RecipeCreateException;
use App\Service\Recipe\RecipeCreate\RecipeCreateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use VictorCodigo\SymfonyFormExtended\Factory\FormFactoryExtendedInterface;

#[Route(
    name: 'recipe_create',
    path: '/{_locale}/recipe/create',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class RecipeCreateController extends AbstractController
{
    public const string FORM_FLASH_BAG_MESSAGES_SUCCESS = 'recipeCreateForm.success';
    public const string FORM_FLASH_BAG_MESSAGES_ERROR = 'recipeCreateForm.error';

    public function __construct(
        private RecipeCreateService $recipeCreateService,
        private FormFactoryExtendedInterface $formFactoryExtended,
        private readonly int $appConfigPaginationPageMaxItems,
    ) {
    }

    /**
     * @throws RecipeCreateException
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $form = $this->formFactoryExtended
            ->createNamedExtended(RECIPE_CREATE_FORM_FIELDS::FORM_NAME->value, RecipeCreateFormType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeCreateService->__invoke($request, $form, null);
        }

        $form->addFlashMessagesTranslated(self::FORM_FLASH_BAG_MESSAGES_SUCCESS, self::FORM_FLASH_BAG_MESSAGES_ERROR, true);

        return $this->redirectToRoute('recipe_home', [
            'page' => 1,
            'pageItems' => $this->appConfigPaginationPageMaxItems,
        ],
            Response::HTTP_SEE_OTHER
        );
    }
}
