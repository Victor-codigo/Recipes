<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Recipe\RecipeCreate;

use App\Common\RECIPE_TYPE;
use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Tests\Traits\TestingFormTrait;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RecipeCreateFormDataProvider
{
    use TestingFormTrait;

    /**
     * @return iterable<array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  expectedIsValid: bool
     * }>
     */
    public static function formDataDataProvider(): iterable
    {
        // ALL DATA
        yield [
            ...self::recipeCreateFormAllDataValidDataProvider(),
            'expectedIsValid' => true,
        ];

        // NAME
        yield [
            ...self::recipeCreateFormNameIsTooShort(),
            'expectedIsValid' => false,
        ];

        yield [
            ...self::recipeCreateFormNameIsTooLargeMoreThan255Characters(),
            'expectedIsValid' => false,
        ];

        // MESSAGE
        yield [
            ...self::recipeCreateFormMessageIsEmpty(),
            'expectedIsValid' => true,
        ];

        yield [
            ...self::recipeCreateFormMessageIsEmpty(),
            'expectedIsValid' => true,
        ];

        yield [
            ...self::recipeCreateFormMessageIsLargerThan500Characters(),
            'expectedIsValid' => false,
        ];

        // INGREDIENTS
        yield [
            ...self::recipeCreateFormIngredientsHasNotIngredients(),
            'expectedIsValid' => false,
        ];

        yield [
            ...self::recipeCreateFormIngredientsHasMoreThan100Ingredients(),
            'expectedIsValid' => false,
        ];

        yield [
            ...self::recipeCreateFormIngredientsHasIngredientsEmpty(),
            'expectedIsValid' => false,
        ];

        yield [
            ...self::recipeCreateFormIngredientsHasIngredientsWithMoreThan250Characters(),
            'expectedIsValid' => false,
        ];

        // STEPS
        yield [
            ...self::recipeCreateFormStepsHasNotSteps(),
            'expectedIsValid' => false,
        ];

        yield [
            ...self::recipeCreateFormStepsHasMoreThan100Steps(),
            'expectedIsValid' => false,
        ];

        yield [
            ...self::recipeCreateFormStepsHasStepsEmpty(),
            'expectedIsValid' => false,
        ];

        yield [
            ...self::recipeCreateFormStepsHasStepsWithMoreThan500Characters(),
            'expectedIsValid' => false,
        ];

        // PREPARATION TIME
        yield [
            ...self::recipeCreateFormPreparationTimeIsEmpty(),
            'expectedIsValid' => true,
        ];

        yield [
            ...self::recipeCreateFormPreparationTimeInvalidTime(),
            'expectedIsValid' => false,
        ];

        yield [
            ...self::recipeCreateFormPreparationTimeGreaterThan0Hours(),
            'expectedIsValid' => true,
        ];

        yield [
            ...self::recipeCreateFormPreparationTimeLessThan24Hours(),
            'expectedIsValid' => false,
        ];

        // CATEGORY
        yield [
            ...self::recipeCreateFormCategoryIsWrong(),
            'expectedIsValid' => false,
        ];

        // PUBLIC
        yield [
            ...self::recipeCreateFormPublicHasValue(),
            'expectedIsValid' => true,
        ];

        // IMAGE
        yield [
            ...self::recipeCreateFormImageIsNull(),
            'expectedIsValid' => true,
        ];

        yield [
            ...self::recipeCreateFormImageUploadOk(),
            'expectedIsValid' => true,
        ];
    }

    /**
     * @return array{
     *  name: string,
     *  description: string|null,
     *  image: UploadedFile|null,
     *  preparation_time: string|null,
     *  category: string,
     *  steps: array<int, string>,
     *  ingredients: array<int, string>
     * }
     */
    public static function recipeCreateFormAllFieldsDefault(): array
    {
        return [
            RECIPE_CREATE_FORM_FIELDS::NAME->value => 'name',
            RECIPE_CREATE_FORM_FIELDS::DESCRIPTION->value => '',
            RECIPE_CREATE_FORM_FIELDS::IMAGE->value => null,
            RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value => null,
            RECIPE_CREATE_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
            RECIPE_CREATE_FORM_FIELDS::STEPS->value => [
                'step 1',
            ],
            RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value => [
                'Ingredient 1',
            ],
        ];
    }

    /**
     * @return array{
     *  name: string,
     *  description: string|null,
     *  image: UploadedFile|null,
     *  preparation_time: DateTimeImmutable|null,
     *  category: RECIPE_TYPE,
     *  public: bool,
     *  steps: array<int, string>,
     *  ingredients: array<int, string>
     * }
     */
    public static function recipeCreateFormDataDefault(): array
    {
        return [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::DESCRIPTION->value => null,
            RECIPE_CREATE_FORM_FIELDS::IMAGE->value => null,
            RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value => null,
            RECIPE_CREATE_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY,
            RECIPE_CREATE_FORM_FIELDS::PUBLIC->value => false,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormAllDataValidDataProvider(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::NAME->value => 'name',
        ];

        $expected = self::recipeCreateFormDataDefault();

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormNameIsTooShort(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::NAME->value => 'n',
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::NAME->value => 'n',
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormNameIsTooLargeMoreThan255Characters(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::NAME->value => str_pad('', 256, 'm'),
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::NAME->value => str_pad('', 256, 'm'),
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormMessageIsEmpty(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::DESCRIPTION->value => '',
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::DESCRIPTION->value => '',
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormMessageIsLargerThan500Characters(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::DESCRIPTION->value => str_pad('', 501, 'm'),
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::DESCRIPTION->value => str_pad('', 501, 'm'),
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormIngredientsHasNotIngredients(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::STEPS->value => [],
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::STEPS->value => [],
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormIngredientsHasMoreThan100Ingredients(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value => array_fill(0, 101, 'm'),
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value => array_fill(0, 101, 'm'),
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormIngredientsHasIngredientsEmpty(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value => [
                'ingredient 1',
                '',
                'ingredient 3',
            ],
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value => [
                'ingredient 1',
                '',
                'ingredient 3',
            ],
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormIngredientsHasIngredientsWithMoreThan250Characters(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value => [
                'ingredient 1',
                str_pad('', 256, 'm'),
                'ingredient 3',
            ],
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value => [
                'ingredient 1',
                str_pad('', 256, 'm'),
                'ingredient 3',
            ],
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormStepsHasNotSteps(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::STEPS->value => [],
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::STEPS->value => [],
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormStepsHasMoreThan100Steps(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::STEPS->value => array_fill(0, 101, 'm'),
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::STEPS->value => array_fill(0, 101, 'm'),
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormStepsHasStepsEmpty(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::STEPS->value => [
                'step 1',
                '',
                'step 3',
            ],
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::STEPS->value => [
                'step 1',
                '',
                'step 3',
            ],
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormStepsHasStepsWithMoreThan500Characters(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value => [
                'ingredient 1',
                str_pad('', 501, 'm'),
                'ingredient 3',
            ],
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value => [
                'ingredient 1',
                str_pad('', 501, 'm'),
                'ingredient 3',
            ],
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormPreparationTimeIsEmpty(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value => '',
        ];

        $expected = self::recipeCreateFormDataDefault();

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormPreparationTimeInvalidTime(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value => 'asdf',
        ];

        $expected = self::recipeCreateFormDataDefault();

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormPreparationTimeGreaterThan0Hours(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value => '00:01',
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value => new \DateTimeImmutable('1970-01-01 00:01:00'),
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormPreparationTimeLessThan24Hours(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value => '24:00',
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value => null,
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormCategoryIsWrong(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::CATEGORY->value => 'wrong category',
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value => null,
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormPublicHasValue(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::PUBLIC->value => 'wrong value',
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::PUBLIC->value => true,
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormImageIsNull(): array
    {
        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::IMAGE->value => null,
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::IMAGE->value => null,
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }

    /**
     * @return array{
     *  formDataSubmitted: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: string|null,
     *      category: string,
     *      public?: string,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  },
     *  formDataExpected: array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  }}
     */
    public static function recipeCreateFormImageUploadOk(): array
    {
        $imageUpload = new UploadedFile(
            '/home/developer/Recipes/tests/Fixtures/img/ImageUpload.png',
            'ImageUpload.png',
            'image/png',
            UPLOAD_ERR_OK,
            true
        );

        $submit = [
            ...self::recipeCreateFormAllFieldsDefault(),
            RECIPE_CREATE_FORM_FIELDS::IMAGE->value => $imageUpload,
        ];

        $expected = [
            ...self::recipeCreateFormDataDefault(),
            RECIPE_CREATE_FORM_FIELDS::IMAGE->value => null,
        ];

        return [
            'formDataSubmitted' => $submit,
            'formDataExpected' => $expected,
        ];
    }
}
