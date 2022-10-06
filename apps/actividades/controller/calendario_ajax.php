<?php

use actividades\model\entity\Actividad;
use actividades\model\entity\GestorNivelStgr;
use actividades\model\entity\GestorRepeticion;
use actividadtarifas\model\entity\GestorTipoTarifa;
use actividadtarifas\model\entity\TipoTarifa;
use core\ConfigGlobal;
use ubis\model\entity\GestorDelegacion;
use ubis\model\entity\Tarifa;
use ubis\model\entity\Ubi;
use web\Desplegable;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

$Qmodelo = (integer)filter_input(INPUT_POST, 'modelo');
switch ($Qmodelo) {
    case 2:
        $print = 1;
    case 1:
    default:
        include_once(core\ConfigGlobal::$dir_estilos . '/calendario.css.php');
        //include_once('apps/web/calendario.php');
        break;
    case 3:
        include_once(core\ConfigGlobal::$dir_estilos . '/calendario_grid.css.php');
        include_once('apps/web/calendario_grid.php');
        break;
}
// para los estilos. Las variables están en la página css.
$oPlanning = new web\Planning();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);

switch ($Qque) {
    case "get":
        $Qdd = (integer)filter_input(INPUT_POST, 'dd');
        $Qcabecera = (string)filter_input(INPUT_POST, 'cabecera');
        $QsIniPlanning = (string)filter_input(INPUT_POST, 'sIniPlanning');
        $QsFinPlanning = (string)filter_input(INPUT_POST, 'sFinPlanning');
        $Qsactividades = (string)filter_input(INPUT_POST, 'sactividades');
        $Qmod = (string)filter_input(INPUT_POST, 'mod');
        $Qnueva = (string)filter_input(INPUT_POST, 'nueva');
        $Qdoble = (string)filter_input(INPUT_POST, 'doble');

        $Qa_actividades = unserialize(base64_decode($Qsactividades));
        $QoIniPlanning = unserialize(base64_decode($QsIniPlanning));
        $QoFinPlanning = unserialize(base64_decode($QsFinPlanning));

        /* TODO: comprobar que tiene permiso para crear algo. Sino: $Qmod = 0;
         *
         */

        $oPlanning->setDd($Qdd);
        $oPlanning->setCabecera($Qcabecera);
        $oPlanning->setInicio($QoIniPlanning);
        $oPlanning->setFin($QoFinPlanning);
        $oPlanning->setActividades($Qa_actividades);
        $oPlanning->setMod($Qmod);
        $oPlanning->setNueva($Qnueva);
        $oPlanning->setDoble($Qdoble);

        echo $oPlanning->dibujar();
        break;
    case 'modificar':
        $obj = 'actividades\\\\model\\\\entity\\\\ActividadDl';
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');

        $permiso_des = FALSE;
        if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
            $permiso_des = TRUE;
        }

        $oActividad = new actividades\model\entity\Actividad($Qid_activ);
        $a_status = $oActividad->getArrayStatus();

        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dl_org = $oActividad->getDl_org();
        $nom_activ = $oActividad->getNom_activ();
        $id_ubi = $oActividad->getId_ubi();
        //$desc_activ = $oActividad->['desc_activ'];
        $f_ini = $oActividad->getF_ini()->getFromLocal();
        $h_ini = $oActividad->getH_ini();
        $f_fin = $oActividad->getF_fin()->getFromLocal();
        $h_fin = $oActividad->getH_fin();
        //$tipo_horario = $oActividad->['tipo_horario'];
        $precio = $oActividad->getPrecio();
        //$num_asistentes = $oActividad->['num_asistentes'];
        $status = $oActividad->getStatus();
        $observ = $oActividad->getObserv();
        $nivel_stgr = $oActividad->getNivel_stgr();
        //$observ_material = $oActividad->['observ_material'];
        $lugar_esp = $oActividad->getLugar_esp();
        $tarifa = $oActividad->getTarifa();
        $id_repeticion = $oActividad->getId_repeticion();
        $publicado = $oActividad->getPublicado();
        $plazas = $oActividad->getPlazas();

        // mirar permisos.
        //if(core\ConfigGlobal::is_app_installed('procesos')) {
        $_SESSION['oPermActividades']->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
        $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

        if ($oPermActiv->only_perm('ocupado')) {
            die();
        }

        if ($oPermActiv->have_perm_activ('ver') === TRUE) {
            $mod = "ver";
            if ($oPermActiv->have_perm_activ('modificar') === TRUE) {
                $mod = "editar";
            }
        }

        $oTipoActiv = new web\TiposActividades($id_tipo_activ);
        $ssfsv = $oTipoActiv->getSfsvText();
        $sasistentes = $oTipoActiv->getAsistentesText();
        $sactividad = $oTipoActiv->getActividadText();
        $snom_tipo = $oTipoActiv->getNom_tipoText();
        $isfsv = $oTipoActiv->getSfsvId();


        if (!empty($id_ubi) && $id_ubi != 1) {
            $oCasa = Ubi::newUbi($id_ubi);
            $nombre_ubi = $oCasa->getNombre_ubi();
            $delegacion = $oCasa->getDl();
            $region = $oCasa->getRegion();
            $sv = $oCasa->getSv();
            $sf = $oCasa->getSf();
        } else {
            if ($id_ubi == 1 && $lugar_esp) $nombre_ubi = $lugar_esp;
            if (!$id_ubi && !$lugar_esp) $nombre_ubi = _("sin determinar");
        }

        $oGesDl = new GestorDelegacion();
        $oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones();
        $oDesplDelegacionesOrg->setNombre('dl_org');
        $oDesplDelegacionesOrg->setOpcion_sel($dl_org);

        $oGesTipoTarifa = new GestorTipoTarifa();
        $oDesplPosiblesTipoTarifas = $oGesTipoTarifa->getListaTipoTarifas($isfsv);
        $oDesplPosiblesTipoTarifas->setNombre('tarifa');
        $oDesplPosiblesTipoTarifas->setOpcion_sel($tarifa);

        $oGesNivelStgr = new GestorNivelStgr();
        $oDesplNivelStgr = $oGesNivelStgr->getListaNivelesStgr();
        $oDesplNivelStgr->setNombre('nivel_stgr');
        $oDesplNivelStgr->setOpcion_sel($nivel_stgr);

        $oGesRepeticion = new GestorRepeticion();
        $oDesplRepeticion = $oGesRepeticion->getListaRepeticion();
        $oDesplRepeticion->setNombre('id_repeticion');
        $oDesplRepeticion->setOpcion_sel($id_repeticion);

        $oHash = new web\Hash();
        $camposForm = 'dl_org!f_fin!f_ini!h_fin!h_ini!iactividad_val!iasistentes_val!id_repeticion!id_ubi!inom_tipo_val!isfsv_val!lugar_esp!nivel_stgr!nom_activ!nombre_ubi!observ!plazas!precio!publicado!status!tarifa';
        $camposNo = 'id_tipo_activ!mod';
        $a_camposHidden = array(
            'id_tipo_activ' => $id_tipo_activ,
            'id_activ' => $Qid_activ,
            'ssfsv' => $ssfsv,
        );
        $oHash->setArraycamposHidden($a_camposHidden);
        $oHash->setcamposForm($camposForm);
        $oHash->setCamposNo($camposNo);

        $oHash1 = new web\Hash();
        $oHash1->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividades/controller/actividad_select_ubi.php');
        $oHash1->setCamposForm('dl_org!ssfsv');
        $h = $oHash1->linkSinVal();

        $oActividadTipo = new actividades\model\ActividadTipo();
        $oActividadTipo->setId_tipo_activ($id_tipo_activ);
        $oActividadTipo->setAsistentes($sasistentes);
        $oActividadTipo->setActividad($sactividad);
        $oActividadTipo->setNom_tipo($snom_tipo);

        $procesos_installed = core\ConfigGlobal::is_app_installed('procesos');

        $status_txt = $a_status[$status];

        $accion = '';
        $a_campos = [
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'h' => $h,
            'obj' => $obj,
            'oPermActiv' => $oPermActiv,
            'mod' => $mod,
            'permiso_des' => $permiso_des,
            'accion' => $accion,
            'sasistentes' => $sasistentes,
            'sactividad' => $sactividad,
            'snom_tipo' => $snom_tipo,
            'ssfsv' => $ssfsv,
            'status' => $status,
            'status_txt' => $status_txt,
            'nom_activ' => $nom_activ,
            'f_ini' => $f_ini,
            'h_ini' => $h_ini,
            'f_fin' => $f_fin,
            'h_fin' => $h_fin,
            'oDesplDelegacionesOrg' => $oDesplDelegacionesOrg,
            'plazas' => $plazas,
            'nombre_ubi' => $nombre_ubi,
            'id_ubi' => $id_ubi,
            'lugar_esp' => $lugar_esp,
            'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
            'precio' => $precio,
            'observ' => $observ,
            'oDesplRepeticion' => $oDesplRepeticion,
            'oDesplNivelStgr' => $oDesplNivelStgr,
            'publicado' => $publicado,
            'oActividadTipo' => $oActividadTipo,
            'id_tipo_activ' => $id_tipo_activ,
            'web' => core\ConfigGlobal::getWeb(),
            'web_icons' => core\ConfigGlobal::getWeb_icons(),
            'procesos_installed' => $procesos_installed,
        ];

        $oView = new core\ViewTwig('actividades/controller');
        echo $oView->render('calendario_form_actividad.html.twig', $a_campos);
        break;
    case 'nueva':
        $obj = 'actividades\\\\model\\\\entity\\\\ActividadDl';
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

        $permiso_des = FALSE;
        if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
            $permiso_des = TRUE;
        }

        $oActividad = new Actividad();
        $a_status = $oActividad->getArrayStatus();

        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dl_org = $oActividad->getDl_org();
        $nom_activ = $oActividad->getNom_activ();
        $id_ubi = $oActividad->getId_ubi();
        //$desc_activ = $oActividad->['desc_activ'];
        $f_ini = $oActividad->getF_ini()->getFromLocal();
        $h_ini = $oActividad->getH_ini();
        $f_fin = $oActividad->getF_fin()->getFromLocal();
        $h_fin = $oActividad->getH_fin();
        //$tipo_horario = $oActividad->['tipo_horario'];
        $precio = $oActividad->getPrecio();
        //$num_asistentes = $oActividad->['num_asistentes'];
        $status = $oActividad->getStatus();
        $observ = $oActividad->getObserv();
        $nivel_stgr = $oActividad->getNivel_stgr();
        //$observ_material = $oActividad->['observ_material'];
        $lugar_esp = $oActividad->getLugar_esp();
        $tarifa = $oActividad->getTarifa();
        $id_repeticion = $oActividad->getId_repeticion();
        $publicado = $oActividad->getPublicado();
        $plazas = $oActividad->getPlazas();

        $oTipoActiv = new web\TiposActividades($id_tipo_activ);
        $ssfsv = $oTipoActiv->getSfsvText();
        $sasistentes = $oTipoActiv->getAsistentesText();
        $sactividad = $oTipoActiv->getActividadText();
        $snom_tipo = $oTipoActiv->getNom_tipoText();
        $isfsv = $oTipoActiv->getSfsvId();

        $isfsv = empty($isfsv) ? core\ConfigGlobal::mi_sfsv() : $isfsv;

        // valores por defecto:
        $status = 1;
        $dl_org = ConfigGlobal::mi_delef();
        $id_ubi = $Qid_ubi;

        if (!empty($id_ubi) && $id_ubi != 1) {
            $oCasa = Ubi::newUbi($id_ubi);
            $nombre_ubi = $oCasa->getNombre_ubi();
            $delegacion = $oCasa->getDl();
            $region = $oCasa->getRegion();
            $sv = $oCasa->getSv();
            $sf = $oCasa->getSf();
        } else {
            if ($id_ubi == 1 && $lugar_esp) {
                $nombre_ubi = $lugar_esp;
            }
            if (!$id_ubi && !$lugar_esp) {
                $nombre_ubi = _("sin determinar");
            }
        }

        $oGesDl = new GestorDelegacion();
        $oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones();
        $oDesplDelegacionesOrg->setNombre('dl_org');
        $oDesplDelegacionesOrg->setOpcion_sel($dl_org);

        $oGesTipoTarifa = new GestorTipoTarifa();
        $oDesplPosiblesTipoTarifas = $oGesTipoTarifa->getListaTipoTarifas($isfsv);
        $oDesplPosiblesTipoTarifas->setNombre('tarifa');
        $oDesplPosiblesTipoTarifas->setOpcion_sel($tarifa);

        $oGesNivelStgr = new GestorNivelStgr();
        $oDesplNivelStgr = $oGesNivelStgr->getListaNivelesStgr();
        $oDesplNivelStgr->setNombre('nivel_stgr');
        $oDesplNivelStgr->setOpcion_sel($nivel_stgr);

        $oGesRepeticion = new GestorRepeticion();
        $oDesplRepeticion = $oGesRepeticion->getListaRepeticion();
        $oDesplRepeticion->setNombre('id_repeticion');
        $oDesplRepeticion->setOpcion_sel($id_repeticion);

        $oHash = new web\Hash();
        $camposForm = 'dl_org!f_fin!f_ini!h_fin!h_ini!iactividad_val!iasistentes_val!id_repeticion!id_ubi!inom_tipo_val!isfsv_val!lugar_esp!nivel_stgr!nom_activ!nombre_ubi!observ!plazas!precio!publicado!status!tarifa';
        $camposNo = 'id_tipo_activ!mod';
        $a_camposHidden = array(
            'id_tipo_activ' => $id_tipo_activ,
            'id_ubi' => $Qid_ubi,
            'ssfsv' => $ssfsv,
        );
        $oHash->setArraycamposHidden($a_camposHidden);
        $oHash->setcamposForm($camposForm);
        $oHash->setCamposNo($camposNo);

        $oHash1 = new web\Hash();
        $oHash1->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividades/controller/actividad_select_ubi.php');
        $oHash1->setCamposForm('dl_org!ssfsv!isfsv');
        $h = $oHash1->linkSinVal();

        $oActividadTipo = new actividades\model\ActividadTipo();
        $oActividadTipo->setId_tipo_activ($id_tipo_activ);
        $oActividadTipo->setAsistentes($sasistentes);
        $oActividadTipo->setActividad($sactividad);
        $oActividadTipo->setNom_tipo($snom_tipo);

        $procesos_installed = core\ConfigGlobal::is_app_installed('procesos');

        $status_txt = $a_status[$status];

        $accion = '';
        $a_campos = [
            'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'h' => $h,
            'obj' => $obj,
            'permiso_des' => $permiso_des,
            'accion' => $accion,
            'sasistentes' => $sasistentes,
            'sactividad' => $sactividad,
            'snom_tipo' => $snom_tipo,
            'ssfsv' => $ssfsv,
            'status' => $status,
            'status_txt' => $status_txt,
            'nom_activ' => $nom_activ,
            'f_ini' => $f_ini,
            'h_ini' => $h_ini,
            'f_fin' => $f_fin,
            'h_fin' => $h_fin,
            'oDesplDelegacionesOrg' => $oDesplDelegacionesOrg,
            'plazas' => $plazas,
            'nombre_ubi' => $nombre_ubi,
            'id_ubi' => $id_ubi,
            'lugar_esp' => $lugar_esp,
            'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
            'precio' => $precio,
            'observ' => $observ,
            'oDesplRepeticion' => $oDesplRepeticion,
            'oDesplNivelStgr' => $oDesplNivelStgr,
            'publicado' => $publicado,
            'oActividadTipo' => $oActividadTipo,
            'id_tipo_activ' => $id_tipo_activ,
            'web' => core\ConfigGlobal::getWeb(),
            'web_icons' => core\ConfigGlobal::getWeb_icons(),
            'procesos_installed' => $procesos_installed,
        ];

        $oView = new core\ViewTwig('actividades/controller');
        echo $oView->render('calendario_form_actividad.html.twig', $a_campos);
        break;
    case "update":
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        $Qyear = (integer)filter_input(INPUT_POST, 'year');
        $Qid_tarifa = (integer)filter_input(INPUT_POST, 'id_tarifa');
        $Qcantidad = (string)filter_input(INPUT_POST, 'cantidad');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');

        if (!empty($Qid_item)) {
            $oTarifa = new Tarifa();
            $oTarifa->setId_item($Qid_item);
            $oTarifa->DBCarregar(); //perque agafi els valors que ja té.
        } else {
            $oTarifa = new Tarifa();
        }
        if (!empty($Qid_ubi)) $oTarifa->setId_ubi($Qid_ubi);
        if (!empty($Qyear)) $oTarifa->setYear($Qyear);
        if (!empty($Qid_tarifa)) $oTarifa->setId_tarifa($Qid_tarifa);
        if (!empty($Qcantidad)) $oTarifa->setCantidad($Qcantidad);
        if (!empty($Qobserv)) $oTarifa->setObserv($Qobserv);
        if ($oTarifa->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oTarifa->getErrorTxt();
        }
        break;
    case "borrar":
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        if (!empty($Qid_item)) {
            $oTarifa = new Tarifa();
            $oTarifa->setId_item($Qid_item);
            if ($oTarifa->DBEliminar() === false) {
                echo _("hay un error, no se ha eliminado");
                echo "\n" . $oTarifa->getErrorTxt();
            }
        } else {
            $Qque = (string)filter_input(INPUT_POST, 'que');
            $error_txt = _("no sé cuál he de borar");
            echo "{ que: '" . $Qque . "', error: '$error_txt' }";
        }
        break;
    case "update_inc":
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        $Qyear = (integer)filter_input(INPUT_POST, 'year');
        foreach ($_POST['inc_cantidad'] as $key => $cantidad) {
            $tarifa = strtok($key, '#');
            $id_item = (integer)strtok('#');
            $cantidad = round($cantidad);
            if (empty($id_item) && empty($cantidad)) continue; // no hay ni habia nada.
            $oTarifa = new Tarifa(array('id_tarifa' => $id_tarifa, 'id_ubi' => $Qid_ubi, 'year' => $Qyear));
            $oTarifa->DBCarregar();
            if (isset($cantidad)) $oTarifa->setCantidad($cantidad);
            if ($oTarifa->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oTarifa->getErrorTxt();
            }
        }
        break;
    case 'tar_form':
        $Qid_tarifa = (string)filter_input(INPUT_POST, 'id_tarifa');
        if ($Qid_tarifa == 'nuevo') {
            $letra = '';
            $modo = 0;
            $observ = '';
        } else {
            $oTipoTarifa = new TipoTarifa($Qid_tarifa);
            $oTipoTarifa->DBCarregar();
            $letra = $oTipoTarifa->getLetra();
            $modo = $oTipoTarifa->getModo();
            $observ = $oTipoTarifa->getObserv();
        }
        $a_opciones = array(0 => _("por dia"), 1 => _("total"));
        $oDespl = new Desplegable('modo', $a_opciones, $modo, 0);

        $oHash = new web\Hash();
        $camposForm = 'letra!modo!observ';
        $oHash->setCamposNo('que');
        $a_camposHidden = array(
            'que' => 'tar_update',
            'id_tarifa' => $Qid_tarifa,
        );
        $oHash->setcamposForm($camposForm);
        $oHash->setArraycamposHidden($a_camposHidden);

        $txt = "<form id='frm_tarifa'>";
        $txt .= $oHash->getCamposHtml();
        $txt .= '<h3>' . _("tarifa") . '</h3>';
        $txt .= _("letra") . " <input type=text size=3 name=letra value=\"$letra\">";
        $txt .= '&nbsp;&nbsp;';
        $txt .= _("modo") . $oDespl->desplegable();
        $txt .= '<br>';
        $txt .= _("observaciones") . " <input type=text size=25 name=observ value=\"$observ\">";
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_tarifa','tar_update');\" >";
        $txt .= "<input type='button' value='" . _("eliminar") . "' onclick=\"fnjs_guardar('#frm_tarifa','tar_eliminar');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
        $txt .= "</form> ";
        echo $txt;
        break;
    case "tar_update":
        $Qid_tarifa = (string)filter_input(INPUT_POST, 'id_tarifa');
        $Qletra = (string)filter_input(INPUT_POST, 'letra');
        $Qmodo = (string)filter_input(INPUT_POST, 'modo');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');
        if ($Qid_tarifa == 'nuevo') {
            $oTipoTarifa = new TipoTarifa();
            // miro si soy sf/sv.
            $oTipoTarifa->setSfsv(ConfigGlobal::mi_sfsv());
        } else {
            $oTipoTarifa = new TipoTarifa($Qid_tarifa);
            $oTipoTarifa->DBCarregar();
        }
        if (isset($Qletra)) $oTipoTarifa->setLetra($Qletra);
        if (isset($Qmodo)) $oTipoTarifa->setModo($Qmodo);
        if (isset($Qobserv)) $oTipoTarifa->setObserv($Qobserv);
        if ($oTipoTarifa->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oTipoTarifa->getErrorTxt();
        }
        break;
    case "tar_eliminar":
        $oTipoTarifa = new TipoTarifa($_POST['id_tarifa']);
        $oTipoTarifa->DBCarregar();
        if ($oTipoTarifa->DBEliminar() === false) {
            echo _("hay un error, no se ha borrado");
        }
        break;
}