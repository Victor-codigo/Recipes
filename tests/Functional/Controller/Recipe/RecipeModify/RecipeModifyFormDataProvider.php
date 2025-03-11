<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Recipe\RecipeModify;

use App\Common\RECIPE_TYPE;
use App\Form\Recipe\RecipeModify\RECIPE_MODIFY_FORM_FIELDS;
use App\Tests\Traits\TestingFixturesTrait;
use App\Tests\Traits\TestingImageTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RecipeModifyFormDataProvider
{
    use TestingImageTrait;
    use TestingFixturesTrait;

    /**
     * @return iterable<array{
     *     request: array{
     *          id: string,
     *          name?: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category?: string,
     *          public?: bool,
     *          steps?: array<int, string>,
     *          ingredients?: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }>
     */
    public static function dataProvider(): iterable
    {
        yield self::formWithAllData();
        yield self::formWithMinimumData();

        // id
        yield self::formWithIdIsWrong();

        // name
        yield self::formWithNameIsNotSet();
        yield self::formWithNameIsTooShort();
        yield self::formWithNameIsTooLarge();

        // description
        yield self::formWithDescriptionIsNotSet();

        // Preparation time
        yield self::formWithPreparationTimeWrong();
        yield self::formWithPreparationTimeMinimum();
        yield self::formWithPreparationTimeMinimumOut();
        yield self::formWithPreparationTimeMaximum();
        yield self::formWithPreparationTimeMaximumOut();

        // Category
        yield self::formWithCategoryNotSet();
        yield self::formWithCategoryWrong();

        // Ingredients
        yield self::formWithIngredientsNotSet();
        yield self::formWithIngredientsEmpty();
        yield self::formWithIngredientsTooMany();
        yield self::formWithIngredientsOneOrMoreAreBlank();
        yield self::formWithIngredientsOneOrMoreAreTooLarge();

        // Steps
        yield self::formWithStepsNotSet();
        yield self::formWithStepsEmpty();
        yield self::formWithStepsTooMany();
        yield self::formWithStepsOneOrMoreAreBlank();
        yield self::formWithStepsOneOrMoreAreTooLarge();

        // Image
        yield self::formWithImageValid();
        yield self::formWithImageWidthTooShort();
        yield self::formWithImageWidthTooLarge();
        yield self::formWithImageHeightTooShort();
        yield self::formWithImageHeightTooLarge();
        yield self::formWithImageMimeTypeWrong();

        // Image remove
        yield self::formWithImageRemoveIsSet();
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description: string,
     *          preparation_time: string,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>,
     *          image_remove: bool
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithAllData(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::DESCRIPTION->value => 'Recipe description test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '2:00',
                RECIPE_MODIFY_FORM_FIELDS::PUBLIC->value => true,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                    'Ingredient 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                    'Step 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value => false,
            ],
            'files' => [
                'image' => self::createImagePng(200, 200),
            ],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => true,
            'messagesOk' => ['form.validation.msg.ok'],
            'messagesError' => [],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          category: string,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithMinimumData(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => true,
            'messagesOk' => ['form.validation.msg.ok'],
            'messagesError' => [],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description: string,
     *          preparation_time: string,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>,
     *          image_remove: bool
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithIdIsWrong(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => 'wrong id',
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::DESCRIPTION->value => 'Recipe description test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '2:00',
                RECIPE_MODIFY_FORM_FIELDS::PUBLIC->value => true,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                    'Ingredient 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                    'Step 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value => false,
            ],
            'files' => [
                'image' => self::createImagePng(200, 200),
            ],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['validation.message'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          description: string,
     *          preparation_time: string,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>,
     *          image_remove: bool
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithNameIsNotSet(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::DESCRIPTION->value => 'Recipe description test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '2:00',
                RECIPE_MODIFY_FORM_FIELDS::PUBLIC->value => true,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                    'Ingredient 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                    'Step 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value => false,
            ],
            'files' => [
                'image' => self::createImagePng(200, 200),
            ],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.name.msg.error.not_blank'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description: string,
     *          preparation_time: string,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>,
     *          image_remove: bool
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithNameIsTooShort(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'j',
                RECIPE_MODIFY_FORM_FIELDS::DESCRIPTION->value => 'Recipe description test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '2:00',
                RECIPE_MODIFY_FORM_FIELDS::PUBLIC->value => true,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                    'Ingredient 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                    'Step 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value => false,
            ],
            'files' => [
                'image' => self::createImagePng(200, 200),
            ],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.name.msg.error.min'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description: string,
     *          preparation_time: string,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>,
     *          image_remove: bool
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithNameIsTooLarge(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => str_pad('', 256, 'p'),
                RECIPE_MODIFY_FORM_FIELDS::DESCRIPTION->value => 'Recipe description test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '2:00',
                RECIPE_MODIFY_FORM_FIELDS::PUBLIC->value => true,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                    'Ingredient 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                    'Step 2',
                ],
                RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value => false,
            ],
            'files' => [
                'image' => self::createImagePng(200, 200),
            ],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.name.msg.error.max'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithDescriptionIsNotSet(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::DESCRIPTION->value => str_pad('', 501, 'm'),
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.description.msg.error.max'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithPreparationTimeWrong(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => 'wrong time',
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['validation.message'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithPreparationTimeMinimum(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '00:01',
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => true,
            'messagesOk' => ['form.validation.msg.ok'],
            'messagesError' => [],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithPreparationTimeMinimumOut(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '00:00',
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.preparation_time.msg.error.greater_than'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithPreparationTimeMaximum(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '23:59',
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => true,
            'messagesOk' => ['form.validation.msg.ok'],
            'messagesError' => [],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithPreparationTimeMaximumOut(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '24:00',
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['validation.message'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithCategoryNotSet(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['validation.message'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithCategoryWrong(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => 'wrong category',
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['validation.message'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithIngredientsNotSet(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::DESSERT->value,

                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.ingredients.msg.error.ingredientsMin'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithIngredientsEmpty(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.ingredients.msg.error.ingredientsMin'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithIngredientsTooMany(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => array_fill(0, 101, 'ingredient'),
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.ingredients.msg.error.ingredientsMax'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithIngredientsOneOrMoreAreBlank(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                    'Ingredient 2',
                    '',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.ingredients.msg.error.not_blank'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithIngredientsOneOrMoreAreTooLarge(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                    'Ingredient 2',
                    str_pad('', 256, 'm'),
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.ingredients.msg.error.max'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          ingredients: array<int, string>,
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithStepsNotSet(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.steps.msg.error.stepsMin'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithStepsEmpty(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [],
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.steps.msg.error.stepsMin'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithStepsTooMany(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => array_fill(0, 101, 'ingredient'),
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.steps.msg.error.stepsMax'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithStepsOneOrMoreAreBlank(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                    'Step 2',
                    '',
                ],
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.steps.msg.error.not_blank'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithStepsOneOrMoreAreTooLarge(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                    'Step 2',
                    str_pad('', 501, 'm'),
                ],
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
            ],
            'files' => [],
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['field.steps.msg.error.max'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithImageValid(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [
                'image' => self::createImagePng(200, 200),
            ],
            'validationOk' => true,
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'messagesOk' => ['form.validation.msg.ok'],
            'messagesError' => [],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithImageWidthTooShort(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [
                'image' => self::createImagePng(199, 200),
            ],
            'validationOk' => false,
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'messagesOk' => [],
            'messagesError' => ['field.image.msg.error.minWidthMessage'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithImageWidthTooLarge(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [
                'image' => self::createImagePng(401, 200),
            ],
            'validationOk' => false,
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'messagesOk' => [],
            'messagesError' => ['field.image.msg.error.maxWidthMessage'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithImageHeightTooShort(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [
                'image' => self::createImagePng(200, 199),
            ],
            'validationOk' => false,
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'messagesOk' => [],
            'messagesError' => ['field.image.msg.error.minHeightMessage'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithImageHeightTooLarge(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [
                'image' => self::createImagePng(200, 401),
            ],
            'validationOk' => false,
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'messagesOk' => [],
            'messagesError' => ['field.image.msg.error.maxHeightMessage'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithImageMimeTypeWrong(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
            ],
            'files' => [
                'image' => self::createImageBmp(200, 200),
            ],
            'validationOk' => false,
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'messagesOk' => [],
            'messagesError' => ['field.image.msg.error.mimeTypesMessage'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          id: string,
     *          name: string,
     *          description?: string|null,
     *          preparation_time?: string|null,
     *          category: string,
     *          public?: bool,
     *          steps: array<int, string>,
     *          ingredients: array<int, string>,
     *          image_remove: string,
     *      },
     *      files: array<string, UploadedFile>,
     *      recipeToModifyId: string,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithImageRemoveIsSet(): array
    {
        return [
            'request' => [
                RECIPE_MODIFY_FORM_FIELDS::ID->value => self::RECIPE_1_FIXTURES_ID,
                RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
                RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::BREAKFAST->value,
                RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                    'Ingredient 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                    'Step 1',
                ],
                RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value => 'wrong value',
            ],
            'files' => [],
            'validationOk' => true,
            'recipeToModifyId' => self::RECIPE_1_FIXTURES_ID,
            'messagesOk' => ['form.validation.msg.ok'],
            'messagesError' => [],
        ];
    }
}
