<?php

namespace src\actividades\application;

use src\shared\security\HashB;

/**
 * Datos del formulario de alta de un tipo de actividad: la cápsula que abre `tipo_activ_nuevo`. El formulario lo
 * monta `frontend/actividades/controller/tipo_activ_form_nuevo.php`.
 */
final class TipoActivFormNuevoData
{
    /**
     * @return array{ctx_nuevo: string}
     */
    public function execute(): array
    {
        return ['ctx_nuevo' => HashB::sign('tipo_activ_nuevo')];
    }
}
