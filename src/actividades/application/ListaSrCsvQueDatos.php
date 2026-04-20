<?php

namespace src\actividades\application;

use core\ConfigGlobal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;

/**
 * Devuelve los valores por defecto del formulario `lista_sr_csv_que`,
 * a partir de la preferencia guardada del usuario (tipo 'busqueda_activ_sr').
 *
 * Concentra el acceso a `PreferenciaRepository` y evita que el controlador
 * frontend toque `src/`.
 */
final class ListaSrCsvQueDatos
{
    public function ejecutar(): array
    {
        $id_usuario = ConfigGlobal::mi_id_usuario();
        $tipo = 'busqueda_activ_sr';
        $PreferenciaRepository = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
        $oPreferencia = $PreferenciaRepository->findById($id_usuario, $tipo);
        $json_busqueda = $oPreferencia !== null ? $oPreferencia->getPreferencia() : '';
        $oBusqueda = json_decode($json_busqueda);

        if (is_object($oBusqueda)) {
            $a_status = json_decode($oBusqueda->status);
            $periodo = (string)$oBusqueda->periodo;
            $a_tipo_activ = json_decode($oBusqueda->tipo_activ);
            $a_ubis = json_decode($oBusqueda->ubis_compartidos);
            $sel_ubis = implode(',', (array)$a_ubis);
        } else {
            $a_status = [1, 2];
            $periodo = 'curso_ca';
            $a_tipo_activ = [1, 3];
            $a_ubis = [];
            $sel_ubis = '';
        }

        $chk_status_1 = '';
        $chk_status_2 = '';
        foreach ((array)$a_status as $val) {
            if ((int)$val === 1) {
                $chk_status_1 = 'checked';
            }
            if ((int)$val === 2) {
                $chk_status_2 = 'checked';
            }
        }

        $chk_activ_crt = '';
        $chk_activ_cv = '';
        foreach ((array)$a_tipo_activ as $tipo_activ) {
            if ((int)$tipo_activ === 1) {
                $chk_activ_crt = 'checked';
            }
            if ((int)$tipo_activ === 3) {
                $chk_activ_cv = 'checked';
            }
        }

        return [
            'periodo' => $periodo,
            'sel_ubis' => $sel_ubis,
            'chk_status_1' => $chk_status_1,
            'chk_status_2' => $chk_status_2,
            'chk_activ_crt' => $chk_activ_crt,
            'chk_activ_cv' => $chk_activ_cv,
        ];
    }
}
