<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use encargossacd\model\EncargoFunciones;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoSacd;
use encargossacd\model\entity\GestorEncargoTexto;
use personas\model\entity\PersonaDl;
use src\ubis\application\repositories\CentroDlRepository;
use src\ubis\application\repositories\CentroEllasRepository;
use web\DateTimeLocal;

/**
 * Esta página muestra los encargos de un ctr.
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        7/3/07.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qsfsv = (string)filter_input(INPUT_POST, 'sfsv');

/* claves:
 *       "com_ctr";
 *       "t_colaborador"
 */
$oGesEncargoTextos = new GestorEncargoTexto();
$cEncargoTextos = $oGesEncargoTextos->getEncargoTextos();
$a_txt_comunicacion = [];
foreach ($cEncargoTextos as $oEncargoTexto) {
    $clave = $oEncargoTexto->getClave();
    $idioma = $oEncargoTexto->getIdioma();
    $texto = $oEncargoTexto->getTexto();
    $a_txt_comunicacion[$idioma][$clave] = $texto;
}

$oDateLocal = new DateTimeLocal();
$hoy_local = $oDateLocal->getFromLocal('.');

// ciudad de la dl
$oEncargoFunciones = new EncargoFunciones();
$poblacion = $oEncargoFunciones->getLugar_dl();
$lugar_fecha = "$poblacion, $hoy_local";

// los ctr
switch ($Qsfsv) {
    case "sv":
        $GesCentros = new CentroDlRepository();
        $cCentros = $GesCentros->getCentros(array('status' => 't', '_ordre' => 'tipo_ctr,nombre_ubi'));
        $origen_txt = ConfigGlobal::mi_dele();
        break;
    case "sf":
        $GesCentros = new CentroEllasRepository();
        $cCentros = $GesCentros->getCentros(array('status' => 't', '_ordre' => 'tipo_ctr,nombre_ubi'));
        $origen_txt = ConfigGlobal::mi_dele() . 'f';
        break;
}
$c = 0;
$array_atn_sacd = [];
foreach ($cCentros as $oCentro) {
    $c++;
    $id_ubi = $oCentro->getId_ubi();
    $nombre_ubi = $oCentro->getNombre_ubi();

    /* busco los datos del encargo que se tengan, para los tipos de encargo de atención de centros: 100,1100,1200,1300,2100,2200,3000. */
    $GesEncargos = new GestorEncargo();
    $cEncargos = $GesEncargos->getEncargos(array('id_ubi' => $id_ubi, 'id_tipo_enc' => '(1|2|3).00'), array('id_tipo_enc' => '~'));
    if (is_array($cEncargos) && count($cEncargos) == 0) continue;
    if (is_array($cEncargos) && count($cEncargos) != 1) {
        echo _("sólo debería haber uno");
        continue;
    }
    $id_enc = $cEncargos[0]->getId_enc();
    if (empty($id_enc)) continue;
    // sacd
    $GesEncargoSacd = new GestorEncargoSacd();
    $cEncargosSacd = $GesEncargoSacd->getEncargosSacd(array('id_enc' => $id_enc, 'f_fin' => 'x', '_ordre' => 'modo'), array('f_fin' => 'IS NULL'));
    $sacd_colaborador = [];  // reset
    foreach ($cEncargosSacd as $oEncargoSacd) {
        $id_nom = $oEncargoSacd->getId_nom();
        $oPersona = new PersonaDl($id_nom);
        $modo = $oEncargoSacd->getModo();
        switch ($modo) {
            case 2: // titular del cl
            case 3: // titular no del cl
                $array_atn_sacd[$nombre_ubi]['titular'] = $oPersona->getNombreApellidos();
                // para saber la dedicación
                $dedicacion_txt = $oEncargoFunciones->dedicacion($id_nom, $id_enc);
                $array_atn_sacd[$nombre_ubi]['titular_dedicacion'] = $dedicacion_txt;

                break;
            case 4: // suplente
                $array_atn_sacd[$nombre_ubi]['suplente'] = $oPersona->getNombreApellidos();
                break;
            case 5: // colaborador
                $dedicacion_txt = $oEncargoFunciones->dedicacion($id_nom, $id_enc);
                $sacd_col = array('nom' => $oPersona->getNombreApellidos(), 'dedicacion' => $dedicacion_txt);
                $sacd_colaborador[] = $sacd_col;
                break;
        }
    }
    $array_atn_sacd[$nombre_ubi]['colaborador'] = $sacd_colaborador;
    $array_atn_sacd[$nombre_ubi]['txt']['com_ctr'] = $oEncargoFunciones->getTraduccion('com_ctr', $idioma);
}

$a_campos = ['oPosicion' => $oPosicion,
    'array_atn_sacd' => $array_atn_sacd,
    'origen_txt' => $origen_txt,
    'lugar_fecha' => $lugar_fecha,
];

$oView = new ViewPhtml('encargossacd\controller');
$oView->renderizar('listas_com_ctr.phtml', $a_campos);
