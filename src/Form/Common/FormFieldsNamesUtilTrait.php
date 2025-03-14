<?php

declare(strict_types=1);

namespace App\Form\Common;

trait FormFieldsNamesUtilTrait
{
    public static function getNameWithForm(\BackedEnum $formField, bool $isArray = false): string
    {
        if (null === static::FORM_NAME) {
            throw new \LogicException('Constant FORM_NAME not defined');
        }

        $fieldWithForm = static::FORM_NAME->value."[$formField->value]";

        if ($isArray) {
            $fieldWithForm .= '[]';
        }

        return $fieldWithForm;
    }
}
