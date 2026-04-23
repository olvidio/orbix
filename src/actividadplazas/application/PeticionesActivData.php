<?php

namespace src\actividadplazas\application;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Data builder de la pantalla `peticiones_activ`: lista de actividades
 * candidatas + peticiones actuales para la persona + tipo.
 *
 * Sucesor (parcial) de `apps/actividadplazas/controller/peticiones_activ.php`.
 * Limpia del repo las peticiones antiguas que ya no esten en la lista
 * (mismo comportamiento que el legacy).
 */
final class PeticionesActivData
{
    /**
     * @return array{
     *     id_nom: int,
     *     ap_nom: string,
     *     na: string,
     *     sactividad: string,
     *     sid_activ: string,
     *     opciones: array<int|string,string>,
     *     tipo: string
     * }
     */
    public static function execute(array $input): array
    {
        $id_nom = (int)($input['id_nom'] ?? 0);
        $na = (string)($input['na'] ?? '');
        $sactividad = (string)($input['sactividad'] ?? '');
        $todos = (int)($input['todos'] ?? 0);
        $id_ctr_agd = (int)($input['id_ctr_agd'] ?? 0);
        $id_ctr_n = (int)($input['id_ctr_n'] ?? 0);

        if (($na === 'a' || $na === 'agd') && $sactividad === 'ca') {
            $sactividad = 'cv';
        }

        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        $oPersona = $PersonaDlRepository->findById($id_nom);
        $ap_nom = is_object($oPersona) ? (string)$oPersona->getPrefApellidosNombre() : '';

        $aWhere = [];
        $aOperador = [];

        if ($todos !== 0 && $todos !== 1) {
            $grupo_estudios = $todos;
            $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
            $cDelegaciones = $DelegacionRepository->getDelegaciones(['grupo_estudios' => $grupo_estudios]);
            if (count($cDelegaciones) > 1) {
                $aOperador['dl_org'] = 'OR';
            }
            $mi_grupo = '';
            foreach ($cDelegaciones as $oDelegacion) {
                $mi_grupo .= $mi_grupo === '' ? '' : ',';
                $mi_grupo .= "'" . $oDelegacion->getDlVo()->value . "'";
            }
            if ($mi_grupo !== '') {
                $aWhere['dl_org'] = $mi_grupo;
            }
        }

        // Periodo del curso.
        $inicurs = '';
        $fincurs = '';
        switch ($sactividad) {
            case 'ca':
            case 'cv':
                $any = $_SESSION['oConfig']->any_final_curs('est');
                $inicurs = \core\curso_est('inicio', $any, 'est')->format('Y-m-d');
                $fincurs = \core\curso_est('fin', $any, 'est')->format('Y-m-d');
                break;
            case 'crt':
                $any = $_SESSION['oConfig']->any_final_curs('crt');
                $inicurs = \core\curso_est('inicio', $any, 'crt')->format('Y-m-d');
                $fincurs = \core\curso_est('fin', $any, 'crt')->format('Y-m-d');
                break;
        }
        $aWhere['f_ini'] = "'$inicurs','$fincurs'";
        $aOperador['f_ini'] = 'BETWEEN';
        $aWhere['status'] = StatusId::ACTUAL;
        $aWhere['_ordre'] = 'f_ini,nivel_stgr';

        $sfsv = ConfigGlobal::mi_sfsv();
        $mi_dele = ConfigGlobal::mi_delef();
        $cActividades = [];

        $Qid_tipo_activ = '';
        switch ($na) {
            case 'agd':
            case 'a':
                switch ($sactividad) {
                    case 'ca':
                    case 'cv':
                        $Qid_tipo_activ = '^' . $sfsv . '33';
                        break;
                    case 'crt':
                        $Qid_tipo_activ = '^' . $sfsv . '31';
                        break;
                }
                $aWhere['id_tipo_activ'] = $Qid_tipo_activ;
                $aOperador['id_tipo_activ'] = '~';

                $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
                $cActividadesDl = $ActividadDlRepository->getActividades($aWhere, $aOperador);
                // Evitar duplicar las de midele al sacar las publicadas.
                $aWhere['dl_org'] = $mi_dele;
                $aOperador['dl_org'] = '!=';
                $ActividadPubRepository = $GLOBALS['container']->get(ActividadPubRepositoryInterface::class);
                $cActividadesPub = $ActividadPubRepository->getActividades($aWhere, $aOperador);
                $cActividades = array_merge($cActividadesDl, ['-------'], $cActividadesPub);
                break;
            case 'n':
                switch ($sactividad) {
                    case 'ca':
                        $Qid_tipo_activ = '^' . $sfsv . '12';
                        break;
                    case 'crt':
                        $Qid_tipo_activ = '^' . $sfsv . '11';
                        break;
                }
                $aWhere['id_tipo_activ'] = $Qid_tipo_activ;
                $aOperador['id_tipo_activ'] = '~';

                $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
                $cActividadesDl = $ActividadDlRepository->getActividades($aWhere, $aOperador);
                $aWhere['dl_org'] = $mi_dele;
                $aOperador['dl_org'] = '!=';
                $ActividadPubRepository = $GLOBALS['container']->get(ActividadPubRepositoryInterface::class);
                $cActividadesPub = $ActividadPubRepository->getActividades($aWhere, $aOperador);
                $cActividades = array_merge($cActividadesDl, ['-------'], $cActividadesPub);
                break;
        }

        $aOpciones = [];
        $a_IdActividades = [];
        foreach ($cActividades as $oActividad) {
            if (is_object($oActividad)) {
                $id_activ = $oActividad->getId_activ();
                $nom_activ = $oActividad->getNom_activ();
                $aOpciones[$id_activ] = $nom_activ;
                $a_IdActividades[] = $id_activ;
            } else {
                $aOpciones[1] = '--------';
            }
        }

        // Peticiones actuales: borrar las que ya no esten en la lista.
        $PlazaPeticionRepository = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
        $cPlazasPeticion = $PlazaPeticionRepository->getPlazasPeticion([
            'id_nom' => $id_nom,
            'tipo' => $sactividad,
            '_ordre' => 'orden',
        ]);
        $sid_activ = '';
        foreach ($cPlazasPeticion as $key => $oPlazaPeticion) {
            $id_activ_pet = $oPlazaPeticion->getId_activ();
            if (!in_array($id_activ_pet, $a_IdActividades)) {
                unset($cPlazasPeticion[$key]);
                $PlazaPeticionRepository->Eliminar($oPlazaPeticion);
                continue;
            }
            $sid_activ .= $sid_activ === '' ? $id_activ_pet : ',' . $id_activ_pet;
        }

        return [
            'id_nom' => $id_nom,
            'ap_nom' => $ap_nom,
            'na' => $na,
            'sactividad' => $sactividad,
            'sid_activ' => $sid_activ,
            'opciones' => $aOpciones,
            'tipo' => $sactividad,
        ];
    }
}
