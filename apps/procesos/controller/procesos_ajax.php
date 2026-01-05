<?php

use core\ConfigGlobal;
use src\actividades\domain\value_objects\StatusId;
use src\menus\domain\PermisoMenu;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use web\Desplegable;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');

function dibujar_tree(array $aPadres): string
{
    $html_tree = '<div id="tree">';
    ksort($aPadres);
    // supongo que el primero siempre es 0, (la fase previa)
    foreach ($aPadres[0] as $padre) {
        $id_fase_i = $padre['id'];
        $nom = $padre['nom'];
        // si tiene hijos: branch
        if (array_key_exists($id_fase_i, $aPadres)) {
            $html_tree .= '<div class="branch">';
            $html_tree .= '<div class="entry"><span>' . $nom . '</span>';
            $html_tree .= '<div class="branch">';
            $html_tree .= dibujar2($aPadres, $id_fase_i);
            $html_tree .= "</div>";
            $html_tree .= "</div>";
        } else {
            // si NO tiene hijos: entry
            $html_tree .= '<div class="entry"><span>' . $nom . '</span></div>';

        }
    }
    $html_tree .= '</div>'; //id="tree">';
    return $html_tree;
}

function dibujar2($aPadres, $id_fase)
{
    $html = '';
    foreach ($aPadres[$id_fase] as $padre) {
        $id_fase_i = $padre['id'];
        $nom = $padre['nom'];
        // si tiene hijos: branch
        if (array_key_exists($id_fase_i, $aPadres)) {
            $html .= '<div class="branch">';
            $html .= '<div class="entry"><span>' . $nom . '</span>';
            $html .= '<div class="branch">';
            $html .= dibujar2($aPadres, $id_fase_i);
            $html .= "</div>";
            $html .= "</div>";
        } else {
            // si NO tiene hijos: entry
            $html .= '<div class="entry"><span>' . $nom . '</span></div>';

        }
    }
    return $html;
}

