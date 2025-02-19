<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeCreate;

use App\Common\RECIPE_TYPE;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;
use VictorCodigo\SymfonyFormExtended\Type\FormTypeBase;
use VictorCodigo\SymfonyFormExtended\Type\FormTypeExtendedInterface;

/**
 * @extends FormTypeBase<RecipeCreateFormType>
 *
 * @implements FormTypeExtendedInterface<RecipeCreateFormType>
 */
class RecipeCreateFormType extends FormTypeBase implements FormTypeExtendedInterface
{
    public const string TRANSLATION_DOMAIN = 'RecipeCreateComponent';
    protected const string CSRF_TOKEN_ID = 'RecipeCreateForm';
    protected const string CSRF_TOKEN_NAME = RECIPE_CREATE_FORM_FIELDS::CSRF_TOKEN->value;

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('csrf_token_id', self::CSRF_TOKEN_ID);
        $resolver->setDefault('data_class', RecipeCreateFormDataValidation::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(RECIPE_CREATE_FORM_FIELDS::NAME->value, TextType::class, ['trim' => true])
            ->add(RECIPE_CREATE_FORM_FIELDS::DESCRIPTION->value, TextareaType::class, ['trim' => true])
            ->add(RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value, TimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'input_format' => 'hh:mm',
            ])
            ->add(RECIPE_CREATE_FORM_FIELDS::CATEGORY->value, EnumType::class, ['class' => RECIPE_TYPE::class])
            ->add(RECIPE_CREATE_FORM_FIELDS::PUBLIC->value, CheckboxType::class)
            ->add(RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value, CollectionType::class, ['allow_add' => true, 'trim' => true])
            ->add(RECIPE_CREATE_FORM_FIELDS::STEPS->value, CollectionType::class, ['allow_add' => true, 'trim' => true])
            ->add(RECIPE_CREATE_FORM_FIELDS::IMAGE->value, DropzoneType::class)
            ->add(RECIPE_CREATE_FORM_FIELDS::SUBMIT->value, SubmitType::class);
    }

    /**
     * @return Collection<array-key, FormError>
     */
    public function getFormSuccessMessages(): Collection
    {
        $messagesOk = [];
        foreach (RecipeCreateFormDataValidation::FORM_SUCCESS_MESSAGES as $message) {
            $messagesOk[] = new FormError($message);
        }

        return new ArrayCollection($messagesOk);
    }
}
