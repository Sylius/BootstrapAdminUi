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

use PHPUnit\Framework\Attributes\DataProvider;
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
        self::assertSame('sylius.ui.copy_to_clipboard', $button->attr('title'));
        self::assertSame('button', $button->attr('data-copy-to-clipboard-target'));
        self::assertNull($button->attr('data-bs-toggle'));
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
        self::assertSame('Copy product code', $button->attr('title'));
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
                '.visually-hidden[role="status"][aria-live="polite"][aria-atomic="true"][data-copy-to-clipboard-target~="status"]',
            ),
        );
        self::assertCount(1, $crawler->filter('button[aria-label="sylius.ui.copy_to_clipboard"]'));
        self::assertStringNotContainsString('Copy to clipboard', $crawler->html());
        self::assertStringNotContainsString('Copied to clipboard', $crawler->html());
        self::assertStringNotContainsString('Unable to copy to clipboard', $crawler->html());
    }

    public function testItRendersThroughTwigHooks(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            {% hook 'app.copy_to_clipboard' with { value: 'ABC-123' } %}
            TWIG);

        self::assertCount(1, $crawler->filter('button[data-test-copy-to-clipboard]'));
        self::assertSame('ABC-123', $crawler->filter('[data-controller]')->attr('data-copy-to-clipboard-value-value'));
        self::assertCount(0, $crawler->filter('[hookableMetadata]'));
    }

    #[DataProvider('provideValues')]
    public function testItDisablesOnlyMissingOrEmptyValues(?string $value, bool $disabled): void
    {
        $crawler = new Crawler($this->twig->createTemplate(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copy_to_clipboard :value="value" />
            TWIG)->render(['value' => $value]));

        self::assertCount($disabled ? 1 : 0, $crawler->filter('button[disabled]'));
        self::assertSame($value, $crawler->filter('[data-controller]')->attr('data-copy-to-clipboard-value-value'));
    }

    public static function provideValues(): iterable
    {
        yield 'null' => [null, true];
        yield 'empty string' => ['', true];
        yield 'zero' => ['0', false];
        yield 'leading zeros' => ['000123', false];
        yield 'unicode and newlines' => ["Référence 日本語\nABC-123", false];
    }

    public function testItAcceptsAnIntegerZero(): void
    {
        $crawler = $this->renderComponent('<twig:sylius_bootstrap_admin_ui:copy_to_clipboard :value="0" />');

        self::assertCount(0, $crawler->filter('button[disabled]'));
        self::assertSame('0', $crawler->filter('[data-controller]')->attr('data-copy-to-clipboard-value-value'));
    }

    public function testItCanBeExplicitlyDisabled(): void
    {
        $crawler = $this->renderComponent('<twig:sylius_bootstrap_admin_ui:copy_to_clipboard value="ABC" disabled />');

        self::assertCount(1, $crawler->filter('button[disabled]'));
    }

    public function testItDisablesCopyingWhenNoValueIsProvided(): void
    {
        $crawler = $this->renderComponent('<twig:sylius_bootstrap_admin_ui:copy_to_clipboard />');

        self::assertCount(1, $crawler->filter('button[disabled]'));
    }

    public function testItCannotEnableCopyingAnAbsentValueWithTheDisabledProp(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copy_to_clipboard :value="null" :disabled="false" />
            TWIG);

        self::assertCount(1, $crawler->filter('button[disabled]'));
    }

    public function testItCanReplaceButtonClassesAndComposeActions(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copy_to_clipboard
                value="ABC"
                button_class="btn btn-outline-secondary"
                class="custom"
                data-action="click->analytics#track"
            />
            TWIG);

        $button = $crawler->filter('button');
        self::assertSame('btn btn-outline-secondary custom', $button->attr('class'));
        self::assertSame('copy-to-clipboard#copy click->analytics#track', $button->attr('data-action'));
    }

    private function renderComponent(string $template): Crawler
    {
        return new Crawler($this->twig->createTemplate($template)->render());
    }
}
