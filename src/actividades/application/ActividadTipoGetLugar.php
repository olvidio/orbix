<?php

namespace src\actividades\application;

use src\shared\domain\helpers\OpcionesDesplegable;
use src\shared\domain\helpers\FuncTablasSupport;

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
        $Qentrada = FuncTablasSupport::inputString($input, 'entrada');
        $Qisfsv = FuncTablasSupport::inputInt($input, 'isfsv');
        $Qssfsv = FuncTablasSupport::inputString($input, 'ssfsv');
        $Qopcion_sel = FuncTablasSupport::inputString($input, 'opcion_sel');

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
