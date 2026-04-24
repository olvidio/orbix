<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use web\TiposActividades;

/**
 * Incorpora la primera peticion de plaza de cada numerario/agregado
 * como asistencia con plaza 'asignada' (si la actividad es de midele)
 * o 'pedida' (si es de otra dl).
 * No incorpora a personas que ya tienen una asistencia marcada como
 * propia en una actividad del listado.
 *
 * Sucesor de `apps/actividadplazas/controller/incorporar_peticion.php`.
 */
final class PeticionesIncorporar
{
    /**
     * @return array{incorporadas:int, mensaje_final:string, error:string}
     */
    public static function execute(array $input): array
    {
        $sactividad = (string)($input['sactividad'] ?? '');
        $sasistentes = (string)($input['sasistentes'] ?? '');

        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $ssfsv = $mi_sfsv === 2 ? 'sf' : 'sv';

        $oTipoActiv = new TiposActividades();
        $oTipoActiv->setSfsvText($ssfsv);
        $oTipoActiv->setAsistentesText($sasistentes);
        $oTipoActiv->setActividadText($sactividad);
        $id_tipo_activ = '^' . $oTipoActiv->getId_tipo_activ();
        $id_tipo_activ_sup = '';
        if ($sasistentes === 'n') {
            $oTipoActiv->setAsistentesText('agd');
            $id_tipo_activ_sup = '^' . $oTipoActiv->getId_tipo_activ();
        }

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

        $mi_dele = ConfigGlobal::mi_delef();
        $aWhereA = [
            'status' => StatusId::ACTUAL,
            'f_ini' => "'$inicurs','$fincurs'",
        ];
        $aOperadorA = ['f_ini' => 'BETWEEN'];
        $cActividades = [];
        $filtro_id_nom = 0;

        switch ($sasistentes) {
            case 'agd':
            case 'a':
                $aWhereA['id_tipo_activ'] = $id_tipo_activ;
                $aOperadorA['id_tipo_activ'] = '~';
                $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
                $cActividadesDl = $ActividadDlRepository->getActividades($aWhereA, $aOperadorA);
                $aWhereA['dl_org'] = $mi_dele;
                $aOperadorA['dl_org'] = '!=';
                $ActividadPubRepository = $GLOBALS['container']->get(ActividadPubRepositoryInterface::class);
                $cActividadesPub = $ActividadPubRepository->getActividades($aWhereA, $aOperadorA);
                $cActividades = array_merge($cActividadesDl, $cActividadesPub);
                $filtro_id_nom = 2;
                break;
            case 'n':
                $aWhereA['id_tipo_activ'] = $id_tipo_activ;
                $aOperadorA['id_tipo_activ'] = '~';
                $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
                $cActividades1 = $ActividadRepository->getActividades($aWhereA, $aOperadorA);
                if ($id_tipo_activ_sup !== '') {
                    $aWhereA['id_tipo_activ'] = $id_tipo_activ_sup;
                    $aOperadorA['id_tipo_activ'] = '~';
                    $cActividades_sup = $ActividadRepository->getActividades($aWhereA, $aOperadorA);
                    $cActividades = array_merge($cActividades1, $cActividades_sup);
                } else {
                    $cActividades = $cActividades1;
                }
                $filtro_id_nom = 1;
                break;
        }

        $aId_activ = [];
        foreach ($cActividades as $oActividad) {
            $aId_activ[$oActividad->getId_activ()] = $oActividad->getDl_org();
        }

        $PlazaPeticionRepository = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
        $aWhereP = [
            'orden' => 1,
            'tipo' => $sactividad,
            'id_nom' => '^\d{4}' . $filtro_id_nom,
        ];
        $aOperadorP = ['id_nom' => '~'];
        $cPlazasPeticion = $PlazaPeticionRepository->getPlazasPeticion($aWhereP, $aOperadorP);

        $incorporadas = 0;
        $msg_err = '';
        foreach ($cPlazasPeticion as $oPlazaPeticion) {
            $id_nom = $oPlazaPeticion->getId_nom();
            $id_activ_new = $oPlazaPeticion->getId_activ();
            if (!array_key_exists($id_activ_new, $aId_activ)) {
                continue;
            }
            if (self::tieneAsistencia($id_nom, $aId_activ)) {
                continue;
            }
            $dl_org = $aId_activ[$id_activ_new];
            $dl = preg_replace('/f$/', '', $dl_org);
            if ($dl === $mi_dele) {
                $AsistenteRepository = $GLOBALS['container']->get(AsistenteDlRepositoryInterface::class);
            } else {
                $AsistenteRepository = $GLOBALS['container']->get(AsistenteOutRepositoryInterface::class);
            }
            $oAsistenteNew = new Asistente();
            $oAsistenteNew->setId_activ($id_activ_new);
            $oAsistenteNew->setId_nom($id_nom);
            $oAsistenteNew->setPropio(true);
            $oAsistenteNew->setPlazaComprobando(PlazaId::ASIGNADA);
            $oAsistenteNew->setPropietarioVo("$dl>$mi_dele");
            $oAsistenteNew->setDl_responsable($mi_dele);
            if ($AsistenteRepository->Guardar($oAsistenteNew) === false) {
                $msg_err = (string)_("hay un error, no se ha guardado");
            } else {
                $incorporadas++;
            }
        }

        $mensaje_final = sprintf(
            (string)_("no se incorporán las peticiones si la persona ya tiene una actividad como propia en el periodo: %s - %s."),
            $inicurs,
            $fincurs
        );

        return [
            'incorporadas' => $incorporadas,
            'mensaje_final' => $mensaje_final,
            'error' => $msg_err,
        ];
    }

    /**
     * @param int $id_nom
     * @param array<int,string> $aId_activ id_activ => dl_org
     */
    private static function tieneAsistencia(int $id_nom, array $aId_activ): bool
    {
        $AsistenteRepository = $GLOBALS['container']->get(AsistenteDlRepositoryInterface::class);
        $cAsistentes = $AsistenteRepository->getAsistentes(['id_nom' => $id_nom, 'propio' => 't']);
        foreach ($cAsistentes as $oAsistente) {
            if (array_key_exists($oAsistente->getId_activ(), $aId_activ)) {
                return true;
            }
        }
        $AsistentesOutRepository = $GLOBALS['container']->get(AsistenteOutRepositoryInterface::class);
        $cAsistentesOut = $AsistentesOutRepository->getAsistentesOut(['id_nom' => $id_nom, 'propio' => 't']);
        foreach ($cAsistentesOut as $oAsistente) {
            if (array_key_exists($oAsistente->getId_activ(), $aId_activ)) {
                return true;
            }
        }
        return false;
    }
}
