<?php

declare(strict_types=1);

namespace Tests\unit\personas\domain;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\entity\Traslado;
use src\personas\domain\Trasladar;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\myTest;

/**
 * Pasos del traslado de dl con repositorios simulados: la fábrica de
 * repositorios por conexión distingue origen (H-dlbv) y destino (H-dlpv) por
 * el esquema de la conexión real que abre Trasladar.
 */
final class TrasladarPasosTest extends myTest
{
    private ActividadAllRepositoryInterface&MockObject $actividades;
    /** @var array<string, object> «Interfaz@esquema» => doble */
    private array $repos = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
    }

    private function repo(string $interfaz, string $esquema): MockObject
    {
        return $this->repos["$interfaz@$esquema"] ??= $this->createMock($interfaz);
    }

    private function trasladar(): Trasladar
    {
        $fabrica = $this->createMock(ConnectionRepositoryFactoryInterface::class);
        $fabrica->method('createWithConnection')->willReturnCallback(function (string $id, \PDO $db): object {
            $esquema = (string) $db->query('select current_schema()')->fetchColumn();
            return $this->repo($id, $esquema);
        });
        $t = new Trasladar(
            $this->createMock(DelegacionRepositoryInterface::class),
            $this->createMock(AsignaturaRepositoryInterface::class),
            $this->actividades,
            $this->createMock(TipoDossierRepositoryInterface::class),
            $this->createMock(CertificadoEmitidoRepositoryInterface::class),
            $fabrica,
        );
        $t->setId_nom(100123);
        $t->setReg_dl_org('H-dlbv');
        $t->setReg_dl_dst('H-dlpv');
        return $t;
    }

    private function asistencia(int $idActiv): Asistente
    {
        $a = new Asistente();
        $a->setId_activ($idActiv);
        $a->setId_nom(100123);
        $a->setDl_responsable('dlb');
        return $a;
    }

    private function actividad(int $id, string $dl): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setDl_org($dl);
        return $a;
    }

    public function test_asistencias_out_van_a_dl_si_organiza_el_destino_y_a_out_si_no(): void
    {
        $this->actividades->method('findById')->willReturnCallback(fn (int $id): ?ActividadAll => match ($id) {
            1 => $this->actividad(1, 'dlpf'),   // la organiza el destino (sf: se quita la f)
            2 => $this->actividad(2, 'dlz'),    // otra dl
            default => null,                     // 3: actividad inexistente
        });
        $outOrg = $this->repo(AsistenteOutRepositoryInterface::class, 'H-dlbv');
        $outOrg->method('matching')->willReturn([$this->asistencia(1), $this->asistencia(2), $this->asistencia(3)]);
        $outOrg->expects($this->exactly(2))->method('Eliminar');
        $this->repo(AsistenteDlRepositoryInterface::class, 'H-dlbv')->method('matching')->willReturn([]);
        $guardadasDl = [];
        $this->repo(AsistenteDlRepositoryInterface::class, 'H-dlpv')->method('Guardar')->willReturnCallback(function (Asistente $a) use (&$guardadasDl): bool {
            $guardadasDl[] = $a->getId_activ();
            return true;
        });
        $guardadasOut = [];
        $this->repo(AsistenteOutRepositoryInterface::class, 'H-dlpv')->method('Guardar')->willReturnCallback(function (Asistente $a) use (&$guardadasOut): bool {
            $guardadasOut[] = $a->getId_activ();
            return true;
        });

        $this->assertTrue($this->trasladar()->copiarAsistencias());

        $this->assertSame([1], $guardadasDl);
        $this->assertSame([2], $guardadasOut, 'antes también iba a la tabla dl destino');
    }

    public function test_asistencias_dl_pasan_a_out_del_destino_con_mi_dl_responsable(): void
    {
        $this->repo(AsistenteOutRepositoryInterface::class, 'H-dlbv')->method('matching')->willReturn([]);
        $dlOrg = $this->repo(AsistenteDlRepositoryInterface::class, 'H-dlbv');
        $a = $this->asistencia(5);
        $a->setDl_responsable('dlz');
        $dlOrg->method('matching')->willReturn([$a]);
        $dlOrg->expects($this->once())->method('Eliminar')->with($a);
        $this->actividades->method('matching')->willReturn([$this->actividad(5, 'dlb')]);
        $this->repo(AsistenteOutRepositoryInterface::class, 'H-dlpv')->expects($this->once())->method('Guardar')->with($a)->willReturn(true);

        $this->assertTrue($this->trasladar()->copiarAsistencias());
        $this->assertSame('dlb', $a->getDl_responsable());
    }

    public function test_si_no_se_guarda_la_asistencia_no_se_borra_el_origen_y_se_avisa(): void
    {
        $this->actividades->method('findById')->willReturn($this->actividad(2, 'dlz'));
        $outOrg = $this->repo(AsistenteOutRepositoryInterface::class, 'H-dlbv');
        $outOrg->method('matching')->willReturn([$this->asistencia(2)]);
        $outOrg->expects($this->never())->method('Eliminar');
        $this->repo(AsistenteDlRepositoryInterface::class, 'H-dlbv')->method('matching')->willReturn([]);
        $this->repo(AsistenteOutRepositoryInterface::class, 'H-dlpv')->method('Guardar')->willReturn(false);

        $t = $this->trasladar();
        $this->assertFalse($t->copiarAsistencias());
        $this->assertStringContainsString('No se ha guardado la asistencia(out) a id_activ: 2', $t->getError());
    }

    public function test_apuntar_el_traslado_en_la_dl_origen(): void
    {
        $repo = $this->repo(TrasladoRepositoryInterface::class, 'H-dlbv');
        $repo->method('getNewId')->willReturn(9);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (Traslado $t): bool {
            $this->assertSame([9, 100123, 'dl', 'dlb', 'dlp'], [$t->getId_item(), $t->getId_nom(), $t->getTipo_cmb(), $t->getCtr_origen(), $t->getCtr_destino()]);
            return true;
        });
        $this->assertTrue($this->trasladar()->apuntar());
    }

    public function test_certificados_recibidos_se_mueven_al_destino_y_un_fallo_conserva_el_mensaje(): void
    {
        $c = new CertificadoRecibido();
        $c->setId_item(3);
        $c->setId_nom(100123);
        $org = $this->repo(CertificadoRecibidoRepositoryInterface::class, 'H-dlbv');
        $org->method('matching')->willReturn([$c]);
        $org->method('findById')->with(3)->willReturn($c);
        $org->expects($this->never())->method('Eliminar');
        $dst = $this->repo(CertificadoRecibidoRepositoryInterface::class, 'H-dlpv');
        $dst->method('getNewId_item')->willReturn(70);
        $dst->method('Guardar')->willReturn(false);
        $dst->method('getErrorTxt')->willReturn('clave duplicada');

        $t = $this->trasladar();
        $this->assertFalse($t->trasladarDossierCertificados());
        $this->assertSame(70, $c->getId_item());
        // Antes «$error .= '<br>' . $this->serror = $error» perdía el texto del error,
        // y el original se borraba aunque no se hubiera copiado.
        $this->assertStringContainsString('clave duplicada', $t->getError());
    }

    public function test_certificado_recibido_copiado_se_borra_del_origen(): void
    {
        $c = new CertificadoRecibido();
        $c->setId_item(3);
        $c->setId_nom(100123);
        $org = $this->repo(CertificadoRecibidoRepositoryInterface::class, 'H-dlbv');
        $org->method('matching')->willReturn([$c]);
        $org->method('findById')->with(3)->willReturn($c);
        $org->expects($this->once())->method('Eliminar')->willReturn(true);
        $dst = $this->repo(CertificadoRecibidoRepositoryInterface::class, 'H-dlpv');
        $dst->method('getNewId_item')->willReturn(70);
        $dst->expects($this->once())->method('Guardar')->with($c)->willReturn(true);
        $this->assertTrue($this->trasladar()->trasladarDossierCertificados());
    }
}
