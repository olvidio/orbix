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
}
