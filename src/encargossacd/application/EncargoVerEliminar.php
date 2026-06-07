<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;

/**
 * Borrado desde lista `encargo_select` (antes `encargo_ajax.php` que=eliminar).
 */
final class EncargoVerEliminar
{

    public function __construct(
        private EncargoRepositoryInterface $encargoRepository
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error: string}
     */
    public function execute(array $input): array
    {
        $sel = $input['sel'] ?? null;
        if (!is_array($sel) || $sel === []) {
            return ['error' => ''];
        }
        $first = $sel[0] ?? '';
        $token = is_string($first) ? $first : (is_scalar($first) ? (string) $first : '');
        $id_enc = (int) explode('#', $token, 2)[0];

        $oEncargo = $this->encargoRepository->findById($id_enc);
        if ($oEncargo === null) {
            return ['error' => sprintf(_('No se encuentra el encargo %d'), $id_enc)];
        }
        if ($this->encargoRepository->Eliminar($oEncargo) === false) {
            return ['error' => _('hay un error, no se ha eliminado') . "\n" . $this->encargoRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
