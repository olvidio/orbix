<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\config\ConfigGlobal;
use Illuminate\Http\JsonResponse;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\value_objects\PauType;
use src\shared\security\HashB;
use function src\shared\domain\helpers\is_true;

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');

$UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
if ($oMiUsuario === null) {
    $jsondata = ['success' => false, 'mensaje' => _('Usuario no encontrado')];
    (new JsonResponse($jsondata))->send();
    return;
}
$miRole = $oMiUsuario->getId_role();
$aOpciones = [];
$a_campos = [];
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

    $RoleRepository = DependencyResolver::get(RoleRepositoryInterface::class);
    $aOpcionesRoles = $RoleRepository->getArrayRolesCondicion($cond_role);

    $oGrupoGrupoPermMenu = [];
    $cUsuarioPerm = [];
    $cUsuarioPermCtr = [];
    $camposMas = '';
    $aDataDespl = [];
    $que_user = 'nuevo';
    $id_role = '';
    $usuario = '';
    $nom_usuario = '';
    $seccion = '';
    $email = '';
    $pau = '';
    $chk_cambio_password = '';
    $chk_has_2fa = '';
    if (!empty($Qid_usuario)) {
        $que_user = 'guardar';
        $oUsuario = $UsuarioRepository->findById($Qid_usuario);
        if ($oUsuario === null) {
            $error_txt = _('Usuario no encontrado');
        } else {

        $seccion = $miSfsv;
        $usuario = $oUsuario->getUsuarioAsString();
        $nom_usuario = $oUsuario->getNomUsuarioAsString();
        $cambio_password = $oUsuario->isCambio_password();
        $chk_cambio_password = is_true($cambio_password) ? 'checked' : '';
        $has_2fa = $oUsuario->isHas_2fa();
        $chk_has_2fa = is_true($has_2fa) ? 'checked' : '';
        $email = $oUsuario->getEmailAsString();
        $id_role = $oUsuario->getId_role();
        $oRole = $RoleRepository->findById($id_role ?? 0);
        if ($oRole === null) {
            $error_txt = _('Rol no encontrado');
        } else {
        $pau = $oRole->getPauAsString();
        $isSv = $oRole->isSv();
        $isSf = $oRole->isSf();
        if ($pau === PauType::PAU_CDC) { //casa
            $id_pau = $oUsuario->getCsvIdPauAsString();
            $cond = '';
            switch ($seccion) {
                case 1:
                    $cond = "WHERE sv = 't'";
                    break;
                case 2:
                    $cond = "WHERE sf = 't'";
                    break;
            }
            $oGCasas = DependencyResolver::get(CasaDlRepositoryInterface::class);
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
            $id_pau = $oUsuario->getCsvIdPauAsString();
            $CentroDlRepository = DependencyResolver::get(CentroEllosRepositoryInterface::class);
            $aOpciones = $CentroDlRepository->getArrayCentros();

            $aDataDespl['tipo'] = 'simple';
            $aDataDespl['nom'] = 'id_ctr';
            $aDataDespl['aOpciones'] = $aOpciones;
            $aDataDespl['blanco'] = 't';
            $aDataDespl['opcion_sel'] = $id_pau;
            $camposMas = 'id_ctr';
        }
        if ($pau === PauType::PAU_CTR && $isSf) { //centroSf
            $id_pau = $oUsuario->getCsvIdPauVo()?->value();
            $CentroDlRepository = DependencyResolver::get(CentroEllasRepositoryInterface::class);
            $aOpciones = $CentroDlRepository->getArrayCentros();

            $aDataDespl['tipo'] = 'simple';
            $aDataDespl['nom'] = 'id_ctr';
            $aDataDespl['aOpciones'] = $aOpciones;
            $aDataDespl['blanco'] = 't';
            $aDataDespl['opcion_sel'] = $id_pau;
            $camposMas = 'id_ctr';
        }
        if ($pau === PauType::PAU_NOM || $pau === PauType::PAU_SACD) { //sacd //personas dl
            $id_pau = $oUsuario->getCsvIdPauAsString();

            $nom_role = $oRole->getRoleAsString();
            $aOpciones = [];
            switch ($nom_role) {
                case "p-agd":
                    $PersonaAgdRepository = DependencyResolver::get(PersonaAgdRepositoryInterface::class);
                    $aOpciones = $PersonaAgdRepository->getArrayPersonas();
                    break;
                case "p-n":
                    $PersonaNRepository = DependencyResolver::get(PersonaNRepositoryInterface::class);
                    $aOpciones = $PersonaNRepository->getArrayPersonas();
                    break;
                case "p-sacd":
                case "p-sacdInt": // para hacer pruebas desde dentro (dmz=false)
                    $PersonaDlRepository = DependencyResolver::get(PersonaDlRepositoryInterface::class);
                    // de momento sólo n y agd
                    $aOpciones = $PersonaDlRepository->getArraySacd("AND id_tabla ~ '[na]'");
                    break;
                default:
                    $aOpciones = [];
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
            $PermUsuarioActividadRepository = DependencyResolver::get(PermUsuarioActividadRepositoryInterface::class);
            $aWhere = ['id_usuario' => $Qid_usuario, '_ordre' => 'dl_propia DESC, id_tipo_activ_txt'];
            $aOperador = [];
            $cUsuarioPerm = $PermUsuarioActividadRepository->getPermUsuarioActividades($aWhere, $aOperador);
        }
        }
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

    if (empty($error_txt)) {
        $obj = 'src\\usuarios\\domain\\entity\\Usuario';

        $id_for_ctx = ($que_user === 'nuevo') ? 0 : (int)$Qid_usuario;
        $ctx_guardar = HashB::sign('usuario_guardar', [
            'que_user' => $que_user,
            'id_usuario' => $id_for_ctx,
            'quien' => (string)$Qquien,
        ]);

        $a_campos = [
            'id_usuario' => $Qid_usuario,
            'obj' => $obj,
            'que_user' => $que_user,
            'quien' => $Qquien,
            'ctx_guardar' => $ctx_guardar,
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
    }

} // fin solo administradores.

if (!empty($error_txt)) {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
} else {
    $jsondata['success'] = TRUE;
    $jsondata['data'] = json_encode(['a_campos' => $a_campos], JSON_FORCE_OBJECT);
}

(new JsonResponse($jsondata))->send();

