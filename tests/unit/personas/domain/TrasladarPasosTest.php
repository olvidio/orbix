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
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\entity\Asignatura;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\entity\TipoDossier;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\TelecoPersona;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use Tests\myTest;

/**
 * Pasos del traslado de dl con repositorios simulados: la fábrica de
 * repositorios por conexión distingue origen (H-dlbv) y destino (H-dlpv) por
 * el esquema de la conexión real que abre Trasladar.
 */
final class TrasladarPasosTest extends myTest
{
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private TipoDossierRepositoryInterface&MockObject $tiposDossier;
    /** @var array<string, object> «Interfaz@esquema» => doble */
    private array $repos = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->tiposDossier = $this->createMock(TipoDossierRepositoryInterface::class);
    }

    private function repo(string $interfaz, string $esquema): MockObject
    {
        return $this->repos["$interfaz@$esquema"] ??= $this->createMock($interfaz);
    }

    /** @param list<string> $dlActivas */
    private function trasladar(array $dlActivas = []): Trasladar
    {
        $delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $delegaciones->method('matching')->willReturn(array_map(static function (string $dl): Delegacion {
            $d = new Delegacion();
            $d->setDlVo($dl);
            return $d;
        }, $dlActivas));
        $fabrica = $this->createMock(ConnectionRepositoryFactoryInterface::class);
        $fabrica->method('createWithConnection')->willReturnCallback(function (string $id, \PDO $db): object {
            $esquema = (string) $db->query('select current_schema()')->fetchColumn();
            return $this->repo($id, $esquema);
        });
        $t = new Trasladar(
            $delegaciones,
            $this->asignaturas,
            $this->actividades,
            $this->tiposDossier,
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

    /**
     * Duda 1 de la sección 6b del plan: con la dl destino no activa avisaba «Solamente se anotará
     * el traslado», cambiaba la ficha pero no anotaba nada.
     */
    public function test_a_una_dl_no_activa_cambia_la_ficha_y_anota_el_traslado_sin_mover_datos(): void
    {
        $this->repo(PersonaDlRepositoryInterface::class, 'H-dlbv')->method('findById')->willReturn($this->personaDl());
        $n = $this->personaN();
        $repoN = $this->repo(PersonaNRepositoryInterface::class, 'H-dlbv');
        $repoN->method('findById')->willReturn($n);
        $repoN->expects($this->once())->method('Guardar')->willReturn(true);
        $traslados = $this->repo(TrasladoRepositoryInterface::class, 'H-dlbv');
        $traslados->method('getNewId')->willReturn(9);
        $traslados->expects($this->once())->method('Guardar')->willReturnCallback(function (Traslado $t): bool {
            $this->assertSame(['dlb', 'dlp'], [$t->getCtr_origen(), $t->getCtr_destino()]);
            return true;
        });
        $this->repo(PersonaNRepositoryInterface::class, 'H-dlpv')->expects($this->never())->method('Guardar');

        $t = $this->trasladar(['dlb']);
        $t->setDl_persona('dlb');
        $t->setSituacionVo(new SituacionCode('L'));
        $t->setF_traslado(new DateTimeLocal('2027-03-01'));
        $r = $t->trasladar();

        $this->assertFalse($r['success']);
        $this->assertStringContainsString('Solamente se anotará el traslado', $r['mensaje'] ?? '');
        $this->assertSame(['L', 'dlp'], [$n->getSituacion(), $n->getDl()]);
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

    private function personaDl(string $tabla = 'n'): PersonaDl
    {
        $p = new PersonaDl();
        $p->setId_nom(100123);
        $p->setIdTablaVo($tabla);
        return $p;
    }

    private function personaN(): PersonaN
    {
        $p = new PersonaN();
        $p->setId_nom(100123);
        $p->setIdTablaVo('n');
        $p->setSituacionVo('A');
        $p->setDlVo('dlb');
        return $p;
    }

    public function test_cambiar_ficha_deja_la_situacion_y_la_dl_destino_en_el_origen(): void
    {
        $this->repo(PersonaDlRepositoryInterface::class, 'H-dlbv')->method('findById')->willReturn($this->personaDl());
        $n = $this->personaN();
        $repoN = $this->repo(PersonaNRepositoryInterface::class, 'H-dlbv');
        $repoN->method('findById')->willReturn($n);
        $repoN->expects($this->once())->method('Guardar')->with($n)->willReturn(true);
        $t = $this->trasladar();
        $t->setSituacionVo(new SituacionCode('L'));
        $t->setF_traslado(new DateTimeLocal('2027-03-01'));

        $this->assertTrue($t->cambiarFichaPersona());
        $this->assertSame(['L', 'dlp', '2027-03-01'], [$n->getSituacion(), $n->getDl(), $n->getF_situacion()->format('Y-m-d')]);
    }

    public function test_cambiar_ficha_con_tipo_desconocido_o_persona_inexistente(): void
    {
        $this->repo(PersonaDlRepositoryInterface::class, 'H-dlbv')->method('findById')->willReturnOnConsecutiveCalls($this->personaDl('zz'), null);
        $t = $this->trasladar();
        $t->setSituacionVo(new SituacionCode('L'));
        $this->assertFalse($t->cambiarFichaPersona());
        $this->assertSame('No se reconoce el tipo de persona: zz', $t->getError());
        $this->assertFalse($t->cambiarFichaPersona());
        $this->assertStringContainsString('No se ha encontrado la persona con id: 100123', $t->getError());
    }

    public function test_copiar_persona_al_destino_activa_y_con_la_dl_destino(): void
    {
        $this->repo(PersonaDlRepositoryInterface::class, 'H-dlbv')->method('findById')->willReturn($this->personaDl());
        $org = $this->personaN();
        $org->setSituacionVo('L');
        $this->repo(PersonaNRepositoryInterface::class, 'H-dlbv')->method('findById')->willReturn($org);
        $guardada = null;
        $this->repo(PersonaNRepositoryInterface::class, 'H-dlpv')->expects($this->once())->method('Guardar')
            ->willReturnCallback(function (PersonaN $p) use (&$guardada): bool {
                $guardada = $p;
                return true;
            });
        $t = $this->trasladar();
        $t->setF_traslado(new DateTimeLocal('2027-03-01'));

        $this->assertTrue($t->copiarPersona());
        $this->assertNotSame($org, $guardada, 'se guarda una copia');
        $this->assertSame(['A', 'dlp', '2027-03-01'], [$guardada->getSituacion(), $guardada->getDl(), $guardada->getF_situacion()->format('Y-m-d')]);
        $this->assertSame('L', $org->getSituacion());
    }

    public function test_aviso_de_notas_pendientes(): void
    {
        $m = new Matricula();
        $m->setId_activ(30);
        $m->setId_nom(100123);
        $m->setIdAsignaturaVo(1101);
        $this->repo(MatriculaDlRepositoryInterface::class, 'H-dlbv')->method('getMatriculasPendientes')->with(100123)->willReturn([$m]);
        $a = new \src\actividades\domain\entity\ActividadAll();
        $a->setNom_activ('ca n 2027');
        $this->actividades->method('findById')->with(30)->willReturn($a);
        $asig = new Asignatura();
        $asig->setNombre_corto('Lógica');
        $this->asignaturas->method('findById')->with(1101)->willReturn($asig);

        $t = $this->trasladar();
        $this->assertFalse($t->comprobarNotas());
        $this->assertSame("tiene pendiente de poner las notas de:\nca: ca n 2027, asignatura: Lógica", $t->getError());
    }

    public function test_sin_notas_pendientes_no_avisa(): void
    {
        $this->repo(MatriculaDlRepositoryInterface::class, 'H-dlbv')->method('getMatriculasPendientes')->willReturn([]);
        $this->assertTrue($this->trasladar()->comprobarNotas());
    }

    private function dossier(int $tipo): Dossier
    {
        $d = new Dossier();
        $d->setTabla('p');
        $d->setId_pau(100123);
        $d->setId_tipo_dossier($tipo);
        return $d;
    }

    public function test_dossiers_copian_sus_datos_y_el_estado_del_dossier(): void
    {
        $dTeleco = $this->dossier(1001);
        $dSinClase = $this->dossier(1099);
        $this->repo(DossierRepositoryInterface::class, 'H-dlbv')->method('matching')->willReturn([$dTeleco, $dSinClase]);
        $this->tiposDossier->method('findById')->willReturnCallback(function (int $id): ?TipoDossier {
            $t = new TipoDossier();
            $t->setClassVo($id === 1001 ? 'TelecoPersonaDl' : null);
            return $t;
        });
        $tel = new TelecoPersona();
        $tel->setId_item(5);
        $tel->setId_nom(100123);
        $telOrg = $this->repo(TelecoPersonaDlRepositoryInterface::class, 'H-dlbv');
        $telOrg->method('matching')->willReturn([$tel]);
        $telOrg->expects($this->once())->method('Eliminar');
        $telDst = $this->repo(TelecoPersonaDlRepositoryInterface::class, 'H-dlpv');
        $telDst->method('getNewId')->willReturn(88);
        $telDst->expects($this->once())->method('Guardar')->willReturnCallback(fn (TelecoPersona $x): bool => $x->getId_item() === 88);
        $guardados = [];
        $this->repo(DossierRepositoryInterface::class, 'H-dlpv')->method('Guardar')->willReturnCallback(function (Dossier $d) use (&$guardados): bool {
            $guardados[] = $d->getId_tipo_dossier();
            return true;
        });

        $this->assertTrue($this->trasladar()->trasladarDossiers());
        // Un tipo de dossier sin clase de datos no se copia (ni su estado), como en el original.
        $this->assertSame([1001], $guardados);
    }

    public function test_dossier_de_traslados_conserva_en_el_origen_el_de_hoy(): void
    {
        $this->repo(DossierRepositoryInterface::class, 'H-dlbv')->method('matching')->willReturn([$this->dossier(1004)]);
        $this->tiposDossier->method('findById')->willReturnCallback(function (): TipoDossier {
            $t = new TipoDossier();
            $t->setClassVo('Traslado');
            return $t;
        });
        $viejo = new Traslado();
        $viejo->setId_item(1);
        $viejo->setId_nom(100123);
        $viejo->setF_traslado(new DateTimeLocal('2001-01-01'));
        $viejo->setTipo_cmb('dl');
        $hoy = new Traslado();
        $hoy->setId_item(2);
        $hoy->setId_nom(100123);
        $hoy->setF_traslado(new DateTimeLocal());
        $hoy->setTipo_cmb('dl');
        $org = $this->repo(TrasladoRepositoryInterface::class, 'H-dlbv');
        $org->method('matching')->willReturn([$viejo, $hoy]);
        $borrados = [];
        $org->method('Eliminar')->willReturnCallback(function (Traslado $t) use (&$borrados): bool {
            $borrados[] = $t->getId_item();
            return true;
        });
        $dst = $this->repo(TrasladoRepositoryInterface::class, 'H-dlpv');
        $dst->method('getNewId')->willReturnOnConsecutiveCalls(50, 51);
        $dst->method('Guardar')->willReturn(true);
        $this->repo(DossierRepositoryInterface::class, 'H-dlpv')->method('Guardar')->willReturn(true);

        $this->assertTrue($this->trasladar()->trasladarDossiers());
        $this->assertSame([1], $borrados, 'se queda en el origen el traslado de hoy, para saber adónde fue');
    }

    public function test_dossier_que_no_se_puede_copiar_avisa_y_no_borra(): void
    {
        $this->repo(DossierRepositoryInterface::class, 'H-dlbv')->method('matching')->willReturn([$this->dossier(1001)]);
        $this->tiposDossier->method('findById')->willReturnCallback(function (): TipoDossier {
            $t = new TipoDossier();
            $t->setClassVo('TelecoPersonaDl');
            return $t;
        });
        $tel = new TelecoPersona();
        $tel->setId_item(5);
        $tel->setId_nom(100123);
        $org = $this->repo(TelecoPersonaDlRepositoryInterface::class, 'H-dlbv');
        $org->method('matching')->willReturn([$tel]);
        $org->expects($this->never())->method('Eliminar');
        $this->repo(TelecoPersonaDlRepositoryInterface::class, 'H-dlpv')->method('Guardar')->willReturn(false);
        $this->repo(DossierRepositoryInterface::class, 'H-dlpv')->method('Guardar')->willReturn(true);

        $t = $this->trasladar();
        $this->assertFalse($t->trasladarDossiers());
        $this->assertStringContainsString('No se ha guardado el dossier: TelecoPersonaDl', $t->getError());
    }
}
