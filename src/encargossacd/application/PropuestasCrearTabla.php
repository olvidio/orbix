<?php

namespace src\encargossacd\application;

use src\encargossacd\db\DBPropuestas;

final class PropuestasCrearTabla
{
    /**
     * @return array{success: bool, mensaje?: string}
     */
    public function execute(): array
    {
        try {
            (new DBPropuestas())->createAll();
        } catch (\RuntimeException) {
            return ['success' => false, 'mensaje' => _('No se puede crear la tabla')];
        }

        return ['success' => true];
    }
}
