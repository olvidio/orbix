<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\application\UbisTablaData;
use src\ubis\domain\criteria\CasaField;
use src\ubis\domain\criteria\CentroDlField;
use src\ubis\domain\criteria\DireccionField;

/**
 * Traducción de los filtros neutros de {@see UbisTablaData} (los que viajan en la URL de paginación) a `Criteria`.
 */
final class UbisTablaDataFiltrosTest extends TestCase
{
    public function test_texto_sin_acentos_banderas_y_orden_para_centros(): void
    {
        $criterio = $this->criterio(
            ['nombre_ubi' => 'cen', 'active' => 't', 'sv' => 't', '_ordre' => 'tipo_ubi,nombre_ubi'],
            CentroDlField::class,
            ['nombre_ubi', 'dl', 'tipo_ctr', 'tipo_casa'],
        );

        $esperado = Criteria::create()
            ->where(Filter::matchesUnaccented(CentroDlField::NombreUbi, 'cen'))
            ->andWhere(Filter::eq(CentroDlField::Active, true))
            ->andWhere(Filter::eq(CentroDlField::Sv, true))
            ->orderBy(CentroDlField::TipoUbi)
            ->addOrderBy(CentroDlField::NombreUbi);

        $this->assertEquals($esperado, $criterio);
    }

    public function test_bandera_f_es_falso_y_region_es_igualdad(): void
    {
        $criterio = $this->criterio(
            ['region' => 'H', 'sv' => 'f'],
            CasaField::class,
            ['nombre_ubi', 'dl', 'tipo_ctr', 'tipo_casa'],
        );

        $esperado = Criteria::create()
            ->where(Filter::eq(CasaField::Region, 'H'))
            ->andWhere(Filter::eq(CasaField::Sv, false));

        $this->assertEquals($esperado, $criterio);
    }

    public function test_clave_que_no_es_campo_del_enum_se_ignora(): void
    {
        // `tipo_ctr` no existe en las casas.
        $criterio = $this->criterio(
            ['tipo_ctr' => 'dl', 'nombre_ubi' => 'x', '_ordre' => 'tipo_ctr'],
            CasaField::class,
            ['nombre_ubi', 'dl', 'tipo_ctr', 'tipo_casa'],
        );

        $esperado = Criteria::create()->where(Filter::matchesUnaccented(CasaField::NombreUbi, 'x'));

        $this->assertEquals($esperado, $criterio);
    }

    public function test_filtros_de_direccion(): void
    {
        $criterio = $this->criterio(
            ['poblacion' => 'Roma', '_ordre' => 'poblacion'],
            DireccionField::class,
            ['poblacion', 'pais'],
        );

        $esperado = Criteria::create()
            ->where(Filter::matchesUnaccented(DireccionField::Poblacion, 'Roma'))
            ->orderBy(DireccionField::Poblacion);

        $this->assertEquals($esperado, $criterio);
    }

    /**
     * @param array<string, mixed> $filtros
     * @param class-string $campos
     * @param list<string> $textoSinAcentos
     */
    private function criterio(array $filtros, string $campos, array $textoSinAcentos): Criteria
    {
        $metodo = new ReflectionMethod(UbisTablaData::class, 'criterioDesdeFiltros');
        $objeto = (new \ReflectionClass(UbisTablaData::class))->newInstanceWithoutConstructor();

        return $metodo->invoke($objeto, $filtros, $campos, $textoSinAcentos);
    }
}
