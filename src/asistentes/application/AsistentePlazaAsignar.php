<?php

namespace src\asistentes\application;

use src\asistentes\application\services\AsistenteApplicationService;

/**
 * Cambia la plaza asignada de un lote de asistentes (columna `plaza`).
 *
 * Sustituye al case `plaza` del antiguo `apps/asistentes/controller/update_3101.php`,
 * que recibia un `lista_json` con los asistentes seleccionados y un `plaza` comun.
 */
final class AsistentePlazaAsignar
{
    public static function execute(array $input): string
    {
        $id_activ = (int) ($input['id_activ'] ?? 0);
        if ($id_activ === 0) {
            return _("falta id_activ");
        }
        $plaza = $input['plaza'] ?? null;
        $lista_json = (string) ($input['lista_json'] ?? '');
        $arr = json_decode($lista_json);
        if (!is_array($arr) || empty($arr)) {
            return _("falta lista de seleccion");
        }

        $asistenteAppService = $GLOBALS['container']->get(AsistenteApplicationService::class);
        $msg_err = '';
        foreach ($arr as $obj) {
            $raw = $obj->value ?? '';
            $id_nom = (int) strtok((string) $raw, '#');
            if ($id_nom === 0) {
                continue;
            }
            $oAsistente = $asistenteAppService->findById($id_activ, $id_nom);
            if ($oAsistente === null) {
                $msg_err .= sprintf(_("no se encuentra el asistente (id_nom: %s, id_activ: %s)"), $id_nom, $id_activ) . "\n";
                continue;
            }
            if ($oAsistente->perm_modificar() === false) {
                $msg_err .= _("los datos de asistencia los modifica la dl del asistente") . "\n";
                continue;
            }
            if ($plaza !== null && $plaza !== '') {
                $oAsistente->setPlazaComprobando((int) $plaza);
            } else {
                $oAsistente->setPlaza(null);
            }
            if ($asistenteAppService->guardar($oAsistente) === false) {
                $msg_err .= _("hay un error, no se ha guardado") . "\n";
            }
        }
        return trim($msg_err);
    }
}
