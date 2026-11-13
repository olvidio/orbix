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
use src\misas\application\CrearNuevoPeriodoData;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaTend;
use src\misas\domain\value_objects\EncargoDiaTstart;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Crear el plan de un periodo a partir de la plantilla ({@see CrearNuevoPeriodoData}), del lunes 2 al
 * miércoles 4 de marzo de 2026: cada día toma el sacd del mismo día de la plantilla salvo que esté
 * ausente o ya tenga una misa a primera hora ese día; un sacd de fuera de la zona también se asigna; con
 * la plantilla de tres semanas, si el de la primera no puede se prueba con el de la segunda.
 */
final class CrearNuevoPeriodoCasosTest extends myTest
{
    private const ZONA = 4;
    private const SACD = 10011;
    private const FUERA = 10099;

    /** @var array<string, list<EncargoDia>> «id_enc#día» => días (plantilla y periodo) */
    private array $dias = [];
    /** @var list<string> «id_enc día sacd hora» guardados */
    private array $guardados = [];
    /** @var list<EncargoSacdHorario> */
    private array $ausencias = [];

    private function plantilla(int $idEnc, string $dia, int $idNom, string $hora): void
    {
        $d = new EncargoDia();
        $d->setId_enc($idEnc);
        $d->setId_nom($idNom);
        $d->setTstart(new EncargoDiaTstart($dia, $hora));
        $d->setTend(new EncargoDiaTend($dia, $hora));
        $this->dias["$idEnc#$dia"][] = $d;
    }

    private function ausente(string $dia): void
    {
        $a = new EncargoSacdHorario();
        $a->setId_enc(700);
        $a->setId_nom(self::SACD);
        $a->setF_ini(new DateTimeLocal($dia));
        $a->setF_fin(new DateTimeLocal($dia));
        $this->ausencias[] = $a;
    }

    /** @return array<string, mixed> */
    private function crear(string $plantilla): array
    {
        $tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $tipos->method('matching')->willReturnCallback(static function (): array {
            $t = new EncargoTipo();
            $t->setId_tipo_enc(8110);
            return [$t];
        });
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->method('matching')->willReturnCallback(static function (): array {
            return array_map(static function (array $e): Encargo {
                $o = new Encargo();
                $o->setId_enc($e[0]);
                $o->setId_tipo_enc(8110);
                $o->setDesc_enc($e[1]);
                $o->setId_zona(self::ZONA);
                return $o;
            }, [[501, 'Misa 8h'], [502, 'Misa 9h']]);
        });
        $encargos->method('findById')->willReturnCallback(static function (int $id): Encargo {
            $o = new Encargo();
            $o->setId_enc($id);
            $o->setId_tipo_enc($id === 700 ? 7100 : 8110);
            $o->setDesc_enc($id === 700 ? 'Retiro' : 'Misa');
            return $o;
        });
        $dias = $this->createMock(EncargoDiaRepositoryInterface::class);
        $dias->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            if (!str_starts_with($f[0], 'id_enc eq ')) {
                return [];
            }
            preg_match('/(\d{4}-\d{2}-\d{2})/', $f[1], $m);
            return $this->dias[substr($f[0], strlen('id_enc eq ')) . '#' . $m[1]] ?? [];
        });
        $dias->method('Guardar')->willReturnCallback(function (EncargoDia $d): bool {
            $dia = $d->getTstart()?->format('Y-m-d');
            $this->guardados[] = $d->getId_enc() . " $dia " . $d->getId_nom() . ' ' . $d->getTstart()?->format('H:i');
            $this->dias[$d->getId_enc() . "#$dia"][] = $d;
            return true;
        });
        $zona = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zona->method('matching')->willReturnCallback(static function (): array {
            $z = new ZonaSacd();
            $z->setId_nom(self::SACD);
            for ($d = 1; $d <= 7; $d++) {
                $z->{"setDw$d"}(true);
            }
            return [$z];
        });
        $cargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargos->method('getAsistenteCargoDeActividad')->willReturn([]);
        $horarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $horarios->method('matching')->willReturnCallback(fn (): array => $this->ausencias);

        $uc = new CrearNuevoPeriodoData($zona, $this->createMock(ActividadRepositoryInterface::class), $this->createMock(InicialesSacdService::class),
            $cargos, $encargos, $horarios, $tipos, $dias, $this->createMock(EncargoHorarioRepositoryInterface::class));

        $avisos = [];
        set_error_handler(static function (int $n, string $m, string $f, int $l) use (&$avisos): bool {
            $avisos[] = "$m (" . basename($f) . ":$l)";
            return true;
        });
        try {
            $r = $uc->build(['id_zona' => self::ZONA, 'tipo_plantilla' => $plantilla, 'periodo' => 'otro', 'empiezamin' => '02/03/2026', 'empiezamax' => '04/03/2026']);
        } finally {
            restore_error_handler();
        }
        $this->assertSame([], $avisos, 'avisos de PHP');
        $this->assertSame('', $r['error']);
        return $r;
    }

    public function test_plantilla_semanal_asigna_salvo_ausencias_y_dos_misas_a_primera_hora(): void
    {
        $this->plantilla(501, '2001-01-01', self::SACD, '08:00');   // lunes
        $this->plantilla(502, '2001-01-01', self::SACD, '09:00');   // lunes: segunda misa a 1.ª hora
        $this->plantilla(501, '2001-01-02', self::SACD, '08:00');   // martes: ausente
        $this->plantilla(501, '2001-01-03', self::FUERA, '08:00');  // miércoles: de fuera de la zona
        $this->ausente('2026-03-03');

        $this->crear('s1');

        $this->assertSame([
            '501 2026-03-02 ' . self::SACD . ' 08:00',
            '501 2026-03-04 ' . self::FUERA . ' 08:00',
        ], $this->guardados);
    }

    /** Antes: si el sacd de la segunda semana no era de la zona, avisos de PHP por índices sin inicializar. */
    public function test_plantilla_de_tres_semanas_prueba_con_la_segunda_si_la_primera_no_puede(): void
    {
        $this->plantilla(501, '2001-01-01', self::SACD, '08:00');   // 1.ª semana: ausente el lunes
        $this->plantilla(501, '2001-01-08', self::FUERA, '08:30');  // 2.ª semana
        $this->ausente('2026-03-02');

        $this->crear('s3');

        $this->assertSame(['501 2026-03-02 ' . self::FUERA . ' 08:30'], $this->guardados);
    }
}
