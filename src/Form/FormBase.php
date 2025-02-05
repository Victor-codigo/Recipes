<?php

declare(strict_types=1);

namespace App\Form;

use App\Common\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @template TData
 *
 * @template-extends AbstractType<TData>
 */
abstract class FormBase extends AbstractType
{
    protected const string CSRF_TOKEN_ID = '';
    protected const string CSRF_TOKEN_NAME = Config::FORM_TOKEN_FIELD_NAME;

    public function __construct(
        protected TranslatorInterface $translator,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private RequestStack $request,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('csrf_protection', true);
        $resolver->setDefault('csrf_field_name', static::CSRF_TOKEN_NAME);
    }

    public function getCsrfToken(): string
    {
        return $this->csrfTokenManager->getToken(static::CSRF_TOKEN_ID)->getValue();
    }

    public function getCsrfTokenFieldName(): string
    {
        return self::CSRF_TOKEN_NAME;
    }
}
