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

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Twig\Environment;

final class CopyToClipboardComponentTest extends KernelTestCase
{
    private Environment $twig;

    protected function setUp(): void
    {
        self::bootKernel();

        $twig = self::getContainer()->get(Environment::class);
        self::assertInstanceOf(Environment::class, $twig);

        $this->twig = $twig;
    }

    public function testItRendersWithTheDefaultConfiguration(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copy_to_clipboard value="customer@example.com" />
            TWIG);

        $controller = $crawler->filter('[data-controller~="copy-to-clipboard"]');
        self::assertCount(1, $controller);
        self::assertSame('customer@example.com', $controller->attr('data-copy-to-clipboard-value-value'));
        self::assertSame('sylius.ui.copied_to_clipboard', $controller->attr('data-copy-to-clipboard-copied-label-value'));
        self::assertSame('sylius.ui.unable_to_copy_to_clipboard', $controller->attr('data-copy-to-clipboard-error-label-value'));

        $button = $controller->filter('button[data-test-copy-to-clipboard]');
        self::assertCount(1, $button);
        self::assertSame('button', $button->attr('type'));
        self::assertSame('copy-to-clipboard#copy', $button->attr('data-action'));
        self::assertSame('sylius.ui.copy_to_clipboard', $button->attr('aria-label'));
        self::assertSame('sylius.ui.copy_to_clipboard', $button->attr('data-bs-title'));
        self::assertSame('tooltip', $button->attr('data-bs-toggle'));
    }

    public function testItSafelyRendersAValueContainingSpecialCharacters(): void
    {
        $value = 'customer+"<script>"@example.com';

        $html = $this->twig->createTemplate(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copy_to_clipboard :value="value" />
            TWIG)->render(['value' => $value]);
        $crawler = new Crawler($html);

        self::assertCount(0, $crawler->filter('script'));
        self::assertStringNotContainsString('<script>', $html);
        self::assertSame(
            $value,
            $crawler->filter('[data-controller~="copy-to-clipboard"]')->attr('data-copy-to-clipboard-value-value'),
        );
    }

    public function testItMergesCustomButtonAttributesWithTheDefaultClasses(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copy_to_clipboard
                value="product-code"
                class="custom-copy-button"
                data-test-copy-product-code
            />
            TWIG);

        $button = $crawler->filter(
            'button.btn.btn-icon.btn-sm.btn-ghost-secondary.custom-copy-button[data-test-copy-product-code]',
        );

        self::assertCount(1, $button);
        self::assertCount(0, $crawler->filter('[data-controller~="copy-to-clipboard"].custom-copy-button'));
    }

    public function testItAcceptsCustomLabels(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copy_to_clipboard
                value="product-code"
                copy_label="Copy product code"
                copied_label="Product code copied"
                error_label="Product code could not be copied"
            />
            TWIG);

        $controller = $crawler->filter('[data-controller~="copy-to-clipboard"]');
        self::assertSame('Product code copied', $controller->attr('data-copy-to-clipboard-copied-label-value'));
        self::assertSame(
            'Product code could not be copied',
            $controller->attr('data-copy-to-clipboard-error-label-value'),
        );

        $button = $controller->filter('button');
        self::assertSame('Copy product code', $button->attr('aria-label'));
        self::assertSame('Copy product code', $button->attr('data-bs-title'));
    }

    public function testItRendersAccessibleFeedbackAndIconTargets(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copy_to_clipboard value="product-code" />
            TWIG);

        self::assertCount(1, $crawler->filter('[data-copy-to-clipboard-target~="copyIcon"]'));
        self::assertCount(1, $crawler->filter('[data-copy-to-clipboard-target~="successIcon"]'));
        self::assertCount(1, $crawler->filter('[data-copy-to-clipboard-target~="errorIcon"]'));
        self::assertCount(
            1,
            $crawler->filter(
                '.visually-hidden[aria-live="polite"][data-copy-to-clipboard-target~="status"]',
            ),
        );
        self::assertCount(1, $crawler->filter('button[aria-label="sylius.ui.copy_to_clipboard"]'));
        self::assertStringNotContainsString('Copy to clipboard', $crawler->html());
        self::assertStringNotContainsString('Copied to clipboard', $crawler->html());
        self::assertStringNotContainsString('Unable to copy to clipboard', $crawler->html());
    }

    private function renderComponent(string $template): Crawler
    {
        return new Crawler($this->twig->createTemplate($template)->render());
    }
}
