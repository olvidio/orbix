<?php

namespace src\actividades\application;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\domain\helpers\OpcionesDesplegable;

/**
 * Devuelve el payload (id, opciones, selected, blanco) del desplegable de
 * lugares posibles para el frontend. El frontend construye el `<select>`.
 */
class ActividadTipoGetLugar
{
    public function __construct(
        private ActividadLugar $actividadLugar,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool}
     */
    public function execute(array $input = []): array
    {
        $Qentrada = input_string($input, 'entrada');
        $Qisfsv = input_int($input, 'isfsv');
        $Qssfsv = input_string($input, 'ssfsv');
        $Qopcion_sel = input_string($input, 'opcion_sel');

        $oActividadLugar = $this->actividadLugar;
        $oActividadLugar->setIsfsv($Qisfsv);
        $oActividadLugar->setSsfsv($Qssfsv);
        $oActividadLugar->setOpcion_sel($Qopcion_sel);

        $opciones = $oActividadLugar->getLugaresPosibles($Qentrada);

        return [
            'id' => 'id_ubi',
            'opciones' => OpcionesDesplegable::enOrden($opciones),
            'selected' => $Qopcion_sel,
            'blanco' => true,
        ];
    }
}
