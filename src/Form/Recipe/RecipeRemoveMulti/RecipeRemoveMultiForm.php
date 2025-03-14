<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeRemoveMulti;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VictorCodigo\SymfonyFormExtended\Form\FormMessage;
use VictorCodigo\SymfonyFormExtended\Type\FormTypeBase;
use VictorCodigo\SymfonyFormExtended\Type\FormTypeExtendedInterface;

/**
 * @extends FormTypeBase<RecipeRemoveMultiForm>
 *
 * @implements FormTypeExtendedInterface<RecipeRemoveMultiForm>
 */
class RecipeRemoveMultiForm extends FormTypeBase implements FormTypeExtendedInterface
{
    public const string TRANSLATION_DOMAIN = 'RecipeRemoveMultiComponent';
    public const string CSRF_TOKEN_ID = 'RecipeRemoveMultiForm';
    public const string CSRF_TOKEN_NAME = RECIPE_REMOVE_MULTI_FORM_FIELDS::CSRF_TOKEN->value;

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('csrf_token_id', self::CSRF_TOKEN_ID);
        $resolver->setDefault('data_class', RecipeRemoveMultiFormDataValidation::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(RECIPE_REMOVE_MULTI_FORM_FIELDS::CSRF_TOKEN->value, HiddenType::class)
            ->add(RECIPE_REMOVE_MULTI_FORM_FIELDS::RECIPES_ID->value, CollectionType::class, [
                'allow_add' => true,
                'trim' => true,
            ])
            ->add(RECIPE_REMOVE_MULTI_FORM_FIELDS::SUBMIT->value, SubmitType::class);
    }

    /**
     * @return Collection<array-key, FormMessage>
     */
    public function getFormSuccessMessages(): Collection
    {
        $messagesOk = [];
        foreach (RecipeRemoveMultiFormDataValidation::FORM_SUCCESS_MESSAGES as $message) {
            $messagesOk[] = new FormMessage($message, $message, [], null);
        }

        return new ArrayCollection($messagesOk);
    }
}
