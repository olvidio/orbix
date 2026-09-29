<?php

namespace src\inventario\application;

use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\shared\security\HashB;

/**
 * Opciones del desplegable de tipos de documento (`lista_tipo_doc.php`).
 */
final class TipoDocOpcionesData
{
    public function __construct(
        private TipoDocRepositoryInterface $tipoDocRepository,
    ) {
    }

    /**
     * @return array{a_opciones: array<int|string, mixed>, ctx_documentos_guardar: string}
     */
    public function execute(): array
    {
        return [
            'a_opciones' => $this->tipoDocRepository->getArrayTipoDoc(),
            'ctx_documentos_guardar' => HashB::sign('documentos_guardar'),
        ];
    }
}
