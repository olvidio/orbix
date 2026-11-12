<?php

declare(strict_types=1);

namespace Tests\unit\ubiscamas\application;

use PHPUnit\Framework\TestCase;
use src\ubiscamas\application\HabitacionGuardar;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use src\ubiscamas\domain\value_objects\CamaId;

/** Alta y modificación de una habitación y de sus camas (descritas o generadas, VIP). */
final class HabitacionGuardarTest extends TestCase
{
    private const ID = '0a6f1d2e-3b4c-4d5e-8f90-112233445566';

    /** @var list<Habitacion> */
    private array $habitaciones = [];
    /** @var list<Cama> */
    private array $camasNuevas = [];

    /** @param list<Cama> $camasExistentes */
    private function uc(?Habitacion $existente = null, array $camasExistentes = [], bool $guardaHabitacion = true): HabitacionGuardar
    {
        $hab = $this->createMock(HabitacionDlRepositoryInterface::class);
        $hab->method('findById')->willReturn($existente);
        $hab->method('Guardar')->willReturnCallback(function (Habitacion $h) use ($guardaHabitacion): bool {
            $this->habitaciones[] = $h;
            return $guardaHabitacion;
        });
        $hab->method('getErrorTxt')->willReturn('fallo BD');
        $camas = $this->createMock(CamaDlRepositoryInterface::class);
        $camas->method('getCamasByHabitacion')->willReturn($camasExistentes);
        $camas->method('Guardar')->willReturnCallback(function (Cama $c): bool {
            $this->camasNuevas[] = $c;
            return true;
        });
        return new HabitacionGuardar($hab, $camas);
    }

    /** @param array<string, mixed> $cambios @return array<string, mixed> */
    private static function entrada(array $cambios = []): array
    {
        return $cambios + [
            'id_habitacion' => '', 'id_ubi' => 1010810, 'orden' => 2, 'nombre' => 'Hab 1', 'numero_camas' => 0,
            'numero_camas_vip' => 0, 'planta' => '1', 'tipoLavabo' => 1, 'sillon' => true, 'adaptada' => false,
            'observaciones' => 'obs', 'despacho' => false, 'new_camas_desc' => [], 'new_camas_larga' => [], 'new_camas_vip' => [],
        ];
    }

    private static function cama(bool $vip): Cama
    {
        $c = new Cama();
        $c->setIdCamaVo(new CamaId('11111111-2222-4333-8444-555555555555'));
        $c->setDescripcionVo(new CamaDescripcion('x'));
        $c->setVip($vip);
        return $c;
    }

    /** @return list<array{string, bool, bool}> */
    private function camas(): array
    {
        return array_map(static fn (Cama $c): array => [$c->getDescripcionVo()->value(), (bool) $c->isLarga(), (bool) $c->isVip()], $this->camasNuevas);
    }

    public function test_alta_de_una_habitacion_con_sus_datos(): void
    {
        $this->assertSame('', $this->uc()->execute(self::entrada()));
        $h = $this->habitaciones[0];
        $this->assertSame([1010810, 2, 'Hab 1', true, false], [$h->getIdUbiVo(), $h->getOrdenVo()->value(), $h->getNombreVo()?->value(), $h->isSillon(), $h->isAdaptada()]);
        $this->assertSame([], $this->camasNuevas);
    }

    public function test_camas_descritas_en_el_formulario(): void
    {
        $this->uc()->execute(self::entrada([
            'new_camas_desc' => ['ventana', '', 'puerta'], 'new_camas_larga' => [0 => 'on'], 'new_camas_vip' => [2 => 'on'],
        ]));
        $this->assertSame([['ventana', true, false], ['puerta', false, true]], $this->camas());
    }

    public function test_genera_las_camas_que_faltan_numeradas_y_vip_hasta_las_pedidas(): void
    {
        $this->uc()->execute(self::entrada(['numero_camas' => 3, 'numero_camas_vip' => 1]));
        $this->assertSame([['Hab 1 - Cama 1', false, true], ['Hab 1 - Cama 2', false, false], ['Hab 1 - Cama 3', false, false]], $this->camas());
    }

    public function test_una_sola_cama_generada_lleva_el_nombre_de_la_habitacion(): void
    {
        $this->uc(null, [self::cama(false)])->execute(self::entrada(['numero_camas' => 2]));
        $this->assertSame([['Hab 1', false, false]], $this->camas());
    }

    public function test_las_vip_que_ya_hay_cuentan_para_las_pedidas(): void
    {
        $this->uc(null, [self::cama(true)])->execute(self::entrada(['numero_camas' => 3, 'numero_camas_vip' => 1]));
        $this->assertSame([false, false], array_column($this->camas(), 2), 'ya había una VIP');

        $this->camasNuevas = [];
        $this->uc()->execute(self::entrada(['numero_camas' => 2, 'numero_camas_vip' => 1, 'new_camas_desc' => ['a'], 'new_camas_vip' => ['on']]));
        $this->assertSame([['a', false, true], ['Hab 1', false, false]], $this->camas(), 'la descrita VIP también cuenta');
    }

    public function test_modificar_una_habitacion_existente(): void
    {
        $h = new Habitacion();
        $h->setIdUbiVo(999);
        $this->uc($h)->execute(self::entrada(['id_habitacion' => self::ID, 'nombre' => 'Nueva']));
        $this->assertSame($h, $this->habitaciones[0]);
        $this->assertSame([999, 'Nueva'], [$h->getIdUbiVo(), $h->getNombreVo()?->value()], 'conserva la casa');
    }

    public function test_errores(): void
    {
        $this->assertStringContainsString('fallo BD', $this->uc(null, [], false)->execute(self::entrada(['numero_camas' => 2])));
        $this->assertSame([], $this->camasNuevas, 'sin habitación guardada no crea camas');
    }
}
