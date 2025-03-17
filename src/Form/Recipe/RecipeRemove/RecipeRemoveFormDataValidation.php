<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeRemove;

use Symfony\Component\Validator\Constraints as Assert;

class RecipeRemoveFormDataValidation
{
    public const array FORM_SUCCESS_MESSAGES = [
        'form.validation.msg.ok',
    ];

    /**
     * @var array<int, string>
     */
    #[Assert\NotBlank(message: 'form.validation.msg.error')]
    #[Assert\NotNull(message: 'form.validation.msg.error')]
    #[Assert\All(
        new Assert\Uuid(
            versions: Assert\Uuid::V4_RANDOM,
            message: 'form.validation.msg.error'
        )
    )]
    public array $recipes_id;
}
