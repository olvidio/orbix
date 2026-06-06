<?php

namespace src\cambios\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\domain\AvisoObjetoCatalog;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\XPermisos;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;

/**
 * Data builder: todas las opciones y preseleccion necesarias para pintar la
 * pantalla `usuario_avisos_pref` (configurar un aviso para un usuario o grupo).
 */
final class UsuarioAvisosPrefFormData
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository,
        private GrupoRepositoryInterface $grupoRepository,
        private TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private CambioUsuarioObjetoPrefRepositoryInterface $cambioUsuarioObjetoPrefRepository,
        private ActividadFaseRepositoryInterface $actividadFaseRepository,
        private CasaDlRepositoryInterface $casaDlRepository,
    ) {
    }

    /**
     * @param array{
     *   id_usuario?: int|string,
     *   id_item_usuario_objeto?: int|string,
     *   salida?: string,
     *   quien?: string,
     * } $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
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

        if (str_starts_with((string)$id_usuario, '4')) {
            $oUsuario = $this->usuarioRepository->findById($id_usuario);
            $grupo = false;
        } else {
            $oUsuario = $this->grupoRepository->findById($id_usuario);
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

        if ($salida === 'modificar' && $id_item_usuario_objeto > 0) {
            $oCUOP = $this->cambioUsuarioObjetoPrefRepository->findById($id_item_usuario_objeto);
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

            $aTiposDeProcesos = $this->tipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $dl_propia);
        } elseif ($salida === 'nuevo') {
            $id_tipo_activ = $mi_sfsv . '.....';
            $aTiposDeProcesos = $this->tipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $dl_propia);
        }

        $result['fases_usa_procesos'] = ConfigGlobal::is_app_installed('procesos');
        if ($result['fases_usa_procesos']) {
            $result['aFases'] = $this->actividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);
        } else {
            $a_status = StatusId::getArrayStatus();
            unset($a_status[StatusId::ALL]);
            $result['aFases'] = array_flip($a_status);
        }

        $oRole = new Role();
        $oRole->setId_role($id_role ?? 0);
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
            $id_pau = $oUsuario->getCsvIdPauAsString() ?? '';
            $idsUbis = array_values(array_filter(
                array_map(static fn ($s) => (int)trim((string)$s), explode(',', (string)$id_pau)),
                static fn (int $x) => $x > 0
            ));
            if ($idsUbis !== []) {
                $cond = "WHERE active='t' AND (id_ubi IN (" . implode(',', $idsUbis) . "))";
            } else {
                $cond = "WHERE active='t' AND FALSE";
            }
        }
        $result['aOpcionesCasas'] = $this->casaDlRepository->getArrayCasas($cond);
        $result['id_pau'] = $id_pau;

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

        $oConfig = $_SESSION['oConfig'] ?? null;
        $oPerm = $_SESSION['oPerm'] ?? null;
        $perm_jefe = false;
        if (
            ($oConfig instanceof ConfigSnapshot && $oConfig->is_jefeCalendario())
            || ($oPerm instanceof XPermisos
                && ($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd'))
                && $mi_sfsv === 1)
            || ($grupo === false && $oRole->isRolePau(PauType::PAU_CDC))
            || ($grupo === false && $oRole->isRolePau(PauType::PAU_SACD))
            || ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('calendario'))
        ) {
            $perm_jefe = true;
        }
        $result['perm_jefe'] = $perm_jefe;

        $quien = (string)($input['quien'] ?? '');
        $result['hash_main'] = [
            'campos_form' => 'id_fase_ref!salida!aviso_tipo!objeto!dl_propia!extendida!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val',
            'campos_no' => 'casas!casas_mas!casas_num!id_tipo_activ!inom_tipo_val',
            'campos_chk' => 'aviso_off!aviso_on!aviso_outdate',
            'campos_hidden' => [
                'id_usuario' => $id_usuario,
                'id_item_usuario_objeto' => $id_item_usuario_objeto,
                'quien' => $quien,
            ],
        ];
        $result['paths'] = [
            'cambio_usuario_objeto_pref_guardar' => 'src/cambios/cambio_usuario_objeto_pref_guardar',
            'cambio_usuario_propiedad_pref_guardar_todas' => 'src/cambios/cambio_usuario_propiedad_pref_guardar_todas',
            'cambio_usuario_propiedad_pref_preview' => 'src/cambios/cambio_usuario_propiedad_pref_preview',
            'usuario_avisos_pref_propiedades' => 'frontend/cambios/controller/usuario_avisos_pref_propiedades.php',
            'usuario_avisos_pref_condicion' => 'frontend/cambios/controller/usuario_avisos_pref_condicion.php',
            'usuario_avisos_pref_fases' => 'frontend/cambios/controller/usuario_avisos_pref_fases.php',
        ];
        $result['hash_ajax_fases'] = [
            'path' => 'frontend/cambios/controller/usuario_avisos_pref_fases.php',
            'campos_form' => 'salida!dl_propia!id_tipo_activ!id_usuario!objeto',
        ];
        $result['hash_ajax_propiedades'] = [
            'path' => 'frontend/cambios/controller/usuario_avisos_pref_propiedades.php',
            'campos_form' => 'salida!objeto!id_item_usuario_objeto',
        ];
        $result['hash_ajax_mod'] = [
            'path' => 'frontend/cambios/controller/usuario_avisos_pref_condicion.php',
            'campos_form' => 'salida!objeto!propiedad!id_item',
        ];

        return $result;
    }
}
