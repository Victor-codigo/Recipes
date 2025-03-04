<?php

namespace App\Templates\Components\HomeSection\Home;

use App\Templates\Components\AlertValidation\AlertValidationComponentDto;
use App\Templates\Components\HomeSection\HomeList\HomeListComponentBuilder;
use App\Templates\Components\HomeSection\HomeList\List\HomeListComponentDto;
use App\Templates\Components\HomeSection\SearchBar\SearchBarComponentDto;
use App\Templates\Components\Modal\ModalComponentDto;
use App\Templates\Components\Title\TITLE_TYPE;
use App\Templates\Components\Title\TitleComponentDto;
use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'HomeSectionComponent',
    template: 'Components/HomeSection/Home/HomeSectionComponent.html.twig'
)]
class HomeSectionComponent extends TwigComponent
{
    public HomeSectionComponentLangDto $lang;
    public HomeSectionComponentDto&TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;
    public readonly ?SearchBarComponentDto $searchBarFormDto;
    public readonly ?ModalComponentDto $createFormModalDto;
    public readonly ?ModalComponentDto $removeMultiFormModalDto;
    public readonly HomeListComponentDto $listComponentDto;
    public readonly AlertValidationComponentDto $alertValidationComponentDto;

    public static function getComponentName(): string
    {
        return 'HomeSectionComponent';
    }

    public function mount(HomeSectionComponentDto $data): void
    {
        $this->data = $data;
        $this->createFormModalDto = $this->data->createFormModalDto;
        $this->removeMultiFormModalDto = $this->data->removeMultiFormModalDto;
        $this->loadTranslation();
        $this->titleDto = $this->createTitleDto($this->data->title, $this->data->titlePath);
        $this->searchBarFormDto = $this->data->searchComponentDto;
        $this->listComponentDto = $this->createListComponentDto();
        $this->alertValidationComponentDto = $this->createAlertValidationComponentDto();
    }

    private function createTitleDto(?string $title, ?string $titlePath): TitleComponentDto
    {
        if (null !== $titlePath) {
            $titlePath .= ' / ';
        }

        if (null === $title) {
            return new TitleComponentDto($this->lang->title, TITLE_TYPE::PAGE_MAIN, $titlePath);
        }

        return new TitleComponentDto($title, TITLE_TYPE::PAGE_MAIN, $titlePath);
    }

    private function createListComponentDto(): HomeListComponentDto
    {
        return (new HomeListComponentBuilder())
            ->pagination(
                $this->data->page,
                $this->data->pageItems,
                $this->data->pagesTotal
            )
            ->listItemModifyForm(
                $this->data->modifyFormModalDto
            )
            ->listItemRemoveForm(
                $this->data->removeFormModalDto
            )
            ->listItems(
                $this->data->listItemComponentName,
                $this->data->listItems,
                $this->data->listItemNoImagePath,
            )
            ->validation(
                [],
                false
            )
            ->translationDomainNames(
                $this->data->translationHomeListDomainName,
            )
            ->build();
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        return new AlertValidationComponentDto(
            $this->data->homeSectionMessageValidationOk,
            $this->data->homeSectionErrorsMessage
        );
    }

    private function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationHomeDomainName);
        $this->lang = (new HomeSectionComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->buttonAdd(
                $this->translate('home_section_add.label'),
                $this->translate('home_section_add.title'),
            )
            ->buttonRemoveMultiple(
                $this->translate('home_section_remove_multiple.label'),
                $this->translate('home_section_remove_multiple.title'),
            )
            ->errors(
                $this->data->validForm ? $this->createAlertValidationComponentDto() : null
            )
            ->build();
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }
}
