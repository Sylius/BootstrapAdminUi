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

namespace Tests\Sylius\BootstrapAdminUi\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TemplatesTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testIndexTemplate(): void
    {
        $this->client->request('GET', '/books');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('title', 'app.ui.books | Sylius');
        self::assertSelectorTextContains('tr.item:first-child[data-test-resource-id]', 'Shinning');
        self::assertSelectorTextContains('tr.item:last-child[data-test-resource-id]', 'Carrie');
    }
}
