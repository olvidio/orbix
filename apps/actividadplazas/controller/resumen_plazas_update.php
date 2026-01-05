<?php

use core\ConfigGlobal;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$que = (string)filter_input(INPUT_POST, 'que');

switch ($que) {
    case "ceder":
        $id_activ = (integer)filter_input(INPUT_POST, 'id_activ');
        $num_plazas = (integer)filter_input(INPUT_POST, 'num_plazas');
        $reg_dl = (string)filter_input(INPUT_POST, 'region_dl');

        $dl = substr($reg_dl, strpos($reg_dl, '-') + 1);

        $mi_dele = ConfigGlobal::mi_delef();
        // OJO, para sf todavía hay que quitar la f:
        if (ConfigGlobal::mi_sfsv() === 2) {
            $dl_sigla = substr($mi_dele, 0, -1);
        } else {
            $dl_sigla = $mi_dele;
        }

        // buscar el id de la dl
        $id_dl = 0;
        $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $dl_sigla]);
        if (is_array($cDelegaciones) && count($cDelegaciones)) {
            $id_dl = $cDelegaciones[0]->getIdDlVo()->value();
        }
        //Si es la dl_org, son plazas concedidas, sino pedidas.
        $ActvidadPlazasDlRepository = $GLOBALS['container']->get(ActividadPlazasDlRepositoryInterface::class);
        $cActividadPlazasDl = $ActvidadPlazasDlRepository->getActividadPlazas(['id_activ' => $id_activ, 'id_dl' => $id_dl, 'dl_tabla' => $mi_dele]);
        $oActividadPlazasDl = $cActividadPlazasDl[0];

        $aCedidas = $oActividadPlazasDl->getCedidas() ?? '';
        if (empty($aCedidas)) {
            $aCedidas = [];
        }
        if ($num_plazas === 0) {
            if (isset($aCedidas[$dl])) {
                unset($aCedidas[$dl]);
            }
        } else {
            $aCedidas[$dl] = $num_plazas;
        }
        $json_cedidas = json_encode($aCedidas);
        $oActividadPlazasDl->setCedidas($json_cedidas);

        //print_r($oActividadPlazasDl);
        if ($ActvidadPlazasDlRepository->Guardar($oActividadPlazasDl) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oActividadPlazasDl->getErrorTxt();
        }
        //$oPosicion = new web\Posicion();
        //echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$_POST['id_usuario']);
        break;
}