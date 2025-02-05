<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeCreate;

use App\Common\RECIPE_TYPE;
use App\Form\FormBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

/**
 * @template-extends FormBase<RecipeCreateFormDataValidation>
 */
class RecipeCreateFormType extends FormBase
{
    protected const string CSRF_TOKEN_ID = 'RecipeCreateForm';

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
            ->add(RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value, CollectionType::class, ['allow_add' => true])
            ->add(RECIPE_CREATE_FORM_FIELDS::STEPS->value, CollectionType::class, ['allow_add' => true])
            ->add(RECIPE_CREATE_FORM_FIELDS::IMAGE->value, DropzoneType::class)
            ->add(RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value, NumberType::class)
            ->add(RECIPE_CREATE_FORM_FIELDS::CATEGORY->value, EnumType::class, ['class' => RECIPE_TYPE::class])
            ->add(RECIPE_CREATE_FORM_FIELDS::PUBLIC->value, CheckboxType::class)
            ->add(RECIPE_CREATE_FORM_FIELDS::SUBMIT->value, SubmitType::class);
    }

    /**
     * @return Collection<string, string>
     */
    public function getTranslationParams(): Collection
    {
        return new ArrayCollection([
            'nameLettersMin' => '255',
            'nameCharactersMax' => '2',
            'descriptionCharactersMax' => '500',
            'ingredientsCharactersMax' => '255',
            'stepsCharactersMax' => '500',
            'imageSizeMax' => '2 MB',
            'imageWidthMin' => '200',
            'imageWidthMax' => '400',
            'imageHeightMin' => '200',
            'imageHeightMax' => '400',
            'imageMineTypesAllowed' => 'jpg, jpeg, png',
        ]);
    }

    /**
     * @return Collection<int, FormError>
     */
    public function getMessagesOk(): Collection
    {
        return new ArrayCollection([
            new FormError('form.validation.msg.ok'),
        ]);
    }
}
