<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\TextoComunicacionData;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;
use src\actividadessacd\domain\value_objects\SacdTextoTexto;

/**
 * Unitarios del use case {@see TextoComunicacionData}: helpers de
 * normalizacion de idioma y la lectura del repo de textos. Todas las
 * dependencias se inyectan via mock del contenedor DI.
 */
final class TextoComunicacionDataTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_normalizar_idioma_sin_underscore_devuelve_igual(): void
    {
        $this->assertSame('ca', TextoComunicacionData::normalizarIdioma('ca'));
        $this->assertSame('es', TextoComunicacionData::normalizarIdioma('es'));
    }

    public function test_normalizar_idioma_con_locale_completo_corta_en_underscore(): void
    {
        $this->assertSame('ca', TextoComunicacionData::normalizarIdioma('ca_ES.UTF-8'));
        $this->assertSame('es', TextoComunicacionData::normalizarIdioma('es_ES'));
    }

    public function test_normalizar_idioma_cadena_vacia_devuelve_cadena_vacia(): void
    {
        $this->assertSame('', TextoComunicacionData::normalizarIdioma(''));
    }

    public function test_sin_clave_devuelve_texto_vacio(): void
    {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->expects($this->never())->method('getActividadSacdTextos');

        $GLOBALS['container'] = $this->containerOne(
            ActividadSacdTextoRepositoryInterface::class,
            $repo
        );

        $out = TextoComunicacionData::execute(['clave' => '', 'idioma' => 'ca']);
        $this->assertSame(['texto' => ''], $out);
    }

    public function test_sin_idioma_devuelve_texto_vacio(): void
    {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->expects($this->never())->method('getActividadSacdTextos');

        $GLOBALS['container'] = $this->containerOne(
            ActividadSacdTextoRepositoryInterface::class,
            $repo
        );

        $out = TextoComunicacionData::execute(['clave' => 'com_sacd', 'idioma' => '']);
        $this->assertSame(['texto' => ''], $out);
    }

    public function test_texto_inexistente_devuelve_cadena_vacia(): void
    {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')
            ->with(['clave' => 'com_sacd', 'idioma' => 'es'])
            ->willReturn([]);

        $GLOBALS['container'] = $this->containerOne(
            ActividadSacdTextoRepositoryInterface::class,
            $repo
        );

        $out = TextoComunicacionData::execute([
            'clave' => 'com_sacd',
            'idioma' => 'es_ES.UTF-8',
        ]);
        $this->assertSame(['texto' => ''], $out);
    }

    public function test_texto_existente_se_devuelve_tal_cual(): void
    {
        $oTexto = new ActividadSacdTexto();
        $oTexto->setId_item(1);
        $oTexto->setTextoVo(new SacdTextoTexto('hola sacd'));

        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')
            ->with(['clave' => 'com_sacd', 'idioma' => 'ca'])
            ->willReturn([$oTexto]);

        $GLOBALS['container'] = $this->containerOne(
            ActividadSacdTextoRepositoryInterface::class,
            $repo
        );

        $out = TextoComunicacionData::execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca_ES.UTF-8',
        ]);
        $this->assertSame(['texto' => 'hola sacd'], $out);
    }

    public function test_repo_devuelve_false_se_trata_como_vacio(): void
    {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')->willReturn(false);

        $GLOBALS['container'] = $this->containerOne(
            ActividadSacdTextoRepositoryInterface::class,
            $repo
        );

        $out = TextoComunicacionData::execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca',
        ]);
        $this->assertSame(['texto' => ''], $out);
    }

    /**
     * @param class-string $iface
     */
    private function containerOne(string $iface, object $service): object
    {
        return new class($iface, $service) {
            public function __construct(
                private readonly string $iface,
                private readonly object $service
            ) {}

            public function get(string $id): object
            {
                if ($id !== $this->iface) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->service;
            }
        };
    }
}
