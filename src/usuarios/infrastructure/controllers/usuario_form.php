<?php

use core\ConfigGlobal;
use Illuminate\Http\JsonResponse;
use personas\model\entity\GestorPersonaAgd;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\GestorPersonaN;
use procesos\model\entity\GestorPermUsuarioActividad;
use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use src\usuarios\domain\entity\Role;
use ubis\model\entity\GestorCasaDl;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use web\DesplegableArray;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************


// FIN de  Cabecera global de URL de controlador ********************************


$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');


$UsuarioRepository = new UsuarioRepository();
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$miRole = $oMiUsuario->getId_role();
$miSfsv = ConfigGlobal::mi_sfsv();


// a los usuarios normales (no administrador) sólo dejo ver la parte de los avisos.
$error_txt = _("no tiene permisos para ver esto");
if ($miRole < 4) { // es administrador
    $error_txt = '';
    if ($miRole != 1) {
        $cond_role = "WHERE id_role <> 1 ";
    } else {
        $cond_role = "WHERE id_role > 0 "; //absurda cond, pero para que no se borre el role del superadmin
    }

    switch ($miSfsv) {
        case 1:
            $cond_role .= "AND sv='t'";
            break;
        case 2:
            $cond_role .= "AND sf='t'";
            break;
    }

    if (!(ConfigGlobal::is_app_installed('personas'))) {
        $cond_role .= "AND (pau != '" . Role::PAU_SACD . "' OR pau IS NULL)";
    }
    if (!(ConfigGlobal::is_app_installed('ubis'))) {
        $cond_role .= "AND (pau != '" . Role::PAU_CTR . "' OR pau != '" . Role::PAU_CDC . "' OR pau IS NULL)";
    }

    $RoleRepository = new RoleRepository();
    $aOpcionesRoles = $RoleRepository->getArrayRolesCondicion($cond_role);

    $oGrupoGrupoPermMenu = [];
    $cUsuarioPerm = [];
    $cUsuarioPermCtr = [];
    $oSelects = new DesplegableArray();
    $camposMas = '';
    if (!empty($Qid_usuario)) {
        $que_user = 'guardar';
        $UsuarioRepository = new UsuarioRepository();
        $oUsuario = $UsuarioRepository->findById($Qid_usuario);

        $seccion = $miSfsv;
        $usuario = $oUsuario->getUsuario();
        $nom_usuario = $oUsuario->getNom_usuario();
        $pass = $oUsuario->getPassword();
        $email = $oUsuario->getEmail();
        $id_role = $oUsuario->getId_role();
        $oRole = $RoleRepository->findById($id_role);
        $pau = $oRole->getPau();
        $isSv = $oRole->isSv();
        $isSf = $oRole->isSf();
        if ($pau === Role::PAU_CDC) { //casa
            $id_pau = $oUsuario->getId_pau();
            $cond = '';
            switch ($seccion) {
                case 1:
                    $cond = "WHERE sv = 't'";
                    break;
                case 2:
                    $cond = "WHERE sf = 't'";
                    break;
            }
            $oGCasas = new GestorCasaDl();
            $oOpcionesCasas = $oGCasas->getPosiblesCasas($cond);

            $oSelects = new DesplegableArray($id_pau, $oOpcionesCasas, 'casas');
            $oSelects->setBlanco('t');
            $oSelects->setAccionConjunto('fnjs_mas_casas(event)');
            $camposMas = 'casas!casas_mas!casas_num';
        }
        if ($pau === Role::PAU_CTR && $isSv) { //centroSv
            $id_pau = $oUsuario->getId_pau();
            $oGesCentrosDl = new GestorCentroDl();
            $oSelects = $oGesCentrosDl->getListaCentros();

            $oSelects->setNombre('id_ctr');
            $oSelects->setOpcion_sel($id_pau);
            $oSelects->setBlanco('t');
            $camposMas = 'id_ctr';
        }
        if ($pau === Role::PAU_CTR && $isSf) { //centroSf
            $id_pau = $oUsuario->getId_pau();
            $oGesCentrosDl = new GestorCentroEllas();
            $oSelects = $oGesCentrosDl->getListaCentros();

            $oSelects->setNombre('id_ctr');
            $oSelects->setOpcion_sel($id_pau);
            $oSelects->setBlanco('t');
            $camposMas = 'id_ctr';
        }
        if ($pau == Role::PAU_NOM || $pau == Role::PAU_SACD) { //sacd //personas dl
            $id_pau = $oUsuario->getId_pau();

            $nom_role = $oRole->getRole();
            switch ($nom_role) {
                case "p-agd":
                    $GesPersonas = new GestorPersonaAgd();
                    $oSelects = $GesPersonas->getListaPersonas();
                    break;
                case "p-n":
                    $GesPersonas = new GestorPersonaN();
                    $oSelects = $GesPersonas->getListaPersonas();
                    break;
                case "p-sacd":
                case "p-sacdInt": // para hacer pruebas desde dentro (dmz=false)
                    $GesPersonas = new GestorPersonaDl();
                    // de momento sólo n y agd
                    $oSelects = $GesPersonas->getListaSacd("AND id_tabla ~ '[na]'");
                    break;
            }

            $oSelects->setNombre('id_nom');
            $oSelects->setOpcion_sel($id_pau);
            $oSelects->setBlanco('t');
            $camposMas = 'id_nom';
        }

        if (ConfigGlobal::is_app_installed('procesos')) {
            $oGesPerm = new GestorPermUsuarioActividad();
            $aWhere = ['id_usuario' => $Qid_usuario, '_ordre' => 'dl_propia DESC, id_tipo_activ_txt'];
            $aOperador = [];
            $cUsuarioPerm = $oGesPerm->getPermUsuarioActividades($aWhere, $aOperador);
        }
    } else {
        $que_user = 'nuevo';
        $id_role = '';
        $Qid_usuario = '';
        $usuario = '';
        $nom_usuario = '';
        $pass = '';
        $seccion = '';
        $email = '';
        $pau = '';
    }

    $camposForm = 'que!usuario!nom_usuario!password!email!id_role';
    $camposForm = !empty($camposMas) ? $camposForm . '!' . $camposMas : $camposForm;
    $oHash = new Hash();
    $oHash->setCamposForm($camposForm);
    $oHash->setcamposNo('pass!password!id_ctr!id_nom!casas');
    $a_camposHidden = array(
        'id_usuario' => $Qid_usuario,
        'quien' => $Qquien
    );
    $oHash->setArraycamposHidden($a_camposHidden);


    $obj = 'src\\usuarios\\domain\\entity\\Usuario';

    $a_campos = [
        'id_usuario' => $Qid_usuario,
        'obj' => $obj,
        'que_user' => $que_user,
        'quien' => $Qquien,
        'pau' => $pau,
        'oSelects' => $oSelects->export(),
        //'pass' => $pass,
        'usuario' => $usuario,
        'nom_usuario' => $nom_usuario,
        'aOpcionesRoles' => $aOpcionesRoles,
        'id_role' => $id_role,
        'oGrupoGrupoPermMenu' => $oGrupoGrupoPermMenu,
        'cUsuarioPermCtr' => $cUsuarioPermCtr,
        'email' => $email,
        'camposMas' => $camposMas,
    ];

} // fin solo administradores.

if (!empty($error_txt)) {
    $jsondata['success'] = FALSE;
    $jsondata['mensaje'] = $error_txt;
} else {
    $jsondata['success'] = TRUE;
    $jsondata['data'] = json_encode(['a_campos' => $a_campos], JSON_FORCE_OBJECT);
}

(new JsonResponse($jsondata))->send();


