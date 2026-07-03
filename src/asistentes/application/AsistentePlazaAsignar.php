<?php

namespace src\asistentes\application;

use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Cambia la plaza asignada de un lote de asistentes (columna `plaza`).
 *
 * Sustituye al case `plaza` del antiguo `apps/asistentes/controller/update_3101.php`,
 * que recibia un `lista_json` con los asistentes seleccionados y un `plaza` comun.
 */
final class AsistentePlazaAsignar
{
    public function __construct(
        private AsistenteApplicationService $asistenteApplicationService,
        private PlazaPropietarioAsignacionInterface $plazaPropietarioAsignacion,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        if ($id_activ === 0) {
            return _("falta id_activ");
        }
        $plaza = $input['plaza'] ?? null;
        $lista_json = FuncTablasSupport::inputString($input, 'lista_json');
        $arr = json_decode($lista_json);
        if (!is_array($arr) || empty($arr)) {
            return _("falta lista de seleccion");
        }

        $asistenteAppService = $this->asistenteApplicationService;
        $msg_err = '';
        foreach ($arr as $obj) {
            if (!is_object($obj)) {
                continue;
            }
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
                $err_plaza = $oAsistente->setPlazaComprobando(is_numeric($plaza) ? (int) $plaza : 0, $this->plazaPropietarioAsignacion);
                if ($err_plaza !== '') {
                    $msg_err .= $err_plaza . "\n";
                    continue;
                }
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
