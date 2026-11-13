<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\misas\application\VerMisasZonaData;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Misas de una zona ({@see VerMisasZonaData}): columnas por día, filas de los encargos 8010/8011 de la
 * zona con las iniciales del sacd (de la zona, n/agd o todos según la selección), hora y observaciones,
 * y error si un encargo tiene dos asignaciones el mismo día.
 */
final class VerMisasZonaDataCasosTest extends myTest
{
    /** @var array<string, list<EncargoDia>> «id_enc#día» => días */
    private array $dias = [];
    /** @var list<string> */
    private array $consultasPersonas = [];

    private static function sacd(int $id, string $nom, string $ap1, ?string $ap2): PersonaSacd
    {
        $p = new PersonaSacd();
        $p->setId_nom($id);
        $p->setNom($nom);
        $p->setApellido1($ap1);
        $p->setApellido2($ap2);
        return $p;
    }

    private function dia(int $idEnc, int $idNom, string $fecha, string $hora, ?string $observ = null): void
    {
        $d = new EncargoDia();
        $d->setUuidItemVo('11111111-2222-4333-8444-' . str_pad((string) (count($this->dias) + 1), 12, '0', STR_PAD_LEFT));
        $d->setId_enc($idEnc);
        $d->setId_nom($idNom);
        $d->setTstart(new DateTimeLocal("$fecha $hora:00"));
        $d->setTend(new DateTimeLocal("$fecha $hora:00"));
        $d->setObserv($observ);
        $this->dias["$idEnc#$fecha"][] = $d;
    }

    /** @return array<string, mixed> */
    private function build(int $seleccion): array
    {
        $personas = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personas->method('findById')->willReturnCallback(static fn (int $id): ?PersonaSacd => $id === 10011 ? self::sacd(10011, 'Pedro', 'Gómez', 'Ruiz') : null);
        $personas->method('matching')->willReturnCallback(function ($c): array {
            $this->consultasPersonas[] = implode(', ', DescribeCriteria::filtros($c));
            return [self::sacd(10022, 'Ana', 'López', null)];
        });
        $zonaSacd = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacd->method('getIdSacdsDeZona')->willReturn([10011, 10999]);
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['id_tipo_enc in [8010,8011]', 'id_zona eq 7'], DescribeCriteria::filtros($c));
            $e = new Encargo();
            $e->setId_enc(501);
            $e->setId_tipo_enc(8010);
            $e->setDesc_enc('Misa parroquia');
            return [$e];
        });
        $dias = $this->createMock(EncargoDiaRepositoryInterface::class);
        $dias->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            preg_match('/(\d{4}-\d{2}-\d{2})/', $f[1], $m);
            return $this->dias[substr($f[0], strlen('id_enc eq ')) . '#' . $m[1]] ?? [];
        });

        return (new VerMisasZonaData($personas, $zonaSacd, $dias, $this->createMock(EncargoHorarioRepositoryInterface::class), $encargos))
            ->build(['id_zona' => 7, 'seleccion' => $seleccion, 'empiezamin' => '02/03/2026', 'empiezamax' => '04/03/2026']);
    }

    public function test_encargos_de_la_zona_con_las_iniciales_de_sus_sacd(): void
    {
        $this->dia(501, 10011, '2026-03-02', '08:30', 'en la capilla');
        $this->dia(501, 10011, '2026-03-03', '00:00');
        $this->dia(501, 10555, '2026-03-04', '19:00');

        $r = $this->build(2);

        $this->assertSame('', $r['error']);
        $columnas = json_decode($r['columns_cuadricula'], true);
        $this->assertSame(['encargo', '2026-03-02', '2026-03-03', '2026-03-04'], array_column($columnas, 'field'));
        $fila = $r['data_cuadricula'][0];
        $this->assertSame('Misa parroquia', $fila['encargo']);
        $this->assertSame('PGR* 08:30', $fila['2026-03-02'], 'iniciales, * por las observaciones y hora');
        $this->assertSame('PGR ', $fila['2026-03-03'], 'sin hora si es 00:00');
        $this->assertSame(' 19:00', $fila['2026-03-04'], 'sacd que no está en la selección: sin iniciales');
        $this->assertSame(['10011#PGR', 'misas', 'en la capilla'], [$fila['meta']['2026-03-02']['key'], $fila['meta']['2026-03-02']['tipo'], $fila['meta']['2026-03-02']['observ']]);
        $this->assertSame([], $this->consultasPersonas, 'solo los de la zona');
    }

    public function test_la_seleccion_4_son_los_n_y_agd_y_la_8_todos_los_sacd(): void
    {
        $this->dia(501, 10022, '2026-03-02', '08:30');

        $r = $this->build(4 | 8);

        $this->assertSame('AL 08:30', $r['data_cuadricula'][0]['2026-03-02']);
        $this->assertSame([
            'sacd eq true, situacion eq A, id_tabla in [n,a]',
            'sacd eq true, situacion eq A',
        ], $this->consultasPersonas);
    }

    public function test_dos_asignaciones_el_mismo_dia_devuelven_error(): void
    {
        $this->dia(501, 10011, '2026-03-02', '08:30');
        $this->dia(501, 10022, '2026-03-02', '09:30');

        $r = $this->build(2);

        $this->assertSame('solo deberia haber uno', $r['error']);
        $this->assertSame('[]', $r['columns_cuadricula']);
    }
}
