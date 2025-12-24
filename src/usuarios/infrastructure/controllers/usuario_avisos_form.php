<?php

//////////// Esto lo ven todos ////////////
// si no hay usuario, no puedo poner permisos.
use cambios\model\GestorAvisoCambios;
use core\ConfigGlobal;
use core\ViewPhtml;
use procesos\model\entity\ActividadFase;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\value_objects\AvisoTipoId;
use web\Hash;
use web\Lista;
use web\TiposActividades;

if ($Qquien === 'usuario') $obj = 'usuarios\\model\\entity\\Usuario';

if ((ConfigGlobal::is_app_installed('cambios')) && (!empty($Qid_usuario)) && ($Qquien === 'usuario')) {
    $a_status = StatusId::getArrayStatus();

    // avisos
    $CambioUsuariosObjetoPrefRepository = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
    $aWhere = ['id_usuario' => $Qid_usuario, '_ordre' => 'objeto, dl_org, id_tipo_activ_txt'];
    $aOperador = [];
    $cListaTablas = $CambioUsuariosObjetoPrefRepository->getCambioUsuarioObjetosPrefs($aWhere, $aOperador);

    // Tipos de avisos
    $aTipos_aviso = AvisoTipoId::getArrayAvisoTipo();
    // Nombre de los possibles objetos (que manejan la tablas) susceptibles de avisar.
    $aObjetos = GestorAvisoCambios::getArrayObjetosPosibles();

    $i = 0;
    $a_cabeceras_avisos = [
        _("objeto"),
        _("dl propia"),
        _("tipo de actividad"),
        _("fase ref."),
        _("off"),
        _("on"),
        _("outdate"),
        _("tipo de aviso"),
        _("propiedades"),
        _("valor"),
    ];
    $a_botones_avisos = [
        array('prefix' => 'av', 'txt' => _("modificar"), 'click' => "fnjs_mod_cambio(\"#avisos\")"),
        array('prefix' => 'av', 'txt' => _("eliminar"), 'click' => "fnjs_del_cambio(\"#avisos\")")
    ];
    $a_valores_avisos = [];
    $oFase = new ActividadFase();
    $CambioUsuarioPropiedadesPref = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
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
        $cListaPropiedades = $CambioUsuarioPropiedadesPref->getCambioUsuarioPropiedadesPrefs(['id_item_usuario_objeto' => $id_item_usuario_objeto]);
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

    $oTablaAvisos = new Lista();
    $oTablaAvisos->setId_tabla('usuario_form_avisos');
    $oTablaAvisos->setCabeceras($a_cabeceras_avisos);
    $oTablaAvisos->setBotones($a_botones_avisos);
    $oTablaAvisos->setDatos($a_valores_avisos);

    //$url_usuario_ajax = ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_ajax.php';
    $oHashAvisos = new Hash();
    //$oHashAvisos->setUrl($url_usuario_ajax);
    $oHashAvisos->setCamposNo('sel!scroll_id!salida');
    $a_camposHidden = array(
        'id_usuario' => $Qid_usuario,
        'quien' => $Qquien,
        'salida' => '',
    );
    $oHashAvisos->setArraycamposHidden($a_camposHidden);
    $oHashAvisos->setPrefix('av'); // prefijo par el id.
    $h1 = $oHashAvisos->linkSinVal();

    $a_camposAvisos = [
        'oPosicion' => $oPosicion,
        'oHashAvisos' => $oHashAvisos,
        'oTablaAvisos' => $oTablaAvisos,
    ];

    $oView = new ViewPhtml('cambios\controller');
    $oView->renderizar('usuario_form_avisos.phtml', $a_camposAvisos);
}