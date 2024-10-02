<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.security.login' => [
                'logo' => [
                    'template' => '@SyliusBootstrapAdminUi/security/common/logo.html.twig',
                ],
                'content' => [
                    'template' => '@SyliusBootstrapAdminUi/security/common/content.html.twig',
                ],
            ],

            'sylius_admin.security.login.content' => [
                'header' => [
                    'template' => '@SyliusBootstrapAdminUi/security/common/content/header.html.twig',
                ],
                'flashes' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/flashes.html.twig',
                ],
                'form' => [
                    'template' => '@SyliusBootstrapAdminUi/security/login/content/form.html.twig',
                ],
            ],

            'sylius_admin.security.login.content.form' => [
                'error' => [
                    'template' => '@SyliusBootstrapAdminUi/security/login/content/form/error.html.twig',
                ],
                'username' => [
                    'template' => '@SyliusBootstrapAdminUi/security/login/content/form/username.html.twig',
                ],
                'password' => [
                    'template' => '@SyliusBootstrapAdminUi/security/login/content/form/password.html.twig',
                ],
                'remember_me' => [
                    'template' => '@SyliusBootstrapAdminUi/security/login/content/form/remember_me.html.twig',
                ],
                'submit' => [
                    'template' => '@SyliusBootstrapAdminUi/security/login/content/form/submit.html.twig',
                ],
            ],
        ],
    ]);
};
