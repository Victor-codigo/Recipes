<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Fixture;

use App\Form\Factory\Form\FormTypeTranslatedInterface;
use App\Form\FormTypeBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormError;

/**
 * @extends FormTypeBase<FormTypeForTesting>
 *
 * @implements FormTypeTranslatedInterface<FormTypeForTesting>
 */
class FormTypeForTesting extends FormTypeBase implements FormTypeTranslatedInterface
{
    public const string  TRANSLATION_DOMAIN = 'FormTypeForTesting';

    /**
     * @return Collection<array-key, FormError>
     */
    public function getFormSuccessMessages(): Collection
    {
        return self::getFormErrors();
    }

    /**
     * @return Collection<array-key, FormError>
     */
    public static function getFormErrors(): Collection
    {
        return new ArrayCollection([
            new FormError(
                'message.error.msg1',
                'messageTemplate.error.msg1',
                [
                    'param1' => 'value1',
                    'param2' => 'value2',
                ],
                null,
                null
            ),
            new FormError(
                'message.error.msg2',
                'messageTemplate.error.msg2',
                [
                    'param1' => 'value1',
                    'param2' => 'value2',
                ],
                1,
                'cause msg 2'
            ),
            new FormError(
                'message.error.msg3',
                'messageTemplate.error.msg3',
                [
                    'param1' => 'value1',
                ],
                2,
                'cause msg 3'
            ),
        ]);
    }
}
