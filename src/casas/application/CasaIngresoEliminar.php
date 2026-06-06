<?php

namespace src\casas\application;

use src\casas\domain\contracts\IngresoRepositoryInterface;

/**
 * Use case: eliminar el Ingreso asociado a una actividad.
 *
 * Sucesor de la rama `que=eliminar` de
 * `apps/casas/controller/casa_ajax.php`.
 */
final class CasaIngresoEliminar
{
    public function __construct(
        private IngresoRepositoryInterface $ingresoRepository,
    ) {
    }

    /**
     * @param array{id_activ?: int|string} $input
     * @return array{ok: bool, mensaje: string, data: string}
     */
    public function execute(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        if ($id_activ === 0) {
            return ['ok' => false, 'mensaje' => (string)_("no sé cuál he de borar"), 'data' => ''];
        }
        $oIngreso = $this->ingresoRepository->findById($id_activ);
        if ($oIngreso === null) {
            return ['ok' => false, 'mensaje' => (string)_("Ingreso no encontrado"), 'data' => ''];
        }
        if ($this->ingresoRepository->Eliminar($oIngreso) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha eliminado"), 'data' => ''];
        }
        return ['ok' => true, 'mensaje' => '', 'data' => ''];
    }
}
