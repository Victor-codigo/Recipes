<?php

declare(strict_types=1);

namespace App\Form\HomeSection\SearchBar;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VictorCodigo\SymfonyFormExtended\Form\FormMessage;
use VictorCodigo\SymfonyFormExtended\Type\FormTypeBase;
use VictorCodigo\SymfonyFormExtended\Type\FormTypeExtendedInterface;

/**
 * @extends FormTypeBase<SearchFormType>
 *
 * @implements FormTypeExtendedInterface<SearchFormType>
 */
class SearchFormType extends FormTypeBase implements FormTypeExtendedInterface
{
    public const string TRANSLATION_DOMAIN = 'RecipeRemoveComponent';
    public const string CSRF_TOKEN_ID = 'RecipeRemoveForm';
    public const string CSRF_TOKEN_NAME = SEARCHBAR_FORM_FIELDS::CSRF_TOKEN->value;

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('csrf_token_id', self::CSRF_TOKEN_ID);
        $resolver->setDefault('data_class', SearchFormDataValidation::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value, EnumType::class, [
                'class' => FIELD_FILTERS::class,
            ])
            ->add(SEARCHBAR_FORM_FIELDS::NAME_FILTER->value, EnumType::class, [
                'class' => SEARCH_TEXT_FILTER::class,
            ])
            ->add(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value, TextType::class, ['trim' => true])
            ->add(SEARCHBAR_FORM_FIELDS::SUBMIT->value, SubmitType::class);
    }

    /**
     * @return Collection<array-key, FormMessage>
     */
    public function getFormSuccessMessages(): Collection
    {
        return new ArrayCollection([]);
    }
}
