<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\ActaMarcarImpresa;
use src\notas\application\DatosActa;
use src\notas\application\support\ActaContenidoImpreso;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;

final class ActaMarcarImpresaTest extends TestCase
{
    public function test_sin_acta(): void
    {
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $uc = new ActaMarcarImpresa($repo, $this->contenidoVacio(), new ActaFirmadaPolicy($repo));
        $this->assertNotSame('', $uc->execute(''));
    }

    public function test_no_toca_acta_ya_firmada(): void
    {
        $acta = new Acta();
        $acta->setActa('dlb 1/26');
        $acta->setPdf('%PDF-fake');
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturn($acta);
        $repo->expects($this->never())->method('Guardar');

        $uc = new ActaMarcarImpresa($repo, $this->contenidoVacio(), new ActaFirmadaPolicy($repo));
        $this->assertSame('', $uc->execute('dlb 1/26'));
        $this->assertNull($acta->getHash_impreso());
    }

    public function test_guarda_hash_al_imprimir(): void
    {
        $acta = new Acta();
        $acta->setActa('dlb 1/26');
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturn($acta);
        $repo->expects($this->once())->method('Guardar')->with($acta)->willReturn(true);

        $contenido = $this->contenidoVacio();
        $uc = new ActaMarcarImpresa($repo, $contenido, new ActaFirmadaPolicy($repo));
        $this->assertSame('', $uc->execute('dlb 1/26'));
        $this->assertSame($contenido->hashDeActa($acta), $acta->getHash_impreso());
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