switch ($Qque) {
    case 'regenerar':
        // para cada fase del proceso
        // mirar que actividades les falta y añadir.
        $Qid_tipo_proceso = (integer)filter_input(INPUT_POST, 'id_tipo_proceso');
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $Qid_tipo_proceso]);
        $i = 0;
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        foreach ($cTareasProceso as $oTareaProceso) {
            $i++;
            $id_item = $oTareaProceso->getId_item();
            $id_fase = $oTareaProceso->getId_fase();
            $id_tarea = $oTareaProceso->getId_tarea();
            $ActividadProcesoTareaRepository->añadirFaseTarea($Qid_tipo_proceso, $id_fase, $id_tarea);
        }
        // buscar las fases a eliminar
        $ActividadProcesoTareaRepository->borrarFaseTareaInexistente($Qid_tipo_proceso, $id_fase, $id_tarea);

        break;
    case 'clonar':
        $Qid_tipo_proceso = (integer)filter_input(INPUT_POST, 'id_tipo_proceso');
        $Qid_tipo_proceso_ref = (integer)filter_input(INPUT_POST, 'id_tipo_proceso_ref');

        // borrar lo anterior:
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso(array('id_tipo_proceso' => $Qid_tipo_proceso));
        foreach ($cTareasProceso as $oTareaProceso) {
            $TareaProcesoRepository->Eliminar($oTareaProceso);
        }
        // clonar
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso(array('id_tipo_proceso' => $Qid_tipo_proceso_ref));
        foreach ($cTareasProceso as $oTareaProceso) {
            $id_fase = $oTareaProceso->getId_fase();
            $id_tarea = $oTareaProceso->getId_tarea();
            $status = $oTareaProceso->getStatus();
            $id_of_responsable = $oTareaProceso->getId_of_responsable();
            $json_fases_previas = $oTareaProceso->getJson_fases_previas();

            $oTareaProceso->setId_tipo_proceso($Qid_tipo_proceso,);
            $newId_item = $TareaProcesoRepository->getNewId();
            $oTareaProceso->setId_item($newId_item);
            $TareaProcesoRepository->Guardar($oTareaProceso);
        }
    // Omito el break, para que a haga el get.
    case 'get':
        if (!isset($TareaProcesoRepository)) {
            $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        }
        if (!isset($Qid_tipo_proceso)) {
            $Qid_tipo_proceso = (integer)filter_input(INPUT_POST, 'id_tipo_proceso');
            $cTareasProceso = $TareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $Qid_tipo_proceso, '_ordre' => 'status,id_of_responsable']);
        }
        $a_status = StatusId::getArrayStatus();

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();
        $miSfsv = ConfigGlobal::mi_sfsv();

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        // para crear un desplegable de oficinas. Uso los de los menus
        $oPermMenus = new PermisoMenu;
        $aOpcionesOficinas = $oPermMenus->lista_array();

        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $soy = 3;
        } else {
            // filtro por sf/sv
            switch ($miSfsv) {
                case 1: // sv
                    $soy = 1;
                    break;
                case 2: //sf
                    $soy = 2;
                    break;
            }
        }

        $j = 0;
        $aPadres = [];
        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        foreach ($cTareasProceso as $oTareaProceso) {
            $j++;
            $clase = ($j % 2 == 0) ? 'tono2' : 'tono4';
            $id_item = $oTareaProceso->getId_item();
            $id_fase = $oTareaProceso->getId_fase();
            $status = $oTareaProceso->getStatus();
            $status_txt = $a_status[$status];
            $id_of_responsable = $oTareaProceso->getId_of_responsable();
            $responsable = empty($aOpcionesOficinas[$id_of_responsable]) ? '' : $aOpcionesOficinas[$id_of_responsable];

            $oFase = $ActividadFaseRepository->findById($id_fase);
            $fase = $oFase->getDesc_fase();
            $sf = ($oFase->isSf()) ? 2 : 0;
            $sv = ($oFase->isSv()) ? 1 : 0;
            //ojo, que puede ser las dos a la vez
            if (!(($soy & $sf) || ($soy & $sv))) {
                $j--;
                continue;
            }
            $oTarea = $ActividadTareaRepository->findById($oTareaProceso->getId_tarea());
            $tarea = $oTarea->getDesc_tarea();
            $tarea_txt = empty($tarea) ? '' : "($tarea)";
            $fase_previa = '';
            $tarea_previa_txt = '';
            $aFases_previas = $oTareaProceso->getJson_fases_previas(TRUE);
            $id_fase_previa = '';
            foreach ($aFases_previas as $oFaseP) {
                $id_fase_previa = $oFaseP['id_fase'];
                if (empty($id_fase_previa)) continue;
                //$id_tarea_previa = $oFaseP['id_tarea'];
                //$mensaje_requisito = $oFaseP['mensaje'];
                $oFase_previa = $ActividadFaseRepository->findById($id_fase_previa);
                $fase_previa .= empty($fase_previa) ? '' : ' ' . _("y") . ' ';
                $fase_previa .= $oFase_previa->getDesc_fase();
                $tarea_previa_txt = empty($tarea_previa) ? '' : "($tarea_previa)";
            }

            $id_fase_previa = empty($id_fase_previa) ? 0 : $id_fase_previa;

            $aPadres[$id_fase_previa][$j] = ['id' => $id_fase, 'nom' => $fase];

        }

        echo dibujar_tree($aPadres);
        break;
    case 'get_listado':
        $Qid_tipo_proceso = (integer)filter_input(INPUT_POST, 'id_tipo_proceso');
        $a_status =  StatusId::getArrayStatus();

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();
        $miSfsv = ConfigGlobal::mi_sfsv();

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();


        // para crear un desplegable de oficinas. Uso los de los menus
        $oPermMenus = new PermisoMenu;
        $aOpcionesOficinas = $oPermMenus->lista_array();

        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $soy = 3;
        } else {
            // filtro por sf/sv
            switch ($miSfsv) {
                case 1: // sv
                    $soy = 1;
                    break;
                case 2: //sf
                    $soy = 2;
                    break;
            }
        }

        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $Qid_tipo_proceso, '_ordre' => 'status,id_of_responsable']);
        $txt = '<table>';
        $txt .= '<tr><th>' . _("status") . '</th><th>' . _("responsable") . '</th>';
        $txt .= '<th colspan=3>' . _("fase - tarea") . '</th><th>' . _("modificar") . '</th><th>' . _("eliminar") . '</th></tr>';
        $i = 0;
        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        foreach ($cTareasProceso as $oTareaProceso) {
            $i++;
            $clase = ($i % 2 == 0) ? 'tono2' : 'tono4';
            $id_item = $oTareaProceso->getId_item();
            $status = $oTareaProceso->getStatus();
            $status_txt = $a_status[$status];
            $id_of_responsable = $oTareaProceso->getId_of_responsable();
            $responsable = empty($aOpcionesOficinas[$id_of_responsable]) ? '' : $aOpcionesOficinas[$id_of_responsable];

            $id_fase = $oTareaProceso->getId_fase();
            $oFase = $ActividadFaseRepository->findById($id_fase);
            $fase = $oFase->getDesc_fase();
            $sf = ($oFase->isSf()) ? 2 : 0;
            $sv = ($oFase->isSv()) ? 1 : 0;
            //ojo, que puede ser las dos a la vez
            if (!(($soy & $sf) || ($soy & $sv))) {
                $i--;
                continue;
            }
            $id_tarea = $oTareaProceso->getId_tarea();
            $oTarea = $ActividadTareaRepository->findById($id_tarea);
            $tarea = $oTarea->getDesc_tarea();
            $tarea_txt = empty($tarea) ? '' : "($tarea)";
            $fase_previa = '';
            $tarea_previa_txt = '';
            $aFases_previas = $oTareaProceso->getJson_fases_previas(TRUE);
            foreach ($aFases_previas as $oFaseP) {
                $id_fase_previa = $oFaseP['id_fase'];
                if (empty($id_fase_previa)) continue;
                //$id_tarea_previa = $oFaseP['id_tarea'];
                //$mensaje_requisito = $oFaseP['mensaje'];
                $oFase_previa = $ActividadFaseRepository->findById($id_fase_previa);
                $fase_previa .= empty($fase_previa) ? '' : ' ' . _("y") . ' ';
                $fase_previa .= $oFase_previa->getDesc_fase();
                $tarea_previa_txt = empty($tarea_previa) ? '' : "($tarea_previa)";
            }

            $mod = "<span class=link onclick=fnjs_modificar($id_item) title='" . _("modificar") . "' >" . _("modificar") . "</span>";
            $drop = "<span class=link onclick=fnjs_eliminar($id_item) title='" . _("eliminar") . "' >" . _("eliminar") . "</span>";

            $txt .= "<tr class=$clase><td>($status_txt)</td><td>$responsable</td><td colspan=3>$fase $tarea_txt</td><td>$mod</td><td>$drop</td></tr>";
            $txt .= "<tr><td></td><td></td><td>&nbsp;&nbsp;&nbsp;" . _("requisito") . ":</td><td>$fase_previa $tarea_previa_txt</td></tr>";
        }
        $txt .= '</table>';
        echo $txt;
        break;
    case 'depende':
        $Qacc = (string)filter_input(INPUT_POST, 'acc');
        $Qvalor_depende = (string)filter_input(INPUT_POST, 'valor_depende');
        //caso de actualizar el campo depende
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        if ($Qacc === '#id_tarea') {
            $aOpciones = $ActividadTareaRepository->getArrayActividadTareas($Qvalor_depende);
            $oDesplegable = new Desplegable();
            $oDesplegable->setOpciones($aOpciones);
            $oDesplegable->setBlanco(true);
            if (is_object($oDesplegable)) {
                echo $oDesplegable->options();
            } else {
                echo "";
            }
        }
        if ($Qacc === '#id_tarea_previa') {
            $aOpciones = $ActividadTareaRepository->getArrayActividadTareas($Qvalor_depende);
            $oDesplegable = new Desplegable();
            $oDesplegable->setOpciones($aOpciones);
            $oDesplegable->setBlanco(true);
            echo $oDesplegable->options();
        }
        break;
    case 'update':
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qid_tipo_proceso = (integer)filter_input(INPUT_POST, 'id_tipo_proceso');
        $Qstatus = (integer)filter_input(INPUT_POST, 'status');
        $Qid_of_responsable = (integer)filter_input(INPUT_POST, 'id_of_responsable');
        $Qid_fase = (integer)filter_input(INPUT_POST, 'id_fase');
        $Qid_tarea = (integer)filter_input(INPUT_POST, 'id_tarea');
        // arrays
        $Qid_fase_previa = (array)filter_input(INPUT_POST, 'id_fase_previa', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $Qid_tarea_previa = (array)filter_input(INPUT_POST, 'id_tarea_previa', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $Qmensaje_requisito = (array)filter_input(INPUT_POST, 'mensaje_requisito', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $aFases_previas = [];
        $num_fases_previas = count($Qid_fase_previa);
        for ($i = 0; $i < $num_fases_previas; $i++) {
            $oFase_previa = [];
            $oFase_previa['id_fase'] = $Qid_fase_previa[$i];
            $oFase_previa['id_tarea'] = $Qid_tarea_previa[$i];
            $oFase_previa['mensaje'] = $Qmensaje_requisito[$i];
            if (empty($Qid_fase_previa[$i])) continue;
            $aFases_previas[] = $oFase_previa;
        }
        if (empty($Qid_tarea)) $Qid_tarea = 0; // no puede ser NULL.

        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $oTareaProceso = $TareaProcesoRepository->findById($Qid_item);
        $oTareaProceso->setId_tipo_proceso($Qid_tipo_proceso);
        $oTareaProceso->setStatus($Qstatus);
        $oTareaProceso->setId_of_responsable($Qid_of_responsable);
        $oTareaProceso->setId_fase($Qid_fase);
        $oTareaProceso->setId_tarea($Qid_tarea);
        $oTareaProceso->setJson_fases_previas($aFases_previas);
        if ($TareaProcesoRepository->Guardar($oTareaProceso) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $TareaProcesoRepository->getErrorTxt();
        }
        break;
    case 'eliminar':
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $oTareaProceso = $TareaProcesoRepository->findById($Qid_item);
        if ($TareaProcesoRepository->Eliminar($oTareaProceso) === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $TareaProcesoRepository->getErrorTxt();
        }
        break;
}