<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\DatosActa;
use src\notas\application\support\ActaContenidoImpreso;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\ActaTribunal;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaSituacion;
use src\shared\domain\value_objects\DateTimeLocal;

final class ActaContenidoImpresoTest extends TestCase
{
    public function test_hash_estable_y_cambia_si_cambia_una_nota(): void
    {
        $acta = $this->actaBase();
        $servicio = $this->servicioConNotas([
            $this->nota(10, '9/10'),
            $this->nota(3, '7/10'),
        ]);

        $hash1 = $servicio->hashDeActa($acta);
        $hash2 = $servicio->hashDeActa($acta);
        $this->assertSame($hash1, $hash2);
        $this->assertSame(64, strlen($hash1));

        $servicio2 = $this->servicioConNotas([
            $this->nota(10, '8/10'),
            $this->nota(3, '7/10'),
        ]);
        $this->assertNotSame($hash1, $servicio2->hashDeActa($acta));
    }

    public function test_coincide_con_impreso(): void
    {
        $acta = $this->actaBase();
        $servicio = $this->servicioConNotas([]);
        $this->assertFalse($servicio->coincideConImpreso($acta));

        $hash = $servicio->hashDeActa($acta);
        $acta->setHash_impreso($hash);
        $this->assertTrue($servicio->coincideConImpreso($acta));
    }

    private function actaBase(): Acta
    {
        $acta = new Acta();
        $acta->setActa('dlb 1/26');
        $acta->setId_asignatura(1101);
        $acta->setF_acta(new DateTimeLocal('2026-01-15'));
        $acta->setLibro(1);
        $acta->setPagina(2);
        $acta->setLinea(3);
        $acta->setLugar('Pamplona');
        $acta->setObserv('');

        return $acta;
    }

    /**
     * @param list<PersonaNota> $notas
     */
    private function servicioConNotas(array $notas): ActaContenidoImpreso
    {
        $ex = new ActaTribunal();
        $ex->setExaminador('Examinador Uno');
        $tribunalRepo = $this->createMock(ActaTribunalRepositoryInterface::class);
        $tribunalRepo->method('getActasTribunales')->willReturn([$ex]);

        $notaRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('getPersonaNotas')->willReturn($notas);

        return new ActaContenidoImpreso($tribunalRepo, new DatosActa($notaRepo));
    }

    private function nota(int $idNom, string $txt): PersonaNota
    {
        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getId_nom')->willReturn($idNom);
        $nota->method('getNota_txt')->willReturn($txt);
        $nota->method('getNota_num')->willReturn($txt);
        $nota->method('getId_situacion')->willReturn(NotaSituacion::NUMERICA);

        return $nota;
    }
}
