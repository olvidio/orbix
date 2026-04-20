<?php

namespace src\actividades\application;

/**
 * Devuelve el payload (id, opciones, selected, blanco) del desplegable de
 * lugares posibles para el frontend. El frontend construye el `<select>`.
 */
class ActividadTipoGetLugar
{
    /**
     * @param array $input
     * @return array{id: string, opciones: array<int|string,string>, selected: string, blanco: bool}
     */
    public function execute(array $input = []): array
    {
        $Qentrada = (string)($input['entrada'] ?? '');
        $Qisfsv = (int)($input['isfsv'] ?? 0);
        $Qssfsv = (string)($input['ssfsv'] ?? '');
        $Qopcion_sel = (string)($input['opcion_sel'] ?? '');

        $oActividadLugar = new ActividadLugar();
        $oActividadLugar->setIsfsv($Qisfsv);
        $oActividadLugar->setSsfsv($Qssfsv);
        $oActividadLugar->setOpcion_sel($Qopcion_sel);

        $opciones = $oActividadLugar->getLugaresPosibles($Qentrada);

        return [
            'id' => 'id_ubi',
            'opciones' => $opciones,
            'selected' => $Qopcion_sel,
            'blanco' => true,
        ];
    }
}
