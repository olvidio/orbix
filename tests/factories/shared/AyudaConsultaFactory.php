<?php

declare(strict_types=1);

namespace Tests\factories\shared;

use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\value_objects\AyudaConsultaId;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory de {@see AyudaConsulta} para tests. La delegación lleva una marca propia
 * ({@see self::ESQUEMA}) para poder encontrar y limpiar solo las filas de prueba.
 */
class AyudaConsultaFactory
{
    public const ESQUEMA = 'T-test-ayuda';

    public function createSimple(?string $uuid = null): AyudaConsulta
    {
        $consulta = new AyudaConsulta();
        $consulta->setUuid_item($uuid ?? AyudaConsultaId::random()->value());
        $consulta->setF_consulta(new DateTimeLocal());
        $consulta->setPregunta('¿cómo apunto a una persona a una actividad?');
        $consulta->setEsquema(self::ESQUEMA);
        $consulta->setPantalla('frontend/asistentes/controller/form_asistentes.php');
        $consulta->setN_resultados(3);
        $consulta->setPuntuacion(12.5);
        $consulta->setFuente('guias/actividades/gestionar_asistentes.md');
        $consulta->setTitulo('Apuntar a una persona');

        return $consulta;
    }
}
