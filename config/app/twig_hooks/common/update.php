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
    $container->extension('twig_hooks', [
        'hooks' => [
            'sylius_admin.common.update' => [
                'sidebar' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar.html.twig',
                ],
                'content' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content.html.twig',
                ],
            ],
            'sylius_admin.common.update.content' => [
                'flashes' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/flashes.html.twig',
                ],
                'header' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header.html.twig',
                ],
            ],
            'sylius_admin.common.update.content.header' => [
                'breadcrumbs' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/update/content/header/breadcrumbs.html.twig',
                ],
                'title_block' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block.html.twig',
                ],
            ],
            'sylius_admin.common.update.content.header.title_block' => [
                'title' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/update/content/header/title_block/title.html.twig',
                ],
                'actions' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/update/content/header/title_block/actions.html.twig',
                ],
            ],
            'sylius_admin.common.update.content.header.title_block.actions' => [
                'cancel' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block/actions/cancel.html.twig',
                    'priority' => -200,
                ],
                'update' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block/actions/update.html.twig',
                    'priority' => -300,
                ],
            ],
        ],
    ]);
};