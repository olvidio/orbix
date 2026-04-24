<?php

namespace src\cambios\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\domain\AvisoObjetoCatalog;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use function core\is_true;

/**
 * Data builder: todas las opciones y preseleccion necesarias para pintar la
 * pantalla `usuario_avisos_pref` (configurar un aviso para un usuario o grupo).
 *
 * Sucesor del backend de `apps/cambios/controller/usuario_avisos_pref.php`.
 */
final class UsuarioAvisosPrefFormData
{
    /**
     * @param array{
     *   id_usuario?: int|string,
     *   id_item_usuario_objeto?: int|string,
     *   salida?: string,
     * } $input
     * @return array
     */
    public static function execute(array $input): array
    {
        $id_usuario = (int)($input['id_usuario'] ?? 0);
        $id_item_usuario_objeto = (int)($input['id_item_usuario_objeto'] ?? 0);
        $salida = (string)($input['salida'] ?? '');

        $result = [
            'error' => '',
            'id_usuario' => $id_usuario,
            'id_item_usuario_objeto' => $id_item_usuario_objeto,
            'salida' => $salida,
            'nombre' => '',
            'id_role' => 0,
            'grupo' => false,
            'aObjetos' => [],
            'aTiposAviso' => [],
            'aFases' => [],
            'aOpcionesCasas' => [],
            'fases_usa_procesos' => false,
            'id_pau' => '',
            'dl_propia' => true,
            'dl_org' => '',
            'id_tipo_activ' => '',
            'id_fase_ref' => '',
            'objeto' => '',
            'aviso_tipo' => '',
            'aviso_off' => false,
            'aviso_on' => true,
            'aviso_outdate' => false,
            'perm_jefe' => false,
            'sfsv_text' => '',
            'asistentes_text' => '',
            'actividad_text' => '',
            'nom_tipo_text' => '',
        ];

        if ($id_usuario <= 0) {
            $result['error'] = (string)_("falta id_usuario");
            return $result;
        }

        $mi_sfsv = ConfigGlobal::mi_sfsv();

        // Si empieza por 4 es usuario, por 5 es grupo.
        if (str_starts_with((string)$id_usuario, '4')) {
            $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
            $oUsuario = $UsuarioRepository->findById($id_usuario);
            $grupo = false;
        } else {
            $GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
            $oUsuario = $GrupoRepository->findById($id_usuario);
            $grupo = true;
        }
        if ($oUsuario === null) {
            $result['error'] = (string)_("usuario/grupo no encontrado");
            return $result;
        }

        $nombre = $oUsuario->getUsuarioAsString();
        $id_role = $oUsuario->getId_role();

        $result['nombre'] = $nombre;
        $result['id_role'] = $id_role;
        $result['grupo'] = $grupo;

        $result['aObjetos'] = AvisoObjetoCatalog::getArrayObjetosPosibles();
        $result['aTiposAviso'] = AvisoTipoId::getArrayAvisoTipo();

        // Campos preseleccionados segun modo (`nuevo` vs `modificar`).
        $id_tipo_activ = '';
        $dl_propia = true;
        $id_pau = '';
        $id_fase_ref = '';
        $aviso_off = false;
        $aviso_on = true;
        $aviso_outdate = false;
        $objeto = '';
        $aviso_tipo = '';
        $dl_org = '';
        $aTiposDeProcesos = [];

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);

        if ($salida === 'modificar' && $id_item_usuario_objeto > 0) {
            $CambioUsuarioObjetoPrefRepository = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
            $oCUOP = $CambioUsuarioObjetoPrefRepository->findById($id_item_usuario_objeto);
            if ($oCUOP === null) {
                $result['error'] = (string)_("preferencia no encontrada");
                return $result;
            }
            $id_tipo_activ = $oCUOP->getId_tipo_activ_txt();
            $dl_org = $oCUOP->getDl_org();
            $objeto = $oCUOP->getObjeto();
            $aviso_tipo = $oCUOP->getAviso_tipo();
            $id_pau = $oCUOP->getCsv_id_pau();
            $id_fase_ref = $oCUOP->getId_fase_ref();
            $aviso_off = $oCUOP->isAviso_off();
            $aviso_on = $oCUOP->isAviso_on();
            $aviso_outdate = $oCUOP->isAviso_outdate();

            $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', (string)$dl_org);
            $dl_propia = (ConfigGlobal::mi_dele() === $dl_org_no_f);

            $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $dl_propia);
        } elseif ($salida === 'nuevo') {
            $id_tipo_activ = $mi_sfsv . '.....';
            $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $dl_propia);
        }

        // Fases segun modulo `procesos`.
        $result['fases_usa_procesos'] = ConfigGlobal::is_app_installed('procesos');
        if ($result['fases_usa_procesos']) {
            $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
            $result['aFases'] = $ActividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);
        } else {
            $a_status = StatusId::getArrayStatus();
            unset($a_status[StatusId::ALL]);
            $result['aFases'] = array_flip($a_status);
        }

        // Rol -> restriccion de casas.
        $oRole = new Role();
        $oRole->setId_role($id_role);
        $cond = '';
        switch ($mi_sfsv) {
            case 1:
                $cond = "WHERE sv = 't'";
                break;
            case 2:
                $cond = "WHERE sf = 't'";
                break;
        }
        if ($grupo === false && $oRole->isRolePau(PauType::PAU_CDC)) {
            $id_pau = $oUsuario->getCsv_id_pau();
            $sDonde = str_replace(',', ' OR id_ubi=', (string)$id_pau);
            $cond = "WHERE active='t' AND (id_ubi=$sDonde)";
        }
        $CasaDlRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $result['aOpcionesCasas'] = $CasaDlRepository->getArrayCasas($cond);
        $result['id_pau'] = $id_pau;

        // Descomposicion del id_tipo_activ actual en sus 4 piezas.
        $result['dl_org'] = $dl_org;
        $result['dl_propia'] = $dl_propia;
        $result['id_tipo_activ'] = $id_tipo_activ;
        $result['id_fase_ref'] = $id_fase_ref;
        $result['objeto'] = $objeto;
        $result['aviso_tipo'] = $aviso_tipo;
        $result['aviso_off'] = $aviso_off;
        $result['aviso_on'] = $aviso_on;
        $result['aviso_outdate'] = $aviso_outdate;

        $ssfsv = '';
        if ($mi_sfsv === 1) {
            $ssfsv = 'sv';
        }
        if ($mi_sfsv === 2) {
            $ssfsv = 'sf';
        }
        if ($grupo === false && $oRole->isRolePau(PauType::PAU_CDC)) {
            $ssfsv = '';
        }
        $result['sfsv_text'] = $ssfsv;
        $result['asistentes_text'] = '';
        $result['actividad_text'] = '';
        $result['nom_tipo_text'] = '';

        // Perm_jefe: misma logica que el controlador legacy.
        $perm_jefe = false;
        if (
            $_SESSION['oConfig']->is_jefeCalendario()
            || (($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) && $mi_sfsv === 1)
            || ($grupo === false && $oRole->isRolePau(PauType::PAU_CDC))
            || ($grupo === false && $oRole->isRolePau(PauType::PAU_SACD))
            || $_SESSION['oPerm']->have_perm_oficina('calendario')
        ) {
            $perm_jefe = true;
        }
        $result['perm_jefe'] = $perm_jefe;

        return $result;
    }
}
