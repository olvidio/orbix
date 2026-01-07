<?php

use core\ConfigGlobal;
use Illuminate\Http\JsonResponse;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use web\Hash;
use function core\is_true;

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');

$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$miRole = $oMiUsuario->getId_role();
$miSfsv = ConfigGlobal::mi_sfsv();

// a los usuarios normales (no administrador) sólo dejo ver la parte de los avisos.
$error_txt = _("no tiene permisos para ver esto");
if ($miRole < 4) { // es administrador
    $error_txt = '';
    if ($miRole !== 1) {
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
        $cond_role .= "AND (pau != '" . PauType::PAU_SACD . "' OR pau IS NULL)";
    }
    if (!(ConfigGlobal::is_app_installed('ubis'))) {
        $cond_role .= "AND (pau != '" . PauType::PAU_CTR . "' OR pau != '" . PauType::PAU_CDC . "' OR pau IS NULL)";
    }

    $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
    $aOpcionesRoles = $RoleRepository->getArrayRolesCondicion($cond_role);

    $oGrupoGrupoPermMenu = [];
    $cUsuarioPerm = [];
    $cUsuarioPermCtr = [];
    $camposMas = '';
    $aDataDespl = [];
    if (!empty($Qid_usuario)) {
        $que_user = 'guardar';
        $oUsuario = $UsuarioRepository->findById($Qid_usuario);

        $seccion = $miSfsv;
        $usuario = $oUsuario->getUsuarioAsString();
        $nom_usuario = $oUsuario->getNomUsuarioAsString();
        $cambio_password = $oUsuario->isCambio_password();
        $chk_cambio_password = is_true($cambio_password) ? 'checked' : '';
        $has_2fa = $oUsuario->has2fa();
        $chk_has_2fa = is_true($has_2fa) ? 'checked' : '';
        $email = $oUsuario->getEmailAsString();
        $id_role = $oUsuario->getId_role();
        $oRole = $RoleRepository->findById($id_role);
        $pau = $oRole->getPauAsString();
        $isSv = $oRole->isSv();
        $isSf = $oRole->isSf();
        if ($pau === PauType::PAU_CDC) { //casa
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
            $oGCasas = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
            $aOpcionesCasas = $oGCasas->getArrayCasas($cond);

            $aDataDespl['tipo'] = 'array';
            $aDataDespl['nom'] = 'casas';
            $aDataDespl['blanco'] = 't';
            $aDataDespl['aOpciones'] = $aOpcionesCasas;
            $aDataDespl['accionConjunto'] = 'fnjs_mas_casas(event)';
            $aDataDespl['opcion_sel'] = $id_pau;
            $camposMas = 'casas!casas_mas!casas_num';
        }
        if ($pau === PauType::PAU_CTR && $isSv) { //centroSv
            $id_pau = $oUsuario->getId_pauAsString();
            $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            $aOpciones = $CentroDlRepository->getArrayCentros();

            $aDataDespl['tipo'] = 'simple';
            $aDataDespl['nom'] = 'id_ctr';
            $aDataDespl['aOpciones'] = $aOpciones;
            $aDataDespl['blanco'] = 't';
            $aDataDespl['opcion_sel'] = $id_pau;
            $camposMas = 'id_ctr';
        }
        if ($pau === PauType::PAU_CTR && $isSf) { //centroSf
            $id_pau = $oUsuario->getId_pauAsString();
            $oGesCentrosDl = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
            $aOpciones = $oGesCentrosDl->getArrayCentros();

            $aDataDespl['tipo'] = 'simple';
            $aDataDespl['nom'] = 'id_ctr';
            $aDataDespl['aOpciones'] = $aOpciones;
            $aDataDespl['blanco'] = 't';
            $aDataDespl['opcion_sel'] = $id_pau;
            $camposMas = 'id_ctr';
        }
        if ($pau === PauType::PAU_NOM || $pau === PauType::PAU_SACD) { //sacd //personas dl
            $id_pau = $oUsuario->getId_pauAsString();

            $nom_role = $oRole->getRoleAsString();
            switch ($nom_role) {
                case "p-agd":
                    $PersonaAgdRepository = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
                    $aOpciones = $PersonaAgdRepository->getArrayPersonas();
                    break;
                case "p-n":
                    $PersonaNRepository = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
                    $aOpciones = $PersonaNRepository->getArrayPersonas();
                    break;
                case "p-sacd":
                case "p-sacdInt": // para hacer pruebas desde dentro (dmz=false)
                    $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
                    // de momento sólo n y agd
                    $aOpciones = $PersonaDlRepository->getArraySacd("AND id_tabla ~ '[na]'");
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
            $PermUsuarioActividadRepository = $GLOBALS['container']->get(PermUsuarioActividadRepositoryInterface::class);
            $aWhere = ['id_usuario' => $Qid_usuario, '_ordre' => 'dl_propia DESC, id_tipo_activ_txt'];
            $aOperador = [];
            $cUsuarioPerm = $PermUsuarioActividadRepository->getPermUsuarioActividades($aWhere, $aOperador);
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
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
} else {
    $jsondata['success'] = TRUE;
    $jsondata['data'] = json_encode(['a_campos' => $a_campos], JSON_FORCE_OBJECT);
}

(new JsonResponse($jsondata))->send();

