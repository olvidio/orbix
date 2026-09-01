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

    private function contenidoVacio(): ActaContenidoImpreso
    {
        $tribunalRepo = $this->createMock(ActaTribunalRepositoryInterface::class);
        $tribunalRepo->method('getActasTribunales')->willReturn([]);
        $notaRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('getPersonaNotas')->willReturn([]);

        return new ActaContenidoImpreso($tribunalRepo, new DatosActa($notaRepo));
    }
}
