<?php

namespace src\procesos\application;

use frontend\actividades\helpers\ActividadTipo;
use src\permisos\domain\PermisosActividades;
use src\actividades\domain\entity\TiposActividades;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\procesos\domain\PermAccionBits;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use function src\shared\domain\helpers\is_true;

/**
 * Caso de uso: datos para la pantalla usuario_perm_activ (alta/edicion
 * de permisos de actividad para un usuario).
 *
 * Agrupa la resolucion de repositorios para que el controlador frontend
 * no acceda directamente al contenedor ni a `use src\...`. El frontend
 * recibe arrays serializables y construye los `frontend\shared\web\Desplegable`.
 */
class UsuarioPermActivData
{
    /**
     * @return array{
     *     nombre:string,
     *     dl_propia:string,
     *     perm_jefe:bool,
     *     tipo_actividad_html:string,
     *     a_fases:array<string,string>,
     *     a_acciones:array<string,int>,
     *     a_afecta_a:array<string,int>,
     *     aPerm:array<int,array{afecta_a:string,num:int,fase_ref:string,perm_on:string,perm_off:string,marcado:bool}>
     * }
     */
    public static function execute(array $input): array
    {
        $Qid_usuario = (int)($input['id_usuario'] ?? 0);
        $Qid_tipo_activ_txt = (string)($input['id_tipo_activ_txt'] ?? '');
        $Qdl_propia = is_true($input['dl_propia'] ?? '') ? 't' : 'f';
        if (empty($Qid_tipo_activ_txt)) {
            $Qdl_propia = 't';
        }

        $oTipoActiv = new TiposActividades($Qid_tipo_activ_txt, true);
        $id_tipo_activ = $oTipoActiv->getId_tipo_activ();

        $GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
        $oUsuario = $GrupoRepository->findById($Qid_usuario);
        $nombre = $oUsuario?->getUsuario() ?? '';

        $a_acciones = PermAccionBits::valueToLabel();
        $a_afecta_a = PermisosActividades::AFECTA;
        asort($a_afecta_a);

        $perm_jefe = false;
        if ($_SESSION['oConfig']->is_jefeCalendario()
            || (($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) && (int)$_SESSION['session_auth']['sfsv'] === 1)
            || ($_SESSION['oPerm']->have_perm_oficina('calendario'))
        ) {
            $perm_jefe = true;
        }

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $Qdl_propia);

        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $a_fases = $ActividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);

        $PermUsuarioActividadRepository = $GLOBALS['container']->get(PermUsuarioActividadRepositoryInterface::class);
        $aPerm = [];
        foreach ($a_afecta_a as $afecta_a_txt => $num) {
            $aWhere = [
                'id_usuario' => $Qid_usuario,
                'dl_propia' => $Qdl_propia,
                'id_tipo_activ_txt' => $Qid_tipo_activ_txt,
                'afecta_a' => $num,
            ];
            $fase_ref = '';
            $perm_on = '';
            $perm_off = '';
            $afecta_a_match = '';
            $cPermUsuarioActividad = $PermUsuarioActividadRepository->getPermUsuarioActividades($aWhere);
            foreach ($cPermUsuarioActividad as $oPermiso) {
                $fase_ref = (string)$oPermiso->getFase_ref();
                $afecta_a_match = (string)$oPermiso->getAfecta_a();
                $perm_on = (string)$oPermiso->getPerm_on();
                $perm_off = (string)$oPermiso->getPerm_off();
            }

            $aPerm[] = [
                'afecta_a' => $afecta_a_txt,
                'num' => $num,
                'fase_ref' => $fase_ref,
                'perm_on' => $perm_on,
                'perm_off' => $perm_off,
                'marcado' => ($afecta_a_match === (string)$num),
            ];
        }

        $oAt = new ActividadTipo();
        if ($id_tipo_activ !== '' && $id_tipo_activ !== '0') {
            $oAt->setId_tipo_activ($id_tipo_activ);
        }
        $oAt->setAsistentes($oTipoActiv->getAsistentesText());
        $oAt->setActividad($oTipoActiv->getActividadText());
        $oAt->setNom_tipo($oTipoActiv->getNom_tipoText());
        $oAt->setPara('procesos');
        $oAt->setPerm_jefe($perm_jefe);
        $tipo_actividad_html = $oAt->captureHtml(true);

        return [
            'nombre' => $nombre,
            'dl_propia' => $Qdl_propia,
            'perm_jefe' => $perm_jefe,
            'tipo_actividad_html' => $tipo_actividad_html,
            'a_fases' => $a_fases,
            'a_acciones' => $a_acciones,
            'a_afecta_a' => $a_afecta_a,
            'aPerm' => $aPerm,
        ];
    }
}
