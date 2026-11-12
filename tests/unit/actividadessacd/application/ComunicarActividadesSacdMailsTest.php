<?php

declare(strict_types=1);

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadessacd\application\services\ActividadesSacdHelper;
use src\actividadessacd\application\services\ComunicarActividadesSacdService;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\entity\ConfigSchema;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\permisos\domain\PermisosActividades;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaSacd;
use src\personas\domain\services\TelecoPersonaService;
use src\personas\domain\value_objects\PersonaApellido1Text;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\entity\ColaMail;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use Tests\myTest;

/**
 * Comunicación de actividades a los sacd: filas con datos reales (fechas en
 * romanos, horas, tipo, lugar, centros, cargo), orden, propuesta, solo cargos,
 * quitar inactivos; y encolado de los correos (jefe de calendario, sacd, ctr).
 *
 * Extiende myTest porque una actividad sin lugar especial busca la casa con
 * Ubi::NewUbi (localizador estático contra la BD; con un id inexistente).
 */
final class ComunicarActividadesSacdMailsTest extends myTest
{
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private ActividadCargoRepositoryInterface&MockObject $actCargos;
    private CargoRepositoryInterface&MockObject $cargos;
    private CentroEncargadoRepositoryInterface&MockObject $centros;
    private ConfigSchemaRepositoryInterface&MockObject $config;
    private UsuarioRepositoryInterface&MockObject $usuarios;
    private PersonaDlRepositoryInterface&MockObject $personas;
    private CentroDlRepositoryInterface&MockObject $centrosDl;
    private TelecoPersonaService&MockObject $teleco;
    private ColaMailRepositoryInterface&MockObject $cola;
    private EncargoSacdHorarioRepositoryInterface&MockObject $horarios;
    /** @var list<ColaMail> */
    private array $encolados = [];
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'oPermActividades'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $_SESSION['config'] = ['a_apps' => [], 'app_installed' => []];
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->actCargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->cargos = $this->createMock(CargoRepositoryInterface::class);
        $this->cargos->method('getArrayCargos')->with('sacd')->willReturn([35 => 'sacd']);
        $this->centros = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->config = $this->createMock(ConfigSchemaRepositoryInterface::class);
        $this->usuarios = $this->createMock(UsuarioRepositoryInterface::class);
        $this->personas = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);
        $this->teleco = $this->createMock(TelecoPersonaService::class);
        $this->cola = $this->createMock(ColaMailRepositoryInterface::class);
        $this->cola->method('Guardar')->willReturnCallback(function (ColaMail $m): bool {
            $this->encolados[] = $m;
            return true;
        });
        $this->horarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $this->horarios->method('matching')->willReturn([]);
    }

    public function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function service(): ComunicarActividadesSacdService
    {
        $textos = $this->createMock(ActividadSacdTextoRepositoryInterface::class);
        $t = new ActividadSacdTexto();
        $t->setIdioma('es');
        $t->setClave('com_sacd');
        $t->setTexto('Estas son tus actividades');
        $t2 = new ActividadSacdTexto();
        $t2->setIdioma('es');
        $t2->setClave('t_propio');
        $t2->setTexto('actividad propia');
        $textos->method('matching')->willReturn([$t, $t2]);
        $helper = new ActividadesSacdHelper($textos, $this->createMock(CentroDlRepositoryInterface::class));
        $s = new ComunicarActividadesSacdService(
            $this->cargos, $this->actividades, $this->centros, $this->actCargos, $helper,
            $this->config, $this->usuarios, $this->personas, $this->centrosDl, $this->teleco,
            $this->cola, $this->createMock(EncargoRepositoryInterface::class), $this->horarios,
        );
        $s->setInicioIso('2027-01-01');
        $s->setFinIso('2027-12-31');
        return $s;
    }

    private function sacd(int $id = 4411): PersonaSacd
    {
        $p = new PersonaSacd();
        $p->setId_schema(1);
        $p->setId_nom($id);
        $p->setIdTablaVo(new PersonaTablaCode('n'));
        $p->setApellido1Vo(new PersonaApellido1Text('García'));
        $p->setSituacionVo(new SituacionCode('A'));
        return $p;
    }

    private function actividad(int $id, string $ini, string $fin, int $tipo = 112021, string $lugar = 'Casa de prueba', ?int $ubi = null): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setId_tipo_activ($tipo);
        $a->setNom_activ("act $id");
        $a->setDl_org('dlb');
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($fin));
        $a->setLugar_esp($lugar);
        $a->setId_ubi($ubi);
        return $a;
    }

    private function conActividades(array $filas, array $actividades): void
    {
        $this->actCargos->method('getAsistenteCargoDeActividad')->willReturn($filas);
        $this->actividades->method('findById')->willReturnCallback(fn (int $id): ?ActividadAll => $actividades[$id] ?? null);
    }

    public function test_fila_de_actividad_con_todos_los_datos(): void
    {
        $a = $this->actividad(10, '2027-03-05', '2027-03-08');
        $a->setH_ini(TimeLocal::fromString('20:30:00'));
        $a->setH_fin(TimeLocal::fromString('10:00:00'));
        $a->setObserv('traer alba');
        $this->conActividades([['id_activ' => '10', 'propio' => 't', 'id_cargo' => '12']], [10 => $a]);
        $c1 = new CentroDl();
        $c1->setNombre_ubi('Ctr A');
        $c2 = new CentroDl();
        $c2->setNombre_ubi('Ctr B');
        $this->centros->method('getCentrosEncargadosActividad')->with(10)->willReturn([$c1, $c2]);

        $out = $this->service();
        $out->setPersonas([$this->sacd()]);
        $res = $out->getArrayComunicacion();

        $this->assertSame('Estas son tus actividades', $res[4411]['txt']['com_sacd']);
        $this->assertStringContainsString('falta definir el texto t_f_ini', $res[4411]['txt']['t_f_ini']);
        $this->assertSame([
            'propio' => true,
            'f_ini' => '5.III.27 (20:30)',
            'f_fin' => '8.III.27 (10:00)',
            'nombre_ubi' => 'Casa de prueba',
            'id_activ' => 10,
            'sfsv' => 'sv',
            'asistentes' => 'n',
            'actividad' => 'ca',
            'nom_tipo' => $res[4411]['actividades'][0]['nom_tipo'],
            'observ' => 'traer alba',
            'cargo' => 'te carrec',
            'encargado' => 'Ctr A, Ctr B',
        ], $res[4411]['actividades'][0]);
    }

    public function test_cargo_de_sacd_o_sin_cargo_no_se_marca_y_sin_lugar_busca_la_casa(): void
    {
        $this->conActividades(
            [['id_activ' => 10, 'propio' => 'f', 'id_cargo' => 35], ['id_activ' => 11, 'propio' => false, 'id_cargo' => '']],
            [10 => $this->actividad(10, '2027-03-05', '2027-03-08'), 11 => $this->actividad(11, '2027-04-05', '2027-04-08', lugar: '', ubi: 999999991)]
        );
        $this->centros->method('getCentrosEncargadosActividad')->willReturn([]);
        $s = $this->service();
        $s->setPersonas([$this->sacd()]);
        $acts = $s->getArrayComunicacion()[4411]['actividades'];
        $this->assertSame(['', ''], array_column($acts, 'cargo'));
        $this->assertSame([false, false], array_column($acts, 'propio'));
        $this->assertSame('?', $acts[1]['nombre_ubi']);
    }

    public function test_ordena_por_fecha_conserva_las_del_mismo_dia_y_salta_las_inexistentes(): void
    {
        $this->conActividades(
            [['id_activ' => 3], ['id_activ' => 1], ['id_activ' => 2], ['id_activ' => 99]],
            [
                1 => $this->actividad(1, '2027-05-01', '2027-05-02'),
                2 => $this->actividad(2, '2027-05-01', '2027-05-03'),
                3 => $this->actividad(3, '2027-02-10', '2027-02-12'),
            ]
        );
        $this->centros->method('getCentrosEncargadosActividad')->willReturn([]);
        $s = $this->service();
        $s->setPersonas([$this->sacd()]);
        $this->assertSame([3, 1, 2], array_column($s->getArrayComunicacion()[4411]['actividades'], 'id_activ'));
    }

    public function test_solo_cargos_consulta_los_cargos_y_quitar_inactivos_omite_a_quien_no_tiene_nada(): void
    {
        $this->actCargos->expects($this->once())->method('getCargoDeActividad')->willReturn([]);
        $this->actCargos->expects($this->never())->method('getAsistenteCargoDeActividad');
        $s = $this->service();
        $s->setSoloCargos(true);
        $s->setQuitarInactivos(true);
        $s->setPersonas([$this->sacd()]);
        $this->assertSame([], $s->getArrayComunicacion());
    }

    public function test_en_propuesta_no_se_miran_los_permisos(): void
    {
        $_SESSION['config'] = ['a_apps' => ['procesos' => 3], 'app_installed' => [3]];
        $perm = $this->createMock(PermisosActividades::class);
        $perm->expects($this->never())->method('motivoSinPermisoSacd');
        $_SESSION['oPermActividades'] = $perm;
        $this->conActividades([['id_activ' => 10]], [10 => $this->actividad(10, '2027-03-05', '2027-03-08')]);
        $this->centros->method('getCentrosEncargadosActividad')->willReturn([]);
        $s = $this->service();
        $s->setPropuesta('true');
        $s->setPersonas([$this->sacd()]);
        $this->assertCount(1, $s->getArrayComunicacion()[4411]['actividades']);
        $this->assertSame([], $s->getAvisos());
    }

    public function test_actividad_con_permiso_se_incluye(): void
    {
        $_SESSION['config'] = ['a_apps' => ['procesos' => 3], 'app_installed' => [3]];
        $perm = $this->createMock(PermisosActividades::class);
        $perm->expects($this->once())->method('setActividad')->with(10, '112021', 'dlb');
        $perm->method('motivoSinPermisoSacd')->willReturn(null);
        $_SESSION['oPermActividades'] = $perm;
        $this->conActividades([['id_activ' => 10]], [10 => $this->actividad(10, '2027-03-05', '2027-03-08')]);
        $this->centros->method('getCentrosEncargadosActividad')->willReturn([]);
        $s = $this->service();
        $s->setPersonas([$this->sacd()]);
        $this->assertCount(1, $s->getArrayComunicacion()[4411]['actividades']);
    }

    // --- enviarMails ---

    private function jefe(?string $valor = 'jefe1,jefe2', ?string $email = 'jefe@dl.org'): void
    {
        $cfg = $valor === null ? null : new ConfigSchema();
        $cfg?->setValor($valor);
        $this->config->method('findById')->with('jefe_calendario')->willReturn($cfg);
        if ($email === 'sin usuario') {
            $this->usuarios->method('matching')->willReturn([]);
            return;
        }
        $u = new Usuario();
        $u->setEmailVo($email);
        $this->usuarios->method('matching')->willReturn([$u]);
    }

    /** @return array<int, array<string, mixed>> */
    private function comunicacion(string $sfsv = 'sv'): array
    {
        return [4411 => [
            'nom_ap' => 'D. Juan García',
            'txt' => ['com_sacd' => 'Hola', 't_propio' => 'propia', 't_f_ini' => 'inicio', 't_f_fin' => 'fin'],
            'actividades' => [[
                'propio' => true, 'f_ini' => '5.III.27', 'f_fin' => '8.III.27', 'nombre_ubi' => 'Casa',
                'id_activ' => 10, 'sfsv' => $sfsv, 'asistentes' => 'n', 'actividad' => 'ca',
                'nom_tipo' => 'cuadrienio', 'observ' => 'obs', 'cargo' => 'te carrec', 'encargado' => 'Ctr A',
            ]],
        ]];
    }

    public function test_sin_jefe_de_calendario_no_se_envia_nada(): void
    {
        $this->jefe(null);
        $this->assertSame('falta el definir el jefe de calendario', $this->service()->enviarMails($this->comunicacion()));
        $this->assertSame([], $this->encolados);
    }

    public function test_jefe_sin_usuario_o_sin_mail(): void
    {
        $this->jefe('jefe1', 'sin usuario');
        $msg = 'No hay un mail (jefe calendario) para enviar los errores. No se procesan los mails.';
        $this->assertSame($msg, $this->service()->enviarMails($this->comunicacion()));

        $this->usuarios = $this->createMock(UsuarioRepositoryInterface::class);
        $this->config = $this->createMock(ConfigSchemaRepositoryInterface::class);
        $this->jefe('jefe1', null);
        $this->assertSame($msg, $this->service()->enviarMails($this->comunicacion()));
        $this->assertSame([], $this->encolados);
    }

    public function test_encola_un_mail_al_jefe_y_al_sacd_y_otro_al_centro(): void
    {
        $this->jefe();
        $this->usuarios->expects($this->any())->method('matching');
        $p = new PersonaDl();
        $p->setId_ctr(300);
        $p->setIdioma_preferido('ca_ES.UTF-8');
        $this->personas->method('findById')->with(4411)->willReturn($p);
        $this->teleco->method('getEmailPrincipalOPrimero')->with(4411)->willReturn('sacd@dl.org');
        $ctr = $this->createMock(CentroDl::class);
        $ctr->method('emailPrincipalOPrimero')->willReturn('ctr@dl.org');
        $this->centrosDl->method('findById')->with(300)->willReturn($ctr);

        $this->assertSame('', $this->service()->enviarMails($this->comunicacion()));

        $this->assertCount(2, $this->encolados);
        [$alSacd, $alCtr] = $this->encolados;
        $this->assertSame('jefe@dl.org, sacd@dl.org', $alSacd->getMail_to());
        $this->assertSame('atención actividades', $alSacd->getSubject());
        $this->assertStringContainsString('Reply-To: jefe@dl.org', $alSacd->getHeaders());
        // Hallazgo menor: el atributo lang lleva el locale tal cual (no «ca» ni «ca-ES»).
        $this->assertStringStartsWith('<html lang="ca_ES.UTF-8"><body>', $alSacd->getMessage());
        $this->assertStringContainsString('D. Juan García', $alSacd->getMessage());
        $this->assertStringContainsString('* 5.III.27', $alSacd->getMessage());
        $this->assertStringContainsString('te carrec. obs', $alSacd->getMessage());
        $this->assertStringContainsString('vc-dlb', $alSacd->getMessage());
        $this->assertSame('ctr@dl.org', $alCtr->getMail_to());
        $this->assertStringContainsString('Ctr A', $alCtr->getMessage());
    }

    public function test_al_centro_no_se_le_muestran_los_detalles_de_las_actividades_de_sf(): void
    {
        $this->jefe();
        $p = new PersonaDl();
        $p->setId_ctr(300);
        $this->personas->method('findById')->willReturn($p);
        $ctr = $this->createMock(CentroDl::class);
        $ctr->method('emailPrincipalOPrimero')->willReturn('ctr@dl.org');
        $this->centrosDl->method('findById')->willReturn($ctr);

        $this->service()->enviarMails($this->comunicacion('sf'));

        [$alSacd, $alCtr] = $this->encolados;
        $this->assertSame('jefe@dl.org', $alSacd->getMail_to(), 'sin mail del sacd solo va al jefe');
        $this->assertStringContainsString('Ctr A', $alSacd->getMessage());
        $this->assertStringNotContainsString('Ctr A', $alCtr->getMessage());
        $this->assertStringNotContainsString('te carrec', $alCtr->getMessage());
    }

    public function test_sin_actividades_o_sin_centro_no_se_encola_ese_mail(): void
    {
        $this->jefe();
        $this->personas->method('findById')->willReturn(null);
        $datos = $this->comunicacion() + [5522 => ['nom_ap' => 'otro', 'txt' => [], 'actividades' => []]];
        $this->assertSame('', $this->service()->enviarMails($datos));
        $this->assertCount(1, $this->encolados, 'solo el del sacd con actividades, sin centro');
        $this->assertStringStartsWith('<html lang=""><body>', $this->encolados[0]->getMessage());
    }
}
