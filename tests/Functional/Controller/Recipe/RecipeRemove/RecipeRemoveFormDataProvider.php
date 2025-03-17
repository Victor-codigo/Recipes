<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Recipe\RecipeRemove;

use App\Form\Recipe\RecipeRemove\RECIPE_REMOVE_FORM_FIELDS;
use App\Tests\Traits\TestingFixturesTrait;

class RecipeRemoveFormDataProvider
{
    use TestingFixturesTrait;

    /**
     * @return iterable<array{
     *     request: array{
     *          recipes_id?: array<int, string>,
     *      },
     *      recipesExpectedNotToBeRemovedId: array<int, string>,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }>
     */
    public static function dataProvider(): iterable
    {
        yield self::formWithRecipeId();
        yield self::formWithManyRecipeIds();
        yield self::formWithManyRecipeIdsNotAllFound();
        yield self::formWithoutRecipeId();
        yield self::formWithRecipeIdIsWrong();
        yield self::formWithRecipeIdSomeAreWrong();
        yield self::formWithRecipesIdNotFound();
    }

    /**
     * @return array{
     *      request: array{
     *          recipes_id: array<int, string>,
     *      },
     *      recipesExpectedNotToBeRemovedId: array<int, string>,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithRecipeId(): array
    {
        return [
            'request' => [
                RECIPE_REMOVE_FORM_FIELDS::RECIPES_ID->value => [self::RECIPE_1_FIXTURES_ID],
            ],
            'recipesExpectedNotToBeRemovedId' => [],
            'validationOk' => true,
            'messagesOk' => ['form.validation.msg.ok'],
            'messagesError' => [],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          recipes_id: array<int, string>,
     *      },
     *      recipesExpectedNotToBeRemovedId: array<int, string>,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithManyRecipeIds(): array
    {
        return [
            'request' => [
                RECIPE_REMOVE_FORM_FIELDS::RECIPES_ID->value => [
                    self::RECIPE_1_FIXTURES_ID,
                    self::RECIPE_2_FIXTURES_ID,
                ],
            ],
            'recipesExpectedNotToBeRemovedId' => [],
            'validationOk' => true,
            'messagesOk' => ['form.validation.msg.ok'],
            'messagesError' => [],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          recipes_id: array<int, string>,
     *      },
     *      recipesExpectedNotToBeRemovedId: array<int, string>,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithManyRecipeIdsNotAllFound(): array
    {
        return [
            'request' => [
                RECIPE_REMOVE_FORM_FIELDS::RECIPES_ID->value => [
                    self::RECIPE_1_FIXTURES_ID,
                    self::RECIPE_2_FIXTURES_ID,
                    self::RECIPE_4_FIXTURES_ID,
                ],
            ],
            'recipesExpectedNotToBeRemovedId' => [self::RECIPE_4_FIXTURES_ID],
            'validationOk' => true,
            'messagesOk' => ['form.validation.msg.ok'],
            'messagesError' => [],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          recipes_id: array<int, string>,
     *      },
     *      recipesExpectedNotToBeRemovedId: array<int, string>,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithRecipesIdNotFound(): array
    {
        return [
            'request' => [
                RECIPE_REMOVE_FORM_FIELDS::RECIPES_ID->value => [
                    'a28e78ac-bb18-45f0-b08e-8c992081c5ba',
                    'b7527110-b7c4-498c-adae-adcaccf6fd80',
                ],
            ],
            'recipesExpectedNotToBeRemovedId' => [],
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['Recipes not found'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          recipes_id?: array<int, string>,
     *      },
     *      recipesExpectedNotToBeRemovedId: array<int, string>,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithoutRecipeId(): array
    {
        return [
            'request' => [],
            'recipesExpectedNotToBeRemovedId' => [],
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['form.validation.msg.error'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          recipes_id?: array<int, string>,
     *      },
     *      recipesExpectedNotToBeRemovedId: array<int, string>,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithRecipeIdIsWrong(): array
    {
        return [
            'request' => [
                RECIPE_REMOVE_FORM_FIELDS::RECIPES_ID->value => ['Wrong recipe id'],
            ],
            'recipesExpectedNotToBeRemovedId' => [],
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['form.validation.msg.error'],
        ];
    }

    /**
     * @return array{
     *      request: array{
     *          recipes_id?: array<int, string>,
     *      },
     *      recipesExpectedNotToBeRemovedId: array<int, string>,
     *      validationOk: bool,
     *      messagesOk: array<int, string>,
     *      messagesError: array<int, string>
     * }
     */
    private static function formWithRecipeIdSomeAreWrong(): array
    {
        return [
            'request' => [
                RECIPE_REMOVE_FORM_FIELDS::RECIPES_ID->value => [
                    self::RECIPE_1_FIXTURES_ID,
                    'Wrong recipe id',
                    self::RECIPE_2_FIXTURES_ID,
                ],
            ],
            'recipesExpectedNotToBeRemovedId' => [
                self::RECIPE_1_FIXTURES_ID,
                self::RECIPE_2_FIXTURES_ID,
            ],
            'validationOk' => false,
            'messagesOk' => [],
            'messagesError' => ['form.validation.msg.error'],
        ];
    }
}
