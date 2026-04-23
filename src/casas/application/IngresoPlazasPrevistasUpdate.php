<?php

namespace src\casas\application;

use src\casas\domain\contracts\IngresoRepositoryInterface;

/**
 * Mutación: actualiza `num_asistentes_previstos` de un `Ingreso`.
 *
 * Sucesor de la rama `update` del dispatcher legacy
 * `apps/casas/controller/prevision_asistentes_ajax.php`. Recibe los
 * campos `data` y `colName` codificados en JSON por `TablaEditable`.
 */
final class IngresoPlazasPrevistasUpdate
{
    public static function execute(array $input): string
    {
        $dataRaw = (string)($input['data'] ?? '');
        $colNameRaw = (string)($input['colName'] ?? '');

        $obj = json_decode($dataRaw);
        $colName = json_decode($colNameRaw);

        $id_activ = is_object($obj) ? (int)($obj->id ?? 0) : 0;
        $plazas = 0;
        if (is_object($obj) && is_string($colName) && $colName !== '') {
            $plazas = (int)($obj->$colName ?? 0);
        }

        if ($id_activ === 0) {
            return (string)_('no se encuentra el ingreso');
        }

        $repo = $GLOBALS['container']->get(IngresoRepositoryInterface::class);
        $oIngreso = $repo->findById($id_activ);
        if ($oIngreso === null) {
            return (string)_('no se encuentra el ingreso');
        }

        $oIngreso->setNum_asistentes_previstos($plazas);
        if ($repo->Guardar($oIngreso) === false) {
            return (string)_('Hay un error, no se ha guardado')
                . "\n" . $repo->getErrorTxt();
        }

        return '';
    }
}
