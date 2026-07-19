<?php

declare(strict_types=1);

namespace Tests\unit\frontend;

use frontend\shared\config\AppUrlConfig;
use PHPUnit\Framework\TestCase;

final class AppUrlConfigSrcBrowserUrlTest extends TestCase
{
    public function test_src_browser_url_uses_physical_proxy_and_path_info(): void
    {
        $url = AppUrlConfig::srcBrowserUrl('/src/actividades/actividad_tipo_get');
        self::assertStringContainsString('/frontend/shared/controller/src_ajax.php/src/actividades/actividad_tipo_get', $url);
        self::assertStringNotContainsString('?', $url);
    }

    public function test_src_browser_url_rejects_query_in_path(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AppUrlConfig::srcBrowserUrl('/src/foo?bar=1');
    }

    public function test_src_browser_url_rejects_non_src_path(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AppUrlConfig::srcBrowserUrl('/frontend/foo.php');
    }

    public function test_browser_url_from_app_relative_routes_src_via_proxy(): void
    {
        $url = AppUrlConfig::browserUrlFromAppRelative('src/cambios/cambio_usuario_eliminar');
        self::assertStringContainsString('/frontend/shared/controller/src_ajax.php/src/cambios/cambio_usuario_eliminar', $url);
    }

    public function test_browser_url_from_app_relative_keeps_frontend_path(): void
    {
        $url = AppUrlConfig::browserUrlFromAppRelative('frontend/asistentes/controller/form_asistentes_a_una_actividad.php');
        self::assertStringContainsString('/frontend/asistentes/controller/form_asistentes_a_una_actividad.php', $url);
        self::assertStringNotContainsString('src_ajax.php', $url);
    }

    public function test_browser_url_from_app_relative_empty(): void
    {
        self::assertSame('', AppUrlConfig::browserUrlFromAppRelative(''));
        self::assertSame('', AppUrlConfig::browserUrlFromAppRelative('/'));
    }
}
