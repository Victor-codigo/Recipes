<?php

declare(strict_types=1);

namespace App\Controller\Recipe\RecipeRemove;

use App\Form\Recipe\RecipeRemove\RECIPE_REMOVE_FORM_FIELDS;
use App\Form\Recipe\RecipeRemove\RecipeRemoveFormType;
use App\Service\Exception\RecipeRemoveException;
use App\Service\Recipe\RecipeRemove\RecipeRemoveService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use VictorCodigo\SymfonyFormExtended\Factory\FormFactoryExtendedInterface;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

#[Route(
    name: 'recipe_remove',
    path: '/{_locale}/recipe/remove',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class RecipeRemoveController extends AbstractController
{
    public const string FORM_FLASH_BAG_MESSAGES_SUCCESS = 'recipeRemoveForm.success';
    public const string FORM_FLASH_BAG_MESSAGES_ERROR = 'recipeRemoveForm.error';

    public function __construct(
        private RecipeRemoveService $recipeRemoveService,
        private FormFactoryExtendedInterface $formFactoryExtended,
        private readonly int $appConfigPaginationPageMaxItems,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $form = $this->formFactoryExtended
            ->createNamedExtended(RECIPE_REMOVE_FORM_FIELDS::FORM_NAME->value, RecipeRemoveFormType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeRemove($form);
        }

        $form->addFlashMessagesTranslated(self::FORM_FLASH_BAG_MESSAGES_SUCCESS, self::FORM_FLASH_BAG_MESSAGES_ERROR, true);

        return $this->redirectToRoute('recipe_home', [
            'page' => 1,
            'pageItems' => $this->appConfigPaginationPageMaxItems,
        ],
            Response::HTTP_SEE_OTHER
        );
    }

    private function recipeRemove(FormExtendedInterface $form): void
    {
        try {
            $this->recipeRemoveService->__invoke($form, null);
        } catch (RecipeRemoveException) {
            $form->addError(new FormError('Recipes not found'));
        }
    }
}
