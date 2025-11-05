<?php


// INICIO Cabecera global de URL de controlador *********************************
use actividades\model\entity\ActividadAll;
use cambios\model\entity\CambioUsuarioObjetoPref;
use cambios\model\entity\GestorCambioUsuarioObjetoPref;
use cambios\model\entity\GestorCambioUsuarioPropiedadPref;
use cambios\model\GestorAvisoCambios;
use core\ConfigGlobal;
use procesos\model\entity\ActividadFase;
use src\usuarios\application\repositories\UsuarioRepository;
use web\ContestarJson;
use web\TiposActividades;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');


$error_txt = '';
$nombre_usuario = '';

if ((ConfigGlobal::is_app_installed('cambios')) && (!empty($Qid_usuario)) && ($Qquien === 'usuario')) {
    // mando también el nombre del usuario
    $UsuarioRepository = new UsuarioRepository();
    $oUsuario = $UsuarioRepository->findById($Qid_usuario);
    $nombre_usuario = $oUsuario->getUsuarioAsString();

    $oActividad = new ActividadAll();
    $a_status = $oActividad->getArrayStatus();

    // avisos
    $oGesCambiosUsuariosObjeto = new GestorCambioUsuarioObjetoPref();
    $aWhere = ['id_usuario' => $Qid_usuario, '_ordre' => 'objeto, dl_org, id_tipo_activ_txt'];
    $aOperador = [];
    $cListaTablas = $oGesCambiosUsuariosObjeto->getCambioUsuarioObjetosPrefs($aWhere, $aOperador);

    // Tipos de avisos
    $aTipos_aviso = CambioUsuarioObjetoPref::getTipos_aviso();
    // Nombre de los possibles objetos (que manejan la tablas) susceptibles de avisar.
    $aObjetos = GestorAvisoCambios::getArrayObjetosPosibles();

    $i = 0;
    $a_valores_avisos = [];
    $oFase = new ActividadFase();
    foreach ($cListaTablas as $oCambioUsuarioObjetoPref) {
        $i++;

        $id_item_usuario_objeto = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
        $id_tipo = $oCambioUsuarioObjetoPref->getId_tipo_activ_txt();
        $dl_org = $oCambioUsuarioObjetoPref->getDl_org();
        $objeto = $oCambioUsuarioObjetoPref->getObjeto();
        $aviso_tipo = $oCambioUsuarioObjetoPref->getAviso_tipo();
        $id_fase_ref = $oCambioUsuarioObjetoPref->getId_fase_ref();
        $aviso_off = $oCambioUsuarioObjetoPref->getAviso_off();
        $aviso_on = $oCambioUsuarioObjetoPref->getAviso_on();
        $aviso_outdate = $oCambioUsuarioObjetoPref->getAviso_outdate();

        $isfsv = substr($id_tipo, 0, 1);
        $mi_dele = ConfigGlobal::mi_delef($isfsv);
        if ($dl_org != $mi_dele) {
            $dl_org = _("otras");
        }

        $oTipoActividad = new TiposActividades($oCambioUsuarioObjetoPref->getId_tipo_activ_txt());
        $objeto_txt = $aObjetos[$objeto];

        $a_valores_avisos[$i]['sel'] = "$Qid_usuario#$id_item_usuario_objeto";
        $a_valores_avisos[$i][1] = $objeto_txt;
        $a_valores_avisos[$i][2] = $dl_org;
        $a_valores_avisos[$i][3] = $oTipoActividad->getNom();
        $txt_fases = '';
        if (ConfigGlobal::is_app_installed('procesos')) {
            $oFase->setId_fase($id_fase_ref);
            $oFase->DBCarregar();
            $txt_fases .= empty($txt_fases) ? '' : ', ';
            $txt_fases .= $oFase->getDesc_fase();
        } else {
            $txt_fases .= empty($txt_fases) ? '' : ', ';
            $txt_fases .= $a_status[$id_fase_ref];
        }
        $a_valores_avisos[$i][4] = $txt_fases;
        $a_valores_avisos[$i][5] = $aviso_off;
        $a_valores_avisos[$i][6] = $aviso_on;
        $a_valores_avisos[$i][7] = $aviso_outdate;

        $a_valores_avisos[$i][8] = $aTipos_aviso[$aviso_tipo];
        $GesCambiosUsuarioPropiedadesPref = new GestorCambioUsuarioPropiedadPref();
        $cListaPropiedades = $GesCambiosUsuarioPropiedadesPref->getCambioUsuarioPropiedadesPrefs(array('id_item_usuario_objeto' => $id_item_usuario_objeto));
        $txt_cambio = '';
        $txt_propiedades = '';
        $c = 0;
        foreach ($cListaPropiedades as $oCambioUsuarioPropiedadPref) {
            $c++;
            $propiedad = $oCambioUsuarioPropiedadPref->getPropiedad();
            $operador = $oCambioUsuarioPropiedadPref->getOperador();
            $valor = $oCambioUsuarioPropiedadPref->getValor();
            $valor_old = $oCambioUsuarioPropiedadPref->getValor_old();
            $valor_new = $oCambioUsuarioPropiedadPref->getValor_new();
            if ($c > 1) {
                $txt_propiedades .= ', ';
            }
            $txt_cambio .= empty($txt_cambio) ? '' : ', ';
            $txt_propiedades .= $propiedad;
            $txt_cambio .= $oCambioUsuarioPropiedadPref->getTextCambio();

        }
        $a_valores_avisos[$i][9] = $txt_propiedades;
        $a_valores_avisos[$i][10] = $txt_cambio;
    }
} else {
    $error_txt = _("No tiene permiso");
}

$data = [
    'a_valores' => $a_valores_avisos,
    'nombre_usuario' => $nombre_usuario,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);