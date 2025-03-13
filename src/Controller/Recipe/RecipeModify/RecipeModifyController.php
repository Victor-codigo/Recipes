<?php

declare(strict_types=1);

namespace App\Controller\Recipe\RecipeModify;

use App\Form\Recipe\RecipeModify\RECIPE_MODIFY_FORM_FIELDS;
use App\Form\Recipe\RecipeModify\RecipeModifyFormType;
use App\Service\Exception\RecipeModifyException;
use App\Service\Recipe\RecipeModify\RecipeModifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use VictorCodigo\SymfonyFormExtended\Factory\FormFactoryExtendedInterface;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

#[Route(
    name: 'recipe_modify',
    path: '/{_locale}/recipe/modify',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class RecipeModifyController extends AbstractController
{
    public const string FORM_FLASH_BAG_MESSAGES_SUCCESS = 'recipeModifyForm.success';
    public const string FORM_FLASH_BAG_MESSAGES_ERROR = 'recipeModifyForm.error';

    public function __construct(
        private RecipeModifyService $recipeModifyService,
        private FormFactoryExtendedInterface $formFactoryExtended,
        private readonly int $appConfigPaginationPageMaxItems,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $form = $this->formFactoryExtended
            ->createNamedExtended(RECIPE_MODIFY_FORM_FIELDS::FORM_NAME->value, RecipeModifyFormType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeModify($request, $form);
        }

        $form->addFlashMessagesTranslated(self::FORM_FLASH_BAG_MESSAGES_SUCCESS, self::FORM_FLASH_BAG_MESSAGES_ERROR, true);

        return $this->redirectToRoute('recipe_home', [
            'page' => 1,
            'pageItems' => $this->appConfigPaginationPageMaxItems,
        ],
            Response::HTTP_SEE_OTHER
        );
    }

    private function recipeModify(Request $request, FormExtendedInterface $form): void
    {
        try {
            $this->recipeModifyService->__invoke($request, $form, null);
        } catch (RecipeModifyException) {
            $form->addError(new FormError('Recipe not found'));
        }
    }
}
