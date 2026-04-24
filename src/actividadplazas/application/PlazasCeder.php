<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Actualiza el array `cedidas` de `ActividadPlazasDl` para ceder
 * (o quitar) plazas de `mi_dele` a otra dl en una actividad.
 *
 * Sucesor de la rama `ceder` del dispatcher legacy
 * `apps/actividadplazas/controller/resumen_plazas_update.php`.
 */
final class PlazasCeder
{
    public static function execute(array $input): string
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $num_plazas = (int)($input['num_plazas'] ?? 0);
        $reg_dl = (string)($input['region_dl'] ?? '');

        if ($id_activ <= 0 || $reg_dl === '') {
            return (string)_("faltan parametros id_activ / region_dl");
        }

        $dl = substr($reg_dl, strpos($reg_dl, '-') + 1);

        $mi_dele = ConfigGlobal::mi_delef();
        $dl_sigla = ConfigGlobal::mi_sfsv() === 2 ? substr($mi_dele, 0, -1) : $mi_dele;

        $id_dl = 0;
        $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $dl_sigla]);
        if (is_array($cDelegaciones) && count($cDelegaciones) > 0) {
            $id_dl = (int)($cDelegaciones[0]->getIdDlVo()->value() ?? 0);
        }

        $ActvidadPlazasDlRepository = $GLOBALS['container']->get(ActividadPlazasDlRepositoryInterface::class);
        $cActividadPlazasDl = $ActvidadPlazasDlRepository->getActividadesPlazas([
            'id_activ' => $id_activ,
            'id_dl' => $id_dl,
            'dl_tabla' => $mi_dele,
        ]);
        if (empty($cActividadPlazasDl)) {
            return (string)_("Todavía no se han asignado las plazas por calendario");
        }
        $oActividadPlazasDl = $cActividadPlazasDl[0];

        $aCedidas = $oActividadPlazasDl->getArrayCedidas() ?? [];
        if (!is_array($aCedidas)) {
            $aCedidas = [];
        }
        if ($num_plazas === 0) {
            if (isset($aCedidas[$dl])) {
                unset($aCedidas[$dl]);
            }
        } else {
            $aCedidas[$dl] = $num_plazas;
        }
        $oActividadPlazasDl->setCedidas($aCedidas);

        if ($ActvidadPlazasDlRepository->Guardar($oActividadPlazasDl) === false) {
            return (string)_("hay un error, no se ha guardado")
                . "\n" . $ActvidadPlazasDlRepository->getErrorTxt();
        }
        return '';
    }
}
