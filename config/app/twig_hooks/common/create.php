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
            'sylius_admin.common.create' => [
                'sidebar' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar.html.twig',
                ],
                'content' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content.html.twig',
                ],
            ],
            'sylius_admin.common.create.content' => [
                'header' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header.html.twig',
                ],
            ],
            'sylius_admin.common.create.content.header' => [
                'breadcrumbs' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/create/content/header/breadcrumbs.html.twig',
                ],
                'title_block' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block.html.twig',
                ],
            ],
            'sylius_admin.common.create.content.header.title_block' => [
                'title' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/create/content/header/title_block/title.html.twig',
                ],
                'actions' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/create/content/header/title_block/actions.html.twig',
                ],
            ],
            'sylius_admin.common.create.content.header.title_block.actions' => [
//                'cancel' => [
//                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block/actions/cancel.html.twig',
//                ],
                'create' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block/actions/create.html.twig',
                ],
            ],
        ],
    ]);
};
