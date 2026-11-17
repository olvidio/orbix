<?php

namespace src\ubis\domain\contracts;

/**
 * Plano (fichero binario) guardado en la fila de una dirección. Lo implementan los seis
 * repositorios de direcciones con {@see PlanoOperationsTrait}.
 */
interface PlanoRepositoryInterface
{
    /**
     * @return array<string, mixed> `plano_nom`, `plano_extension`, `plano_doc` (string, recurso o null)
     */
    public function planoDownload(int $id_direccion): array;

    /**
     * @param resource|string|null $fichero
     */
    public function planoUpload(int $id_direccion, ?string $nom, ?string $extension, $fichero): void;

    public function planoBorrar(int $id_direccion): void;
}
