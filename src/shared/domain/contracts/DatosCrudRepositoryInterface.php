<?php

declare(strict_types=1);

namespace src\shared\domain\contracts;

/**
 * Operaciones CRUD comunes de repositorios usados por DatosUpdateRepo.
 */
interface DatosCrudRepositoryInterface extends DatosLookupRepositoryInterface
{
    public function Eliminar(object $entity): bool;

    public function Guardar(object $entity): bool;

    public function getErrorTxt(): string;

    /**
     * @return int|string
     */
    public function getNewId();
}
