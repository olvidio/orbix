<?php

declare(strict_types=1);

namespace src\actividades\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;

/**
 * Devuelve en una sola respuesta TODO lo que necesita el espejo en frontend
 * {@see \frontend\actividades\helpers\TiposDeActividades} para funcionar sin
 * tocar el repositorio:
 *
 *  - `maps`: los 4 mapas estáticos texto→código del id_tipo_activ
 *    (sfsv, asistentes, actividad 1 dígito, actividad 2 dígitos). Vienen de
 *    las constantes públicas de {@see TiposActividades}, así no hay duplicación
 *    entre dominio y frontend.
 *  - `filas`: la lista plana `{id_tipo_activ, nombre}` de `a_tipos_actividad`
 *    para resolver los "posibles" en memoria.
 *
 * Pensado para una única request por petición de página: el loader frontend
 * cachea el payload completo en memoria.
 */
final class TipoActivMetadata
{
    /**
     * @return array{
     *     maps: array{
     *         sfsv: array<string, int|string>,
     *         asistentes: array<string, int|string>,
     *         actividad1digito: array<string, int|string>,
     *         actividad2digitos: array<string, int|string>,
     *     },
     *     filas: list<array{id_tipo_activ:int, nombre:string}>,
     * }
     */
    public function execute(): array
    {
        return [
            'maps' => [
                'sfsv' => TiposActividades::A_SFSV,
                'asistentes' => TiposActividades::A_ASISTENTES,
                'actividad1digito' => TiposActividades::A_ACTIVIDAD_1_DIGITO,
                'actividad2digitos' => TiposActividades::A_ACTIVIDAD_2_DIGITOS,
            ],
            'filas' => $this->cargarFilas(),
        ];
    }

    /**
     * @return list<array{id_tipo_activ:int, nombre:string}>
     */
    private function cargarFilas(): array
    {
        $repository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $cTiposDeActividades = $repository->getTiposDeActividades(['_ordre' => 'id_tipo_activ']);

        $filas = [];
        if (is_array($cTiposDeActividades)) {
            foreach ($cTiposDeActividades as $oTipo) {
                $filas[] = [
                    'id_tipo_activ' => (int)$oTipo->getId_tipo_activ(),
                    'nombre' => (string)$oTipo->getNombre(),
                ];
            }
        }

        return $filas;
    }
}
