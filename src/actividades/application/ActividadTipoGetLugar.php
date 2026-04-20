<?php

namespace src\actividades\application;

/**
 * Devuelve el HTML del desplegable de lugares posibles. Portado del case
 * `lugar` del dispatcher legacy.
 */
class ActividadTipoGetLugar
{
    public function execute(array $input = []): string
    {
        $Qentrada = (string)($input['entrada'] ?? '');
        $Qisfsv = (int)($input['isfsv'] ?? 0);
        $Qssfsv = (string)($input['ssfsv'] ?? '');
        $Qopcion_sel = (string)($input['opcion_sel'] ?? '');

        $oActividadLugar = new ActividadLugar();
        $oActividadLugar->setIsfsv($Qisfsv);
        $oActividadLugar->setSsfsv($Qssfsv);
        $oActividadLugar->setOpcion_sel($Qopcion_sel);

        $oDesplegableCasas = $oActividadLugar->getLugaresPosibles($Qentrada);

        return $oDesplegableCasas->desplegable();
    }
}
