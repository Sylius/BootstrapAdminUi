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
            'sylius_admin.common.component.sidebar' => [
                'logo' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar/logo.html.twig',
                ],
                'toggle_button' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar/toggle_button.html.twig',
                ],
                'menu' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar/menu.html.twig',
                ],
            ],

            'sylius_admin.common.component.sidebar.logo' => [
                'image' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar/logo/image.html.twig',
                ],
            ],
        ],
    ]);
};
