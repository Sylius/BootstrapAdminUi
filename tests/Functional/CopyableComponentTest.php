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

final class CopyableComponentTest extends KernelTestCase
{
    public function testItDisplaysAndCopiesTheValueByDefault(): void
    {
        $crawler = $this->renderComponent('<twig:sylius_bootstrap_admin_ui:copyable value="000123" />');

        self::assertSame('000123', $crawler->filter('.d-inline-flex')->text());
        self::assertCount(1, $crawler->filter('button[data-test-copy-to-clipboard]'));
        self::assertSame('000123', $crawler->filter('[data-controller]')->attr('data-copy-to-clipboard-value-value'));
    }

    public function testItDisplaysAndCopiesAnIntegerZero(): void
    {
        $crawler = $this->renderComponent('<twig:sylius_bootstrap_admin_ui:copyable :value="0" />');

        self::assertSame('0', $crawler->filter('.d-inline-flex')->text());
        self::assertCount(0, $crawler->filter('button[disabled]'));
        self::assertSame('0', $crawler->filter('[data-controller]')->attr('data-copy-to-clipboard-value-value'));
    }

    public function testItKeepsCustomContentSeparateFromTheCopyButtonAndValue(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copyable value="customer@example.com">
                <a href="mailto:customer@example.com"><span class="badge">Customer email</span></a>
            </twig:sylius_bootstrap_admin_ui:copyable>
            TWIG);

        self::assertSame('Customer email', $crawler->filter('a')->text());
        self::assertSame('mailto:customer@example.com', $crawler->filter('a')->attr('href'));
        self::assertCount(0, $crawler->filter('a button, button a'));
        self::assertSame('customer@example.com', $crawler->filter('[data-controller]')->attr('data-copy-to-clipboard-value-value'));
    }

    public function testItSeparatesContainerAndButtonAttributes(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copyable
                value="ABC"
                class="custom-container"
                button:class="custom-button"
                button:data-test-copy-code
                button_class="btn btn-outline-secondary"
                copy_label="Copy code"
                copied_label="Code copied"
                error_label="Copy failed"
                disabled
            />
            TWIG);

        self::assertCount(1, $crawler->filter('.d-inline-flex.custom-container'));
        self::assertCount(0, $crawler->filter('button.custom-container'));
        $button = $crawler->filter('button.custom-button[data-test-copy-code][disabled]');
        self::assertCount(1, $button);
        self::assertSame('btn btn-outline-secondary custom-button', $button->attr('class'));
        self::assertSame('Copy code', $button->attr('aria-label'));
        $controller = $crawler->filter('[data-controller]');
        self::assertSame('Code copied', $controller->attr('data-copy-to-clipboard-copied-label-value'));
        self::assertSame('Copy failed', $controller->attr('data-copy-to-clipboard-error-label-value'));
    }

    public function testItEscapesTheDisplayedValue(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            {% set value = '<script>alert("test")</script>' %}
            <twig:sylius_bootstrap_admin_ui:copyable :value="value" />
            TWIG);

        self::assertCount(0, $crawler->filter('script'));
        self::assertSame('<script>alert("test")</script>', $crawler->filter('.d-inline-flex')->text());
    }

    public function testItRendersThroughTwigHooks(): void
    {
        $crawler = $this->renderComponent("{% hook 'app.copyable' with { value: 'ABC-123' } %}");

        self::assertSame('ABC-123', $crawler->filter('.d-inline-flex')->text());
        self::assertCount(1, $crawler->filter('button'));
        self::assertCount(0, $crawler->filter('[hookableMetadata]'));
    }

    public function testItDisablesCopyingAnAbsentValueButPreservesCustomContent(): void
    {
        $crawler = $this->renderComponent(<<<'TWIG'
            <twig:sylius_bootstrap_admin_ui:copyable :value="null">
                <span>Not available</span>
            </twig:sylius_bootstrap_admin_ui:copyable>
            TWIG);

        self::assertSame('Not available', $crawler->filter('.d-inline-flex')->text());
        self::assertCount(1, $crawler->filter('button[disabled]'));
    }

    private function renderComponent(string $template): Crawler
    {
        self::bootKernel();
        $twig = self::getContainer()->get(Environment::class);
        self::assertInstanceOf(Environment::class, $twig);

        return new Crawler($twig->createTemplate($template)->render());
    }
}
