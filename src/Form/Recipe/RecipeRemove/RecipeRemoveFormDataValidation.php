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
    #[Assert\All(
        new Assert\Uuid(versions: Assert\Uuid::V4_RANDOM)
    )]
    public array $recipes_id;
}
