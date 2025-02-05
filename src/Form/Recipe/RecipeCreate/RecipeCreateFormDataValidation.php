<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeCreate;

use App\Common\RECIPE_TYPE;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class RecipeCreateFormDataValidation
{
    #[Assert\NotBlank(message: 'field.name.msg.error.not_blank')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'field.name.msg.error.min',
        maxMessage: 'field.name.msg.error.max',
        charsetMessage: 'field.name.msg.error.charset'
    )]
    public string $name;

    #[Assert\Length(
        max: 500,
        maxMessage: 'field.description.msg.error.max'
    )]
    public ?string $description = null;

    /**
     * @var string[]
     */
    #[Assert\All([
        new Assert\NotBlank(message: 'field.ingredients.msg.error.not_blank'),
        new Assert\Length(
            max: 255,
            maxMessage: 'field.ingredients.msg.error.max'
        ),
    ])]
    #[Assert\Count(
        min: 1,
        minMessage: 'field.ingredients.msg.error.ingredientsMin'
    )]
    public array $ingredients = [];

    /**
     * @var string[]
     */
    #[Assert\All([
        new Assert\NotBlank(message: 'field.steps.msg.error.not_blank'),
        new Assert\Length(
            max: 500,
            maxMessage: 'field.steps.msg.error.max'
        ),
    ])]
    #[Assert\Count(
        min: 1,
        minMessage: 'field.steps.msg.error.stepsMin'
    )]
    public array $steps = [];

    #[Assert\Image(
        maxSize: '2M',
        minWidth: 200,
        maxWidth: 400,
        minHeight: 200,
        maxHeight: 400,
        allowLandscape: false,
        allowPortrait: false,
        mimeTypes: [
            'image/jpeg',
            'image/jpg',
            'image/png',
        ],
        maxSizeMessage: 'field.image.msg.error.maxSizeMessage',
        minWidthMessage: 'field.image.msg.error.minWidthMessage',
        maxWidthMessage: 'field.image.msg.error.maxWidthMessage',
        minHeightMessage: 'field.image.msg.error.minHeightMessage',
        maxHeightMessage: 'field.image.msg.error.maxHeightMessage',
        mimeTypesMessage: 'field.image.msg.error.mimeTypesMessage'
    )]
    public ?File $image = null;

    #[Assert\DateTime(
        format: 'yyyy-mm-dd',
        message: 'field.steps.preparation_time.error'
    )]
    public ?\DateTimeImmutable $preparation_time = null;

    #[Assert\Choice(
        callback: [RECIPE_TYPE::class, 'cases'],
        multiple: false,
        message: 'field.steps.category.error'
    )]
    public RECIPE_TYPE $category = RECIPE_TYPE::NO_CATEGORY;

    #[Assert\Choice([true, false])]
    public bool $public = false;
}
