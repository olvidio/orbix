<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\TextoComunicacionGuardar;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;
use src\actividadessacd\domain\value_objects\SacdTextoTexto;

/**
 * Unitarios del use case {@see TextoComunicacionGuardar}: mockea el repo
 * de textos y cubre los 4 caminos (crear, actualizar, eliminar cuando el
 * texto esta vacio y la fila existe, no hacer nada cuando no existe y el
 * texto esta vacio) mas las validaciones de entrada y propagacion de
 * errores desde `Guardar` / `Eliminar`.
 */
final class TextoComunicacionGuardarTest extends TestCase
{
        public function test_sin_clave_devuelve_error(): void {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->expects($this->never())->method('getActividadSacdTextos');

        $out = (new \src\actividadessacd\application\TextoComunicacionGuardar($repo))->execute([
            'clave' => '',
            'idioma' => 'ca',
            'texto' => 'hola',
        ]);
        $this->assertStringContainsString('faltan parametros', $out);
    }

    public function test_sin_idioma_devuelve_error(): void {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->expects($this->never())->method('getActividadSacdTextos');

        $out = (new \src\actividadessacd\application\TextoComunicacionGuardar($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => '',
            'texto' => 'hola',
        ]);
        $this->assertStringContainsString('faltan parametros', $out);
    }

    public function test_texto_vacio_sin_fila_existente_no_crea_nada(): void {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')->willReturn([]);
        $repo->expects($this->never())->method('Guardar');
        $repo->expects($this->never())->method('Eliminar');

        $out = (new \src\actividadessacd\application\TextoComunicacionGuardar($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca_ES.UTF-8',
            'texto' => '',
        ]);
        $this->assertSame('', $out);
    }

    public function test_texto_vacio_con_fila_existente_elimina(): void {
        $oTexto = new ActividadSacdTexto();
        $oTexto->setId_item(77);

        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')->willReturn([$oTexto]);
        $repo->expects($this->once())
            ->method('Eliminar')
            ->with($oTexto)
            ->willReturn(true);
        $repo->expects($this->never())->method('Guardar');

        $out = (new \src\actividadessacd\application\TextoComunicacionGuardar($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca',
            'texto' => '',
        ]);
        $this->assertSame('', $out);
    }

    public function test_texto_vacio_con_fila_existente_error_si_eliminar_falla(): void {
        $oTexto = new ActividadSacdTexto();
        $oTexto->setId_item(77);

        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')->willReturn([$oTexto]);
        $repo->method('Eliminar')->willReturn(false);

        $out = (new \src\actividadessacd\application\TextoComunicacionGuardar($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca',
            'texto' => '',
        ]);
        $this->assertStringContainsString('no se ha eliminado el texto', $out);
    }

    public function test_fila_existente_con_texto_actualiza(): void {
        $oTexto = new ActividadSacdTexto();
        $oTexto->setId_item(77);
        $oTexto->setTextoVo(new SacdTextoTexto('viejo'));

        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')->willReturn([$oTexto]);
        $repo->expects($this->never())->method('Eliminar');
        $repo->expects($this->once())
            ->method('Guardar')
            ->with($this->callback(function (ActividadSacdTexto $entrante) use ($oTexto) {
                return $entrante === $oTexto
                    && $entrante->getTextoVo() !== null
                    && $entrante->getTextoVo()->value() === 'nuevo';
            }))
            ->willReturn(true);

        $out = (new \src\actividadessacd\application\TextoComunicacionGuardar($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca',
            'texto' => 'nuevo',
        ]);
        $this->assertSame('', $out);
    }

    public function test_fila_existente_con_texto_error_si_guardar_falla(): void {
        $oTexto = new ActividadSacdTexto();
        $oTexto->setId_item(77);

        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')->willReturn([$oTexto]);
        $repo->method('Guardar')->willReturn(false);

        $out = (new \src\actividadessacd\application\TextoComunicacionGuardar($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca',
            'texto' => 'nuevo',
        ]);
        $this->assertStringContainsString('no se ha guardado el texto', $out);
    }

    public function test_sin_fila_con_texto_crea_entidad_y_guarda(): void {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')->willReturn([]);
        $repo->method('getNewId')->willReturn(4242);

        $guardada = null;
        $repo->expects($this->once())
            ->method('Guardar')
            ->with($this->callback(function (ActividadSacdTexto $entrante) use (&$guardada) {
                $guardada = $entrante;
                return true;
            }))
            ->willReturn(true);

        $out = (new \src\actividadessacd\application\TextoComunicacionGuardar($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'es_ES.UTF-8',
            'texto' => 'hola sacd',
        ]);
        $this->assertSame('', $out);
        $this->assertNotNull($guardada);
        $this->assertSame(4242, $guardada->getId_item());
        $this->assertSame('com_sacd', $guardada->getClave());
        $this->assertSame('es', $guardada->getIdioma());
        $this->assertNotNull($guardada->getTextoVo());
        $this->assertSame('hola sacd', $guardada->getTextoVo()->value());
    }

    public function test_sin_fila_con_texto_error_si_guardar_falla(): void {
        $repo = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $repo->method('getActividadSacdTextos')->willReturn([]);
        $repo->method('getNewId')->willReturn(1);
        $repo->method('Guardar')->willReturn(false);

        $out = (new \src\actividadessacd\application\TextoComunicacionGuardar($repo))->execute([
            'clave' => 'com_sacd',
            'idioma' => 'ca',
            'texto' => 'algo',
        ]);
        $this->assertStringContainsString('no se ha guardado el texto', $out);
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
