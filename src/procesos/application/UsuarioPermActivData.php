<?php

namespace src\procesos\application;

use frontend\actividades\helpers\ActividadTipo;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XPermisos;
use src\actividades\domain\entity\TiposActividades;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\procesos\domain\PermAccionBits;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;

/**
 * Caso de uso: datos para la pantalla usuario_perm_activ.
 */
class UsuarioPermActivData
{
    public function __construct(
        private readonly GrupoRepositoryInterface $grupoRepository,
        private readonly TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private readonly ActividadFaseRepositoryInterface $actividadFaseRepository,
        private readonly PermUsuarioActividadRepositoryInterface $permUsuarioActividadRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $Qid_usuario = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_usuario');
        $Qid_tipo_activ_txt = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tipo_activ_txt');
        $Qdl_propia = \src\shared\domain\helpers\FuncTablasSupport::isTrue($input['dl_propia'] ?? '') ? 't' : 'f';
        if ($Qid_tipo_activ_txt === '') {
            $Qdl_propia = 't';
        }

        $oTipoActiv = new TiposActividades($Qid_tipo_activ_txt, true);
        $id_tipo_activ = $oTipoActiv->getId_tipo_activ();

        $oUsuario = $this->grupoRepository->findById($Qid_usuario);
        $nombre = $oUsuario?->getUsuarioAsString() ?? '';

        $a_acciones = PermAccionBits::valueToLabel();
        $a_afecta_a = PermisosActividades::AFECTA;
        asort($a_afecta_a);

        $perm_jefe = false;
        $oConfig = $_SESSION['oConfig'] ?? null;
        $oPerm = $_SESSION['oPerm'] ?? null;
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        $miSfsv = 0;
        if (is_array($sessionAuth) && isset($sessionAuth['sfsv']) && is_numeric($sessionAuth['sfsv'])) {
            $miSfsv = (int) $sessionAuth['sfsv'];
        }
        if ((is_object($oConfig) && method_exists($oConfig, 'is_jefeCalendario') && $oConfig->is_jefeCalendario())
            || ($oPerm instanceof XPermisos
                && ($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd'))
                && $miSfsv === 1)
            || ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('calendario'))
        ) {
            $perm_jefe = true;
        }

        $aTiposDeProcesos = $this->tipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, \src\shared\domain\helpers\FuncTablasSupport::isTrue($Qdl_propia) ?? false);
        $a_fases = $this->actividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);

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
            $cPermUsuarioActividad = $this->permUsuarioActividadRepository->getPermUsuarioActividades($aWhere);
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
        $oAt->setQue('buscar');
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
