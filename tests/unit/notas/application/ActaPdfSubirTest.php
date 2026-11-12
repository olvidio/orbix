<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\ActaPdfSubir;
use src\notas\application\DatosActa;
use src\notas\application\support\ActaContenidoImpreso;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;

final class ActaPdfSubirTest extends TestCase
{
    public function test_sin_acta(): void
    {
        $uc = new ActaPdfSubir(
            $this->createMock(ActaRepositoryInterface::class),
            $this->contenidoVacio(),
        );
        $r = $uc->execute([], []);
        $this->assertNotSame('', $r['error']);
    }

    public function test_rechaza_si_no_se_ha_impreso_despues_del_cambio(): void
    {
        $acta = new Acta();
        $acta->setActa('dlb 1/26');
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->with('dlb 1/26')->willReturn($acta);

        $uc = new ActaPdfSubir($repo, $this->contenidoVacio());
        $r = $uc->execute(['acta_num' => 'dlb 1/26'], [
            'acta_pdf' => ['error' => UPLOAD_ERR_OK, 'tmp_name' => '/tmp/x', 'name' => 'a.pdf'],
        ]);
        $this->assertNotSame('', $r['error']);
        $this->assertStringContainsString('imprimir', mb_strtolower($r['error']));
    }

    public function test_permite_si_la_impresion_coincide(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'acta');
        $this->assertIsString($tmp);
        file_put_contents($tmp, '%PDF-1.4 test');

        $acta = new Acta();
        $acta->setActa('dlb 1/26');
        $contenido = $this->contenidoVacio();
        $acta->setHash_impreso($contenido->hashDeActa($acta));

        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturn($acta);
        $repo->expects($this->once())->method('Guardar')->with($acta)->willReturn(true);

        $uc = new ActaPdfSubir($repo, $contenido);
        $r = $uc->execute(['acta_num' => 'dlb 1/26'], [
            'acta_pdf' => ['error' => UPLOAD_ERR_OK, 'tmp_name' => $tmp, 'name' => 'a.pdf'],
        ]);
        @unlink($tmp);
        $this->assertSame('', $r['error']);
        $this->assertTrue($acta->tienePdfFirmado());
    }

    /**
     * Acta impresa sin cambios desde entonces: el repositorio no debe guardar si `$guarda` es `null`.
     *
     * @param array<string, array<string, mixed>> $files
     * @return array{error: string, http_status: int}
     */
    private function subir(array $files, ?bool $guarda = null, string $errorRepo = ''): array
    {
        $acta = new Acta();
        $acta->setActa('dlb 1/26');
        $contenido = $this->contenidoVacio();
        $acta->setHash_impreso($contenido->hashDeActa($acta));
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturn($acta);
        if ($guarda === null) {
            $repo->expects($this->never())->method('Guardar');
        } else {
            $repo->expects($this->once())->method('Guardar')->willReturn($guarda);
            $repo->method('getErrorTxt')->willReturn($errorRepo);
        }

        return (new ActaPdfSubir($repo, $contenido))->execute(['acta_num' => 'dlb 1/26'], $files);
    }

    public function test_acta_inexistente(): void
    {
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->never())->method('Guardar');

        $r = (new ActaPdfSubir($repo, $this->contenidoVacio()))->execute(['acta_num' => 'dlb 99/26'], []);

        $this->assertSame('No se encuentra el acta', $r['error']);
    }

    /**
     * Comportamiento actual (anotado en el plan): sin fichero, o con `UPLOAD_ERR_NO_FILE`, responde éxito sin
     * guardar nada.
     */
    public function test_sin_fichero_responde_exito_sin_guardar(): void
    {
        $this->assertSame(['error' => '', 'http_status' => 200], $this->subir([]));
        $this->assertSame(['error' => '', 'http_status' => 200], $this->subir(['acta_pdf' => ['error' => UPLOAD_ERR_NO_FILE]]));
    }

    public function test_fichero_demasiado_grande_responde_413(): void
    {
        $r = $this->subir(['acta_pdf' => ['error' => UPLOAD_ERR_INI_SIZE, 'name' => 'grande.pdf']]);

        $this->assertSame(413, $r['http_status']);
        $this->assertStringContainsString('tamaño máximo', $r['error']);
    }

    public function test_otros_errores_de_subida(): void
    {
        $r = $this->subir(['acta_pdf' => ['error' => UPLOAD_ERR_PARTIAL, 'name' => 'a.pdf']]);
        $this->assertNotSame('', $r['error']);
        $this->assertNotSame(413, $r['http_status']);

        $this->assertSame('No se puede subir el archivo a.pdf', $this->subir(['acta_pdf' => ['name' => 'a.pdf']])['error'], 'sin tmp_name');
    }

    public function test_fichero_vacio_no_se_guarda(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'acta');
        $this->assertIsString($tmp);
        try {
            $r = $this->subir(['acta_pdf' => ['error' => UPLOAD_ERR_OK, 'tmp_name' => $tmp, 'name' => 'vacio.pdf']]);
        } finally {
            @unlink($tmp);
        }

        $this->assertSame('No se puede leer el archivo vacio.pdf', $r['error']);
    }

    public function test_error_al_guardar_devuelve_el_del_repositorio(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'acta');
        $this->assertIsString($tmp);
        file_put_contents($tmp, '%PDF-1.4');
        try {
            $r = $this->subir(['acta_pdf' => ['error' => UPLOAD_ERR_OK, 'tmp_name' => $tmp, 'name' => 'a.pdf']], false, 'sin conexión');
        } finally {
            @unlink($tmp);
        }

        $this->assertSame('sin conexión', $r['error']);
    }

    private function contenidoVacio(): ActaContenidoImpreso
    {
        $tribunalRepo = $this->createMock(ActaTribunalRepositoryInterface::class);
        $tribunalRepo->method('matching')->willReturn([]);
        $notaRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('matching')->willReturn([]);

        return new ActaContenidoImpreso($tribunalRepo, new DatosActa($notaRepo));
    }
}
