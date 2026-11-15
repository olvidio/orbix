<?php

declare(strict_types=1);

namespace Tests\unit\shared\config;

use src\shared\config\ConfigGlobal;
use Tests\myTest;

/**
 * El servidor con el que una instancia construye sus URLs: el de `ServerConf`, salvo que la instancia
 * (la de pruebas, sobre el mismo código) indique otro con `ORBIX_WEB_SERVER`.
 */
final class ConfigGlobalWebServerTest extends myTest
{
    private string|false $anterior = false;

    public function setUp(): void
    {
        parent::setUp();
        $this->anterior = getenv('ORBIX_WEB_SERVER');
    }

    public function tearDown(): void
    {
        $this->anterior === false ? putenv('ORBIX_WEB_SERVER') : putenv('ORBIX_WEB_SERVER=' . $this->anterior);
        parent::tearDown();
    }

    public function test_sin_variable_es_el_servidor_configurado(): void
    {
        putenv('ORBIX_WEB_SERVER');

        $this->assertSame(ConfigGlobal::$web_server, ConfigGlobal::webServer());
        $this->assertStringStartsWith(ConfigGlobal::$web_server, ConfigGlobal::getWeb());
    }

    public function test_con_variable_las_urls_salen_con_ese_servidor(): void
    {
        putenv('ORBIX_WEB_SERVER=http://orbix.local/');

        $this->assertSame('http://orbix.local', ConfigGlobal::webServer(), 'sin la barra final');
        $this->assertStringStartsWith('http://orbix.local' . ConfigGlobal::getWebPort(), ConfigGlobal::getWeb());
    }

    public function test_una_variable_vacia_no_cuenta(): void
    {
        putenv('ORBIX_WEB_SERVER=');

        $this->assertSame(ConfigGlobal::$web_server, ConfigGlobal::webServer());
    }
}
