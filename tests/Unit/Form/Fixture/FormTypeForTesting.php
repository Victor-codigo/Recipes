<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Fixture;

use App\Form\Factory\Form\FormTypeTranslatedInterface;
use App\Form\FormTypeBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @extends FormTypeBase<FormTypeForTesting>
 *
 * @implements FormTypeTranslatedInterface<FormTypeForTesting>
 */
class FormTypeForTesting extends FormTypeBase implements FormTypeTranslatedInterface
{
    public const string  TRANSLATION_DOMAIN = 'FormTypeForTesting';

    public function getFormSuccessMessages(): Collection
    {
        return new ArrayCollection(['form.messages.success']);
    }
}
