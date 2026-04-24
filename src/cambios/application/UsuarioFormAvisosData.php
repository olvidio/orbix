<?php

namespace src\cambios\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\domain\AvisoObjetoCatalog;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use web\TiposActividades;

/**
 * Data builder: listado de `CambioUsuarioObjetoPref` de un usuario para
 * pintar la tabla `usuario_form_avisos`.
 *
 * Sucesor del backend de `apps/cambios/controller/usuario_form_avisos.php`.
 */
final class UsuarioFormAvisosData
{
    /**
     * @param array{id_usuario?: int|string, quien?: string} $input
     * @return array{
     *   error: string,
     *   a_valores: array,
     *   nombre_usuario: string,
     * }
     */
    public static function execute(array $input): array
    {
        $id_usuario = (int)($input['id_usuario'] ?? 0);
        $quien = (string)($input['quien'] ?? '');

        $a_valores = [];
        $nombre_usuario = '';

        if (!ConfigGlobal::is_app_installed('cambios') || empty($id_usuario) || $quien !== 'usuario') {
            return [
                'error' => (string)_("No tiene permiso"),
                'a_valores' => $a_valores,
                'nombre_usuario' => $nombre_usuario,
            ];
        }

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oUsuario = $UsuarioRepository->findById($id_usuario);
        $nombre_usuario = $oUsuario->getUsuarioAsString();

        $a_status = StatusId::getArrayStatus();

        $CambiosUsuarioPropiedadesPrefRepository = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);
        $CambiosUsuariosObjetoRepository = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $aWhere = ['id_usuario' => $id_usuario, '_ordre' => 'objeto, dl_org, id_tipo_activ_txt'];
        $cListaTablas = $CambiosUsuariosObjetoRepository->getCambioUsuarioObjetoPrefs($aWhere, []);

        $aTipos_aviso = AvisoTipoId::getArrayAvisoTipo();
        $aObjetos = AvisoObjetoCatalog::getArrayObjetosPosibles();

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);

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

            $isfsv = substr((string)$id_tipo, 0, 1);
            $mi_dele = ConfigGlobal::mi_delef((int)$isfsv);
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
                $oActividadFase = $ActividadFaseRepository->findById($id_fase_ref);
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

            $cListaPropiedades = $CambiosUsuarioPropiedadesPrefRepository->getCambioUsuarioPropiedadPrefs(
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
        ];
    }
}
