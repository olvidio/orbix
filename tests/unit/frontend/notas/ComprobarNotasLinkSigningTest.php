<?php

declare(strict_types=1);

namespace Tests\unit\frontend\notas;

use frontend\notas\helpers\ComprobarNotasLinkSigning;
use PHPUnit\Framework\TestCase;

final class ComprobarNotasLinkSigningTest extends TestCase
{
    private string|false $previousPublicBase;

    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('comprobar-notas-link-signing-test');
        $this->previousPublicBase = getenv('ORBIX_PUBLIC_APP_BASE_URL');
        putenv('ORBIX_PUBLIC_APP_BASE_URL=https://orbix.test/app');
    }

    protected function tearDown(): void
    {
        if ($this->previousPublicBase === false) {
            putenv('ORBIX_PUBLIC_APP_BASE_URL');
        } else {
            putenv('ORBIX_PUBLIC_APP_BASE_URL=' . $this->previousPublicBase);
        }
        parent::tearDown();
    }

    public function test_sign_html_replaces_backend_link_spec_tokens(): void
    {
        $html = ComprobarNotasLinkSigning::signHtml(
            '<span onclick="fnjs_update_div(\'#main\',\'__LINK__\')">ir</span>',
            [
                '__LINK__' => [
                    'path' => 'frontend/notas/controller/comprobar_notas.php',
                    'query' => ['id_tabla' => 'n', 'actualizar' => '9999'],
                ],
            ]
        );

        $this->assertStringNotContainsString('__LINK__', $html);
        $this->assertStringContainsString(
            '/app/frontend/notas/controller/comprobar_notas.php?',
            $html
        );
        $this->assertStringContainsString('id_tabla=n', $html);
        $this->assertStringContainsString('actualizar=9999', $html);
        $this->assertStringContainsString('h=', $html);
    }

    public function test_sign_html_ignores_malformed_specs(): void
    {
        $this->assertSame(
            '<p></p>',
            ComprobarNotasLinkSigning::signHtml('<p></p>', ['__LINK__' => ['query' => []]])
        );
    }
}
