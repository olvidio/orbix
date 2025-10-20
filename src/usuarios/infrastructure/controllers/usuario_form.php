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
use function core\is_true;

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
    $camposMas = '';
    $aDataDespl = [];
    if (!empty($Qid_usuario)) {
        $que_user = 'guardar';
        $UsuarioRepository = new UsuarioRepository();
        $oUsuario = $UsuarioRepository->findById($Qid_usuario);

        $seccion = $miSfsv;
        $usuario = $oUsuario->getUsuarioAsString();
        $nom_usuario = $oUsuario->getNom_usuarioAsString();
        $cambio_password = $oUsuario->isCambio_password();
        $chk_cambio_password = is_true($cambio_password)? 'checked' : '';
        $has_2fa = $oUsuario->has2fa();
        $chk_has_2fa = is_true($has_2fa)? 'checked' : '';
        $email = $oUsuario->getEmailAsString();
        $id_role = $oUsuario->getId_role();
        $oRole = $RoleRepository->findById($id_role);
        $pau = $oRole->getPauAsString();
        $isSv = $oRole->isSv();
        $isSf = $oRole->isSf();
        if ($pau === Role::PAU_CDC) { //casa
            $id_pau = $oUsuario->getId_pauAsString();
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
            $aOpcionesCasas = $oGCasas->getArrayPosiblesCasas($cond);

            $aDataDespl['tipo'] = 'array';
            $aDataDespl['nom'] = 'casas';
            $aDataDespl['blanco'] = 't';
            $aDataDespl['aOpciones'] = $aOpcionesCasas;
            $aDataDespl['accionConjunto'] = 'fnjs_mas_casas(event)';
            $aDataDespl['opcion_sel'] = $id_pau;
            $camposMas = 'casas!casas_mas!casas_num';
        }
        if ($pau === Role::PAU_CTR && $isSv) { //centroSv
            $id_pau = $oUsuario->getId_pau()->value();
            $oGesCentrosDl = new GestorCentroDl();
            $aOpciones = $oGesCentrosDl->getArrayCentros();

            $aDataDespl['tipo'] = 'simple';
            $aDataDespl['nom'] = 'id_ctr';
            $aDataDespl['aOpciones'] = $aOpciones;
            $aDataDespl['blanco'] = 't';
            $aDataDespl['opcion_sel'] = $id_pau;
            $camposMas = 'id_ctr';
        }
        if ($pau === Role::PAU_CTR && $isSf) { //centroSf
            $id_pau = $oUsuario->getId_pau()->value();
            $oGesCentrosDl = new GestorCentroEllas();
            $aOpciones = $oGesCentrosDl->getArrayCentros();

            $aDataDespl['tipo'] = 'simple';
            $aDataDespl['nom'] = 'id_ctr';
            $aDataDespl['aOpciones'] = $aOpciones;
            $aDataDespl['blanco'] = 't';
            $aDataDespl['opcion_sel'] = $id_pau;
            $camposMas = 'id_ctr';
        }
        if ($pau == Role::PAU_NOM || $pau == Role::PAU_SACD) { //sacd //personas dl
            $id_pau = $oUsuario->getId_pau()->value();

            $nom_role = $oRole->getRoleAsString();
            switch ($nom_role) {
                case "p-agd":
                    $GesPersonas = new GestorPersonaAgd();
                    $aDataDespl = $GesPersonas->getArrayPersonas();
                    break;
                case "p-n":
                    $GesPersonas = new GestorPersonaN();
                    $aDataDespl = $GesPersonas->getArrayPersonas();
                    break;
                case "p-sacd":
                case "p-sacdInt": // para hacer pruebas desde dentro (dmz=false)
                    $GesPersonas = new GestorPersonaDl();
                    // de momento sólo n y agd
                    $aOpciones = $GesPersonas->getArraySacd("AND id_tabla ~ '[na]'");
                    break;
            }

            $aDataDespl['tipo'] = 'simple';
            $aDataDespl['nom'] = 'id_nom';
            $aDataDespl['aOpciones'] = $aOpciones;
            $aDataDespl['blanco'] = 't';
            $aDataDespl['opcion_sel'] = $id_pau;
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
        $seccion = '';
        $email = '';
        $pau = '';
        $chk_cambio_password = '';
        $chk_has_2fa = '';
    }

    $camposForm = 'que!usuario!nom_usuario!password!email!id_role';
    $camposForm = !empty($camposMas) ? $camposForm . '!' . $camposMas : $camposForm;
    $oHash = new Hash();
    $oHash->setCamposForm($camposForm);
    $oHash->setcamposNo('password!id_ctr!id_nom!casas');
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
        'aDataDespl' => $aDataDespl,
        'usuario' => $usuario,
        'nom_usuario' => $nom_usuario,
        'aOpcionesRoles' => $aOpcionesRoles,
        'id_role' => $id_role,
        'oGrupoGrupoPermMenu' => $oGrupoGrupoPermMenu,
        'cUsuarioPermCtr' => $cUsuarioPermCtr,
        'email' => $email,
        'camposMas' => $camposMas,
        'chk_cambio_password' => $chk_cambio_password,
        'chk_has_2fa' => $chk_has_2fa,
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


