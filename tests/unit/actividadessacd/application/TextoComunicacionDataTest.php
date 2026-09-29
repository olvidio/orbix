<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\TextoComunicacionData;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;
use src\actividadessacd\domain\value_objects\SacdTextoTexto;
use src\shared\security\HashB;

/**
 * Unitarios del use case {@see TextoComunicacionData}: helpers de
 * normalizacion de idioma y la lectura del repo de textos. Todas las
 * dependencias se inyectan via mock del contenedor DI.
 */
final class TextoComunicacionDataTest extends TestCase
{

    public function test_sin_clave_devuelve_texto_vacio(): void {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->expects($this->never())->method('getActividadSacdTextos');

        $out = (new \src\actividadessacd\application\TextoComunicacionData($repo))->execute(['clave' => '', 'idioma' => 'ca']);
        $this->assertSame(['texto' => ''], $out);
    }

    public function test_sin_idioma_devuelve_texto_vacio(): void {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->expects($this->never())->method('getActividadSacdTextos');

        $out = (new \src\actividadessacd\application\TextoComunicacionData($repo))->execute(['clave' => 'com_sacd', 'idioma' => '']);
        $this->assertSame(['texto' => ''], $out);
    }

    public function test_texto_inexistente_devuelve_cadena_vacia(): void {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')
            ->with(['clave' => 'com_sacd', 'idioma' => 'es_ES.UTF-8'])
            ->willReturn([]);

        $out = (new \src\actividadessacd\application\TextoComunicacionData($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'es_ES.UTF-8',
        ]);
        $this->assertSame('', $out['texto']);
        $this->assertSame(
            ['clave' => 'com_sacd', 'idioma' => 'es_ES.UTF-8'],
            HashB::open($out['ctx_guardar'], 'texto_comunicacion_guardar')
        );
    }

    public function test_texto_existente_se_devuelve_tal_cual(): void {
        $oTexto = new ActividadSacdTexto();
        $oTexto->setId_item(1);
        $oTexto->setTextoVo(new SacdTextoTexto('hola sacd'));

        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')
            ->with(['clave' => 'com_sacd', 'idioma' => 'ca_ES.UTF-8'])
            ->willReturn([$oTexto]);

        $out = (new \src\actividadessacd\application\TextoComunicacionData($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca_ES.UTF-8',
        ]);
        $this->assertSame('hola sacd', $out['texto']);
        $this->assertSame(
            ['clave' => 'com_sacd', 'idioma' => 'ca_ES.UTF-8'],
            HashB::open($out['ctx_guardar'], 'texto_comunicacion_guardar')
        );
    }

    public function test_repo_devuelve_lista_vacia_se_trata_como_vacio(): void {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')->willReturn([]);

        $out = (new \src\actividadessacd\application\TextoComunicacionData($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca',
        ]);
        $this->assertSame('', $out['texto']);
        $this->assertSame(
            ['clave' => 'com_sacd', 'idioma' => 'ca'],
            HashB::open($out['ctx_guardar'], 'texto_comunicacion_guardar')
        );
    }
}
