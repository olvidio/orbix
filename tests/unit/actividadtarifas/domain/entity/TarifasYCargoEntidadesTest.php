<?php

declare(strict_types=1);

namespace Tests\unit\actividadtarifas\domain\entity;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\entity\Cargo;
use src\actividadcargos\domain\value_objects\CargoCode;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\actividadtarifas\domain\value_objects\TarifaModoId;

/** Setters con VO, entero o nulo y metadatos de TipoTarifa, RelacionTarifaTipoActividad y Cargo. */
final class TarifasYCargoEntidadesTest extends TestCase
{
    public function test_tipo_tarifa_valores_y_texto_del_modo(): void
    {
        $t = new TipoTarifa();
        $t->setIdTarifaVo(4);
        $this->assertSame(4, $t->getId_tarifa());
        $t->setIdTarifaVo(new TarifaId(5));
        $this->assertSame(5, $t->getIdTarifaVo()->value());

        $t->setModoVo(TarifaModoId::POR_DIA);
        $this->assertSame(TarifaModoId::POR_DIA, $t->getModo());
        $this->assertSame('por día', $t->getModoTxt());
        $t->setModoVo(new TarifaModoId(TarifaModoId::TOTAL));
        $this->assertSame('total', $t->getModoTxt());

        $t->setLetraVo('A');
        $this->assertSame('A', $t->getLetra());
        $t->setLetraVo(null);
        $this->assertNull($t->getLetra());
        $t->setSfsvVo(2);
        $this->assertSame(2, $t->getSfsv());
        $t->setSfsvVo(null);
        $this->assertNull($t->getSfsv());
    }

    public function test_tipo_tarifa_id_y_modo_no_pueden_ser_nulos(): void
    {
        $t = new TipoTarifa();
        foreach (['setIdTarifaVo' => 'id_tarifa cannot be null', 'setModoVo' => 'modo cannot be null'] as $m => $msg) {
            try {
                $t->$m(null);
                $this->fail("$m(null) debería fallar");
            } catch (\InvalidArgumentException $e) {
                $this->assertSame($msg, $e->getMessage());
            }
        }
    }

    public function test_relacion_tarifa_tipo_actividad(): void
    {
        $r = new RelacionTarifaTipoActividad();
        $r->setId_item(9);
        $r->setIdTarifaVo(4);
        $r->setIdTipoActivVo(112021);
        $r->setIdSerieVo(1);
        $this->assertSame([9, 4, 112021, 1], [$r->getId_item(), $r->getId_tarifa(), $r->getId_tipo_activ(), $r->getId_serie()]);
        $r->setIdTarifaVo(new TarifaId(5));
        $r->setIdTipoActivVo(new ActividadTipoId(212021));
        $r->setIdSerieVo(new SerieId(2));
        $this->assertSame([5, 212021, 2], [$r->getIdTarifaVo()->value(), $r->getIdTipoActivVo()->value(), $r->getIdSerieVo()->value()]);
    }

    public function test_cargo_valores_y_metadatos(): void
    {
        $c = new Cargo();
        $c->setId_cargo(35);
        $c->setCargoVo('sacd');
        $this->assertSame('sacd', $c->getCargo());
        $c->setCargoVo(new CargoCode('d.est.'));
        $this->assertSame('d.est.', $c->getCargoVo()->value());
        $c->setOrdenCargoVo(3);
        $this->assertSame(3, $c->getOrden_cargo());
        $c->setOrdenCargoVo(null);
        $this->assertNull($c->getOrden_cargo());
        $c->setTipoCargoVo('sacd');
        $this->assertSame('sacd', $c->getTipo_cargo());
        $c->setTipoCargoVo(null);
        $this->assertNull($c->getTipo_cargo());
        $c->setSf(true);
        $c->setSv(false);
        $this->assertSame([true, false], [$c->isSf(), $c->isSv()]);

        $this->assertSame('id_cargo', $c->getPrimary_key());
        $campos = $c->getDatosCampos();
        $this->assertSame(['cargo', 'orden_cargo', 'sf', 'sv', 'tipo_cargo'], array_map(fn ($d) => $d->getNom_camp(), $campos));
    }
}
