<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadVerDatos;
use src\actividades\domain\value_objects\NivelStgrId;

/**
 * {@see ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad} (sin contenedor ni BBDD).
 */
final class ActividadVerDatosNivelStgrTest extends TestCase
{
    public function test_vacio_es_sin_estudios(): void
    {
        $this->assertSame(NivelStgrId::N, ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad(''));
    }

    public function test_ca_repaso_es_repaso(): void
    {
        $this->assertSame(NivelStgrId::R, ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad('1424000'));
    }

    public function test_ca_est_y_semestre_son_cuadrienio_anio_i(): void
    {
        $this->assertSame(NivelStgrId::C1, ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad('1422000'));
        $this->assertSame(NivelStgrId::C1, ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad('1423000'));
    }

    public function test_tipo_generico_sin_reglas_especials_es_sin_estudios(): void
    {
        $this->assertSame(NivelStgrId::N, ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad('1410000'));
    }
}
