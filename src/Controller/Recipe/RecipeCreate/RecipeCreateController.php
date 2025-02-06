<?php

declare(strict_types=1);

namespace App\Controller\Recipe\RecipeCreate;

use App\Common\Config;
use App\Controller\Exception\FormDataEmptyException;
use App\Form\Factory\FormFactoryExtendedInterface;
use App\Form\Factory\Form\FormTranslated;
use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormDataValidation;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormType;
use App\Service\Recipe\RecipeCreate\RecipeCreateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    ) {
    }

    /**
     * @throws FormDataEmptyException
     */
    public function __invoke(Request $request): Response
    {
        $form = $this->formFactoryExtended->createNamedTranslated(RECIPE_CREATE_FORM_FIELDS::FORM_NAME->value, RecipeCreateFormType::class, 'RecipeCreateComponent');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeCreate($form);
        }

        $form->addFlashMessagesTranslated(self::FORM_FLASH_BAG_MESSAGES_SUCCESS, self::FORM_FLASH_BAG_MESSAGES_ERROR);

        return $this->redirectToRoute('recipe_home', [
            'page' => 1,
            'pageItems' => Config::PAGINATION_PAGE_MAX_ITEMS,
        ]);
    }

    /**
     * @param FormInterface<FormTranslated> $form
     */
    private function recipeCreate(FormInterface $form): void
    {
        /** @var RecipeCreateFormDataValidation */
        $formData = $form->getData();

        $this->recipeCreateService->__invoke($formData, null);
    }
}
