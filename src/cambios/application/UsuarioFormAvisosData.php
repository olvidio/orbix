<?php

namespace src\cambios\application;

use src\actividades\domain\entity\TiposActividades;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\domain\AvisoObjetoCatalog;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Data builder: listado de `CambioUsuarioObjetoPref` de un usuario para
 * pintar la tabla `usuario_form_avisos`.
 *
 * Sucesor del backend de `apps/cambios/controller/usuario_form_avisos.php`.
 */
final class UsuarioFormAvisosData
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository,
        private CambioUsuarioPropiedadPrefRepositoryInterface $cambioUsuarioPropiedadPrefRepository,
        private CambioUsuarioObjetoPrefRepositoryInterface $cambioUsuarioObjetoPrefRepository,
        private ActividadFaseRepositoryInterface $actividadFaseRepository,
    ) {
    }

    /**
     * @param array{id_usuario?: int|string, quien?: string} $input
     * @return array{
     *   error: string,
     *   a_valores: array<int, array<int|string, mixed>>,
     *   nombre_usuario: string,
     *   fases_usa_procesos: bool,
     * }
     */
    public function execute(array $input): array
    {
        $id_usuario = (int)($input['id_usuario'] ?? 0);
        $quien = (string)($input['quien'] ?? '');

        $a_valores = [];
        $nombre_usuario = '';

        if (!ConfigGlobal::is_app_installed('cambios') || $id_usuario === 0 || $quien !== 'usuario') {
            return [
                'error' => (string)_("No tiene permiso"),
                'a_valores' => $a_valores,
                'nombre_usuario' => $nombre_usuario,
                'fases_usa_procesos' => ConfigGlobal::is_app_installed('procesos'),
            ];
        }

        $oUsuario = $this->usuarioRepository->findById($id_usuario);
        if ($oUsuario === null) {
            return [
                'error' => (string)_("No tiene permiso"),
                'a_valores' => $a_valores,
                'nombre_usuario' => $nombre_usuario,
                'fases_usa_procesos' => ConfigGlobal::is_app_installed('procesos'),
            ];
        }
        $nombre_usuario = $oUsuario->getUsuarioAsString();

        $a_status = StatusId::getArrayStatus();

        $aWhere = ['id_usuario' => $id_usuario, '_ordre' => 'objeto, dl_org, id_tipo_activ_txt'];
        $cListaTablas = $this->cambioUsuarioObjetoPrefRepository->getCambioUsuarioObjetoPrefs($aWhere, []);

        $aTipos_aviso = AvisoTipoId::getArrayAvisoTipo();
        $aObjetos = AvisoObjetoCatalog::getArrayObjetosPosibles();

        $i = 0;
        foreach ($cListaTablas as $oCambioUsuarioObjetoPref) {
            $i++;
            $id_item_usuario_objeto = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
            $id_tipo = $oCambioUsuarioObjetoPref->getId_tipo_activ_txt();
            $dl_org = $oCambioUsuarioObjetoPref->getDl_org();
            $objeto = $oCambioUsuarioObjetoPref->getObjeto();
            $aviso_tipo = $oCambioUsuarioObjetoPref->getAviso_tipo();
            $id_fase_ref = $oCambioUsuarioObjetoPref->getId_fase_ref();
            $aviso_off = $oCambioUsuarioObjetoPref->isAviso_off();
            $aviso_on = $oCambioUsuarioObjetoPref->isAviso_on();
            $aviso_outdate = $oCambioUsuarioObjetoPref->isAviso_outdate();

            $isfsv = substr((string) $id_tipo, 0, 1);
            $mi_dele = ConfigGlobal::mi_delef($isfsv);
            if ($dl_org !== $mi_dele) {
                $dl_org = (string)_("otras");
            }

            $oTipoActividad = new TiposActividades($id_tipo);
            $objeto_txt = $aObjetos[$objeto] ?? $objeto;

            $a_valores[$i]['sel'] = "$id_usuario#$id_item_usuario_objeto";
            $a_valores[$i][1] = $objeto_txt;
            $a_valores[$i][2] = $dl_org;
            $a_valores[$i][3] = $oTipoActividad->getNom();

            $txt_fases = '';
            if (ConfigGlobal::is_app_installed('procesos')) {
                $oActividadFase = $this->actividadFaseRepository->findById($id_fase_ref);
                if ($oActividadFase !== null) {
                    $txt_fases .= $oActividadFase->getDesc_fase();
                }
            } else {
                $txt_fases .= $a_status[$id_fase_ref] ?? '';
            }
            $a_valores[$i][4] = $txt_fases;
            $a_valores[$i][5] = $aviso_off;
            $a_valores[$i][6] = $aviso_on;
            $a_valores[$i][7] = $aviso_outdate;
            $a_valores[$i][8] = $aTipos_aviso[$aviso_tipo] ?? '';

            $cListaPropiedades = $this->cambioUsuarioPropiedadPrefRepository->getCambioUsuarioPropiedadPrefs(
                ['id_item_usuario_objeto' => $id_item_usuario_objeto]
            );
            $txt_cambio = '';
            $txt_propiedades = '';
            $c = 0;
            foreach ($cListaPropiedades as $oCambioUsuarioPropiedadPref) {
                $c++;
                $propiedad = $oCambioUsuarioPropiedadPref->getPropiedad();
                if ($c > 1) {
                    $txt_propiedades .= ', ';
                }
                $txt_cambio .= $txt_cambio === '' ? '' : ', ';
                $txt_propiedades .= $propiedad;
                $txt_cambio .= $oCambioUsuarioPropiedadPref->getTextCambio();
            }
            $a_valores[$i][9] = $txt_propiedades;
            $a_valores[$i][10] = $txt_cambio;
        }

        return [
            'error' => '',
            'a_valores' => $a_valores,
            'nombre_usuario' => $nombre_usuario,
            'fases_usa_procesos' => ConfigGlobal::is_app_installed('procesos'),
        ];
    }
}
