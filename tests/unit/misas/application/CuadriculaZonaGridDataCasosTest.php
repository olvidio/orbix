<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\entity\EncargoTipo;
use src\misas\application\CuadriculaZonaGridData;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Cuadrícula de una zona con datos (plan de misas, del 2 al 4 de marzo de 2026, lunes a miércoles):
 * filas de encargos con las iniciales del sacd de cada día (hora, observaciones, color por estado,
 * aviso si no es de la zona) y filas de los sacd (SI/NO según los días que está en la zona, número de
 * misas, ausencias). Incluye una zona con sacd pero sin encargos de misa.
 */
final class CuadriculaZonaGridDataCasosTest extends myTest
{
    private const ZONA = 4;
    private const SACD = 10011;
    private const OTRO = 10099;

    /** @var list<Encargo> */
    private array $encargosZona = [];
    /** @var array<int, Encargo> */
    private array $encargosPorId = [];
    /** @var array<string, list<EncargoDia>> «enc#dia» o «nom#dia» => días */
    private array $dias = [];
    /** @var list<EncargoSacdHorario> */
    private array $ausencias = [];
    private mixed $sesion;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesion = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth']['id_usuario'] = 55;
    }

    public function tearDown(): void
    {
        $_SESSION['session_auth'] = $this->sesion;
        parent::tearDown();
    }

    private function encargo(int $id, int $tipo, string $desc, bool $deLaZona = true): Encargo
    {
        $e = new Encargo();
        $e->setId_enc($id);
        $e->setId_tipo_enc($tipo);
        $e->setDesc_enc($desc);
        $e->setId_zona(self::ZONA);
        $this->encargosPorId[$id] = $e;
        if ($deLaZona) {
            $this->encargosZona[] = $e;
        }
        return $e;
    }

    private function dia(int $idEnc, int $idNom, string $fecha, string $hora, int $status, ?string $observ = null): void
    {
        $d = new EncargoDia();
        $d->setUuidItemVo('11111111-2222-4333-8444-' . str_pad((string) (count($this->dias) + 1), 12, '0', STR_PAD_LEFT));
        $d->setId_enc($idEnc);
        $d->setId_nom($idNom);
        $d->setTstart(new DateTimeLocal("$fecha $hora:00"));
        $d->setTend(new DateTimeLocal("$fecha $hora:00"));
        $d->setStatus($status);
        $d->setObserv($observ);
        $this->dias["enc$idEnc#$fecha"][] = $d;
        $this->dias["nom$idNom#$fecha"][] = $d;
    }

    private function grid(): CuadriculaZonaGridData
    {
        $tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $tipos->method('matching')->willReturnCallback(static function (): array {
            return array_map(static function (int $id): EncargoTipo {
                $t = new EncargoTipo();
                $t->setId_tipo_enc($id);
                return $t;
            }, [8010, 8110, 8210]);
        });
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->method('matching')->willReturnCallback(fn (): array => $this->encargosZona);
        $encargos->method('findById')->willReturnCallback(fn (int $id): ?Encargo => $this->encargosPorId[$id] ?? null);
        $dias = $this->createMock(EncargoDiaRepositoryInterface::class);
        $dias->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            preg_match('/^(id_enc|id_nom) eq (\d+)$/', $f[0], $m);
            preg_match('/(\d{4}-\d{2}-\d{2})/', $f[1], $d);
            return $this->dias[($m[1] === 'id_enc' ? 'enc' : 'nom') . $m[2] . '#' . $d[1]] ?? [];
        });
        $zonaSacd = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacd->method('matching')->willReturnCallback(static function (): array {
            $z = new ZonaSacd();
            $z->setId_nom(self::SACD);
            $z->setId_zona(self::ZONA);
            foreach ([1, 3, 4, 5, 6, 7] as $d) {
                $z->{"setDw$d"}(true);
            }
            $z->setDw2(false); // los martes no está en la zona
            return [$z];
        });
        $iniciales = $this->createMock(InicialesSacdService::class);
        $iniciales->method('obtenerIniciales')->willReturnCallback(static fn (int $id): string => $id === self::SACD ? 'AB' : 'ZZ');
        $iniciales->method('obtenerNombreConIniciales')->willReturnCallback(static fn (int $id): string => $id === self::SACD ? 'Sacd Uno (AB)' : 'Otro (ZZ)');
        $cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargos->method('getAsistenteCargoDeActividad')->willReturn([]);
        $horarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $horarios->method('matching')->willReturnCallback(fn (): array => $this->ausencias);

        return new CuadriculaZonaGridData(
            $this->createMock(PreferenciaRepositoryInterface::class),
            $zonaSacd,
            $iniciales,
            $tipos,
            $dias,
            $this->createMock(ActividadRepositoryInterface::class),
            $cargos,
            $encargos,
            $horarios,
            $this->createMock(EncargoHorarioRepositoryInterface::class),
        );
    }

    /** @return array<string, mixed> */
    private function plan(): array
    {
        return $this->grid()->build(['id_zona' => self::ZONA, 'tipo_plantilla' => 'p', 'periodo' => 'otro', 'empiezamin' => '2026-03-02', 'empiezamax' => '2026-03-05']);
    }

    /** @param array<string, mixed> $r @return array<string, mixed> */
    private static function fila(array $r, string $encargo): array
    {
        foreach ($r['data_cuadricula'] as $fila) {
            if (($fila['encargo'] ?? '') === $encargo) {
                return $fila;
            }
        }
        self::fail("no hay fila «{$encargo}»");
    }

    public function test_filas_de_encargos_con_el_sacd_de_cada_dia(): void
    {
        $this->encargo(501, 8110, 'Misa 8h');
        $this->dia(501, self::SACD, '2026-03-02', '08:00', EncargoDiaStatus::STATUS_COMUNICADO_SACD, 'llevar alba');
        $this->dia(501, self::OTRO, '2026-03-03', '00:00', EncargoDiaStatus::STATUS_PROPUESTA);
        $this->ausencias = [];

        $r = $this->plan();

        $this->assertSame('', $r['error']);
        $fila = self::fila($r, 'Misa 8h');
        $this->assertSame('AB 08:00*', $fila['2026-03-02'], 'iniciales, hora y * si hay observaciones');
        $this->assertSame('ZZ ', $fila['2026-03-03'], 'sin hora si es 00:00');
        $this->assertSame(' -- ', $fila['2026-03-04'], 'día sin sacd');
        $meta = $fila['meta'];
        $this->assertSame(['amarilloclaro', self::SACD . '#AB', ''], [$meta['2026-03-02']['color'], $meta['2026-03-02']['key'], $meta['2026-03-02']['texto']]);
        $this->assertSame(['amarillo', 'No es de la zona'], [$meta['2026-03-03']['color'], $meta['2026-03-03']['texto']]);
    }

    public function test_filas_de_los_sacd_si_no_segun_los_dias_de_la_zona_misas_y_ausencias(): void
    {
        $this->encargo(501, 8110, 'Misa 8h');
        $this->encargo(502, 8210, 'Misa 19h');
        $this->encargo(700, 7100, 'Ausencia: retiro', false);
        $this->dia(501, self::SACD, '2026-03-02', '08:00', EncargoDiaStatus::STATUS_PROPUESTA);
        $this->dia(502, self::SACD, '2026-03-02', '19:00', EncargoDiaStatus::STATUS_PROPUESTA);
        $a = new EncargoSacdHorario();
        $a->setId_enc(700);
        $a->setId_nom(self::SACD);
        $a->setF_ini(new DateTimeLocal('2026-03-04'));
        $a->setF_fin(new DateTimeLocal('2026-03-04'));
        $this->ausencias = [$a];

        $sacd = self::fila($this->plan(), 'Sacd Uno (AB)');

        $this->assertSame('SI', $sacd['2026-03-02']);
        $this->assertSame(['amarillo', 'Este día tiene dos Misas'], [$sacd['meta']['2026-03-02']['color'], $sacd['meta']['2026-03-02']['texto']]);
        $this->assertSame('NO', $sacd['2026-03-03'], 'los martes no está en la zona');
        $this->assertSame('--', $sacd['2026-03-04'], 'ausente');
        $this->assertStringStartsWith('Está en Ausencia: retiro', $sacd['meta']['2026-03-04']['texto']);
    }

    /** Antes: el día de la semana de cada fecha solo se calculaba en el bucle de encargos (avisos de PHP y «NO» en todos los días). */
    public function test_zona_con_sacd_pero_sin_encargos_de_misa(): void
    {
        $avisos = [];
        set_error_handler(static function (int $n, string $m, string $f, int $l) use (&$avisos): bool {
            $avisos[] = "$m (" . basename($f) . ":$l)";
            return true;
        });
        try {
            $r = $this->plan();
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $avisos);
        $sacd = self::fila($r, 'Sacd Uno (AB)');
        $this->assertSame(['SI', 'NO', 'SI'], [$sacd['2026-03-02'], $sacd['2026-03-03'], $sacd['2026-03-04']]);
        $this->assertSame('Este día no tiene ninguna Misa', $sacd['meta']['2026-03-02']['texto']);
    }

    /** Antes: con un tipo de plantilla desconocido la fila de títulos leía un índice inexistente. */
    public function test_tipo_de_plantilla_desconocido_sin_avisos(): void
    {
        $avisos = [];
        set_error_handler(static function (int $n, string $m, string $f, int $l) use (&$avisos): bool {
            $avisos[] = "$m (" . basename($f) . ":$l)";
            return true;
        });
        try {
            $r = $this->grid()->build(['id_zona' => self::ZONA, 'tipo_plantilla' => 'x', 'periodo' => 'otro', 'empiezamin' => '2026-03-02', 'empiezamax' => '2026-03-04']);
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $avisos);
        $this->assertSame('', $r['error']);
    }
}
