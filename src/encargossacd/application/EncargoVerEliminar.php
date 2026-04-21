<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;

/**
 * Borrado desde lista `encargo_select` (antes `encargo_ajax.php` que=eliminar).
 */
final class EncargoVerEliminar
{
    /**
     * @param array<string, mixed> $input
     * @return array{error: string}
     */
    public static function execute(array $input): array
    {
        $sel = $input['sel'] ?? null;
        if (!is_array($sel) || $sel === []) {
            return ['error' => ''];
        }
        $id_enc = (int)explode('#', (string)$sel[0], 2)[0];

        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $oEncargo = $EncargoRepository->findById($id_enc);
        if ($oEncargo === null) {
            return ['error' => sprintf(_('No se encuentra el encargo %d'), $id_enc)];
        }
        if ($EncargoRepository->Eliminar($oEncargo) === false) {
            return ['error' => _('hay un error, no se ha eliminado') . "\n" . $EncargoRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
