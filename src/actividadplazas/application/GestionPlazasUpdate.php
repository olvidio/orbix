<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Mutacion de celda de la tabla editable de `gestion_plazas` (y
 * tambien de `plazas_balance_dl`).
 *
 * Devuelve string vacio si ha ido bien, o mensaje de error. El
 * controlador HTTP lo envuelve con `frontend\shared\web\ContestarJson::enviar(...)`
 * en el contrato JSON estandar `{success, mensaje, data}`. El widget
 * `frontend\shared\web\TablaEditable` consume esa respuesta via `dataType: 'json'`.
 *
 * Sucesor de la rama `update` del dispatcher legacy
 * `apps/actividadplazas/controller/gestion_plazas_ajax.php`.
 */
final class GestionPlazasUpdate
{
    /**
     * Campos POST enviados por el form interno de `TablaEditable`:
     *  - `data`    (JSON) fila editada, con claves `id`, `dlorg`,
     *              `tot`, `<dl>-c`, `<dl>-p`, …
     *  - `colName` (JSON) nombre de la columna modificada (`tot`,
     *              `<dl>-c`, `<dl>-p`, `<dl>-l`).
     */
    public static function execute(array $input): string
    {
        $dataRaw = (string)($input['data'] ?? '');
        $colNameRaw = (string)($input['colName'] ?? '');
        if ($dataRaw === '' || $colNameRaw === '') {
            return '';
        }
        $obj = json_decode($dataRaw);
        $dl = json_decode($colNameRaw);
        if (!is_object($obj) || $dl === null) {
            return '';
        }

        $id_activ = (int)($obj->id ?? 0);
        $dl_org = (string)($obj->dlorg ?? '');
        $plazas = (int)($obj->$dl ?? 0);
        if ($id_activ === 0) {
            return '';
        }

        $mi_dele = ConfigGlobal::mi_delef();

        // Plazas totales de la actividad (editable solo si la actividad es
        // de mi dl).
        if ($dl === 'tot' && $mi_dele === $dl_org) {
            $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
            $oActividadDl = $ActividadDlRepository->findById($id_activ);
            if ($oActividadDl === null) {
                return (string)_("no se encuentra la actividad");
            }
            $oActividadDl->setPlazas($plazas);
            if ($ActividadDlRepository->Guardar($oActividadDl) === false) {
                $err = (string)_("hay un error, no se ha guardado");
                return $err . "\n" . $ActividadDlRepository->getErrorTxt();
            }
            return '';
        }

        // Resto de columnas: `<dl>-c` o `<dl>-p` (concedidas / pedidas).
        $dl_sigla = substr((string)$dl, 0, -2);
        if (ConfigGlobal::mi_sfsv() === 2) {
            // para sf quitar la `f` final
            $dl_sigla = substr($dl_sigla, 0, -1);
        }
        $id_dl = 0;
        $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $dl_sigla]);
        if (is_array($cDelegaciones) && count($cDelegaciones) > 0) {
            $id_dl = $cDelegaciones[0]->getIdDlVo()->value();
        }
        $ActividadPlazasDlRepository = $GLOBALS['container']->get(ActividadPlazasDlRepositoryInterface::class);
        $cActividadPlazasDl = $ActividadPlazasDlRepository->getActividadesPlazas([
            'id_activ' => $id_activ,
            'id_dl' => $id_dl,
            'dl_tabla' => $mi_dele,
        ]);
        if (empty($cActividadPlazasDl)) {
            return (string)_("Todavía no se han asignado las plazas por calendario");
        }
        $oActividadPlazasDl = $cActividadPlazasDl[0];
        $oActividadPlazasDl->setPlazas($plazas);
        if ($ActividadPlazasDlRepository->Guardar($oActividadPlazasDl) === false) {
            $err = (string)_("hay un error, no se ha guardado");
            return $err . "\n" . $ActividadPlazasDlRepository->getErrorTxt();
        }
        return '';
    }
}
